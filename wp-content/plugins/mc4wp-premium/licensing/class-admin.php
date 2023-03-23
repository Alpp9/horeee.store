<?php

namespace MC4WP\Licensing;

use Exception;

class Admin
{
    protected $option_name;
    protected $plugin_slug;
    protected $plugin_file;
    protected $plugin_version;
    protected $plugin_basename;
    protected $api_url;
    protected $network_activated = false;

    /**
     * @param string $plugin_slug
     * @param string $plugin_file
     * @param string $plugin_version
     * @param string $api_url
     */
    public function __construct($plugin_slug, $plugin_file, $plugin_version, $api_url)
    {
        $this->plugin_slug = $plugin_slug;
        $this->plugin_file = $plugin_file;
        $this->plugin_version = $plugin_version;
        $this->plugin_basename = plugin_basename($plugin_file);

        $this->api_url = $api_url;
        $this->option_name = $plugin_slug . '_license';

        if (is_multisite()) {
            if (! function_exists('is_plugin_active_for_network')) {
                require_once(ABSPATH . '/wp-admin/includes/plugin.php');
            }

            $this->network_activated = is_plugin_active_for_network($this->plugin_basename);
        }
    }

    public function add_hooks()
    {
        add_action('mc4wp_admin_save_license', array($this, 'process_form'));
        add_action('mc4wp_admin_before_other_settings', array($this, 'show_form'));
        add_action('mc4wp_license_check', array($this, 'check_license_status'));
        add_filter('pre_set_site_transient_update_plugins', array($this, 'add_plugin_update_data'));
        add_filter('plugins_api', array($this, 'get_plugin_info'), 20, 3);
        add_action('admin_notices', array($this, 'admin_notice'));
        add_action('after_plugin_row_' . $this->plugin_basename, array($this, 'after_plugin_row'), 10, 2);
        add_action('admin_init', array($this, 'run_upgrade_routine'));
    }

    private function get_api_client()
    {
        $license = $this->load_license();
        return new Client($this->api_url, $license->key);
    }

    private function flash_message($message, $type = 'success')
    {
        // @NOTE this should probably be dynamic (dependency inject?)
        mc4wp('admin.messages')->flash($message, $type);
    }

    /**
     * @return object
     */
    private function load_license()
    {
        $defaults = array( 'activated' => false, 'key' => '', 'token' => '' );
        $values = $this->network_activated ? get_site_option($this->option_name, array()) : get_option($this->option_name, array());
        $license = array_merge($defaults, (array) $values);
        return (object) $license;
    }

    /**
     * @param object $license
     */
    private function update_license($license)
    {
    	// cast object back to array
    	$license = (array) $license;
        $this->network_activated ? update_site_option($this->option_name, $license) : update_option($this->option_name, $license, false);
    }

    public function check_license_status()
    {
        $client = $this->get_api_client();
        $license = $this->load_license();

        // don't poll if license not currently activated
        if (!$license->activated) {
            return;
        }

        try {
            $remote_license = $client->request('GET', '/license');
            $license->activated = $remote_license->valid;
        } catch (ApiException $e) {
            if (in_array($e->getApiCode(), array('license_invalid', 'license_expired'), true)) {
                $license->activated = false;
                $license->token = '';
            }
        } catch (Exception $e) {
            // connection or parsing problem... uh oh
            // TODO: Write to debug log?
            return;
        }

        $this->update_license($license);
    }

    private function activate_license()
    {
        $client = $this->get_api_client();
        $license = $this->load_license();

        try {
            $data = $client->request('POST', '/license/activations', array( 'site_url' => get_option('home') ));
        } catch (ApiException $e) {
            $message = 'Error activating license: ' . $e->getApiMessage();
            $code = $e->getApiCode();
            if ($code === 'license_expired') {
                $message .= sprintf(' You can <a href="%s">renew your license</a> here.', 'https://my.mc4wp.com/licenses?key=' . $license->key);
            } else if ($code === 'license_at_limit') {
                $message .= sprintf(' <a href="%s">Manage your site activations or upgrade your license here</a>.', 'https://my.mc4wp.com/licenses?key=' . $license->key);
            }
            $this->flash_message($message, 'warning');
            return;
        } catch (Exception $e) {
            $this->flash_message('Error activating license: ' . $e->getMessage(), 'warning');
            return;
        }

        $license->token = $data->token;
        $license->activated = true;
        $this->update_license($license);

        $this->flash_message('Your license was successfully activated.');
    }

    private function deactivate_license()
    {
        $client = $this->get_api_client();
        $license = $this->load_license();

        try {
            $client->request('DELETE', '/license/activations/' . $license->token);
        } catch (ApiException $e) {
            $this->flash_message(sprintf('Error deactivating license: %s', $e->getApiMessage()), 'warning');
            return;
        } catch (Exception $e) {
            $this->flash_message('Error deactivating license: ' . $e->getMessage(), 'warning');
            return;
        }

        $license->token = '';
        $license->activated = false;
        $this->update_license($license);

        $this->flash_message('Your license was successfully deactivated.');
    }

    public function process_form()
    {
        $license_key = trim((string) $_POST['mc4wp_license_key']);
        $action = trim((string) $_POST['action']);
        $license = $this->load_license();

        if ($license->key !== $license_key) {
            // auto-activate license if license key was empty
            if ($license->key === '') {
                $action = 'activate';
            }

            $license->key = $license_key;
            $this->update_license($license);
        }

        switch ($action) {
        case 'deactivate':
            $this->deactivate_license();
            break;

        case 'activate':
            $this->activate_license();
            break;
        }

        // schedule daily license check, first run 12h from now
        if (! wp_next_scheduled('mc4wp_license_check')) {
        	$timestamp = time() + (3600 * 12);
            wp_schedule_event($timestamp, 'daily', 'mc4wp_license_check');
        };
    }

    /**
     * @return object
     */
    private function fetch_plugin()
    {
        static $data;

        // only run this function once
        if ($data !== null) {
			return $data;
		}

		$client = $this->get_api_client();
		try {
			$data = $client->request('GET', '/plugins/premium?format=wp');
		} catch (Exception $e) {
			$data = (object) array();
			return $data;
		}

		// NOTE: because json_decode turns associative arrays into objects, cast them back as arrays
		$data->sections = (array) $data->sections;
		$data->banners = (array) $data->banners;
		$data->contributors = (array) $data->contributors;
		foreach ($data->contributors as $key => $value) {
			$data->contributors[$key] = (array) $value;
		}

		$license = $this->load_license();
		if ($license->activated) {
			// add activation token to download URL's so that updates can be installed
			$data->package = add_query_arg(array( 'token' => $license->token ), $data->package);
			$data->download_link = $data->package;
		} else {
			// show warning that update can not be installed unless license is activated
			$data->sections['changelog'] = '<div class="notice notice-warning"><p>' . sprintf('You will need to <a href="%s">activate your plugin license</a> to install this update.', admin_url('admin.php?page=mailchimp-for-wp-other')) . '</p></div>' . $data->sections['changelog'];
			$data->upgrade_notice = 'You will need to activate your plugin license to install this update.';
			$data->package = '';
			$data->download_link = '';
		}

        return $data;
    }

    public function show_form()
    {
        $license = $this->load_license();
        require __DIR__ . '/views/license-form.php';
    }

    public function add_plugin_update_data($update_data)
    {
        // WP is funky sometimes, so make sure we're dealing with the right thing.
        if (empty($update_data) || ! isset($update_data->response)) {
            return $update_data;
        }

        $plugin_data = $this->fetch_plugin();
        if (! empty($plugin_data->new_version)) {
            if (version_compare($this->plugin_version, $plugin_data->new_version, '<')) {
                $plugin = plugin_basename($this->plugin_file);
                $plugin_data->plugin = $plugin;
                $update_data->response[ $plugin ] = $plugin_data;
            }
        }

        return $update_data;
    }

    public function get_plugin_info($data, $action = '', $args = null)
    {
        if ($action !== 'plugin_information') {
            return $data;
        }

        if ($args === null || $args->slug !== $this->plugin_slug) {
            return $data;
        }

        return $this->fetch_plugin();
    }

	public function admin_notice()
	{
		// only show admin notice on our own settings pages
		if (!isset($_GET['page']) || strpos($_GET['page'], 'mailchimp-for-wp') !== 0) {
			return;
		}

		// don't show on localhost
		if (stripos(get_home_url(), 'localhost') !== false || (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] === '127.0.0.1')) {
			return;
		}

		$license = $this->load_license();
		if ($license->activated) {
			return;
		}

		echo '<div class="notice notice-warning"><p>';
		echo sprintf('Please <a href="%s">activate your Mailchimp for WordPress Premium license</a>.', admin_url('admin.php?page=mailchimp-for-wp-other'));
		echo ' ' . sprintf('Need a license key? <a href="%s" target="_blank">Purchase one here</a>.', 'https://my.mc4wp.com/register');
		echo '</p></div>';
	}

    public function after_plugin_row()
    {
        $license = $this->load_license();
        if ($license->activated) {
            return;
        }

        echo '<style>.plugins .mc4wp-after-plugin-row th, .plugins .mc4wp-after-plugin-row td {  background-color: lightYellow; box-shadow: inset 0 -1px 0 rgba(0,0,0,0.1); }</style>';
        echo '<tr class="active mc4wp-after-plugin-row"><th scope="row" class="check-column"></th><td colspan="2">';
        echo sprintf('Please <a href="%s">activate your Mailchimp for WordPress Premium license</a>.', admin_url('admin.php?page=mailchimp-for-wp-other'));
        echo ' ' . sprintf('Need a license key? <a href="%s" target="_blank">Purchase one here</a>.', 'https://my.mc4wp.com/register');
        echo '</td>';
        echo '</tr>';
    }

    public function run_upgrade_routine()
    {
        $opts = $this->network_activated ? get_site_option('mc4wp_license') : get_option('mc4wp_license');
        if (empty($opts) || empty($opts['key'])) {
            return;
        }

        // transfer key to new option
        $license = $this->load_license();
        $license->key = $opts['key'];
        $this->update_license($license);

        // delete old option
        delete_option('mc4wp_license');
        delete_site_option('mc4wp_license');

        // try to obtain activation token
        $this->activate_license();
    }
}
