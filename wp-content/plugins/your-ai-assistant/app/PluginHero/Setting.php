<?php

namespace BeycanPress\YAIA\PluginHero;

use \CSF;

abstract class Setting
{
    use Helpers;

    /**
     * @var string
     */
    private static $prefix;

    /**
     * @param string $title
     * @param string|null $parent
     * @return void
     */
    public function __construct(string $title, string $parent = null)
    {
        self::$prefix = $this->settingKey;

        $params = array(

            'framework_title'         => $title . ' <small>By BeycanPress</small>',

            // menu settings
            'menu_title'              => $title,
            'menu_slug'               => self::$prefix,
            'menu_capability'         => 'manage_options',
            'menu_position'           => null,
            'menu_hidden'             => false,

            // menu extras
            'show_bar_menu'           => false,
            'show_sub_menu'           => false,
            'show_network_menu'       => true,
            'show_in_customizer'      => false,

            'show_search'             => true,
            'show_reset_all'          => true,
            'show_reset_section'      => true,
            'show_footer'             => true,
            'show_all_options'        => true,
            'sticky_header'           => true,
            'save_defaults'           => true,
            'ajax_save'               => true,
            
            // database model
            'transient_time'          => 0,

            // contextual help
            'contextual_help'         => array(),

            // typography options
            'enqueue_webfont'         => false,
            'async_webfont'           => false,

            // others
            'output_css'              => false,

            // theme
            'theme'                   => 'dark',

            // external default values
            'defaults'                => array(),

        );

        if (!is_null($parent)) {
            $params['menu_type'] = 'submenu';
            $params['menu_parent'] = $parent;
        }

        CSF::createOptions(self::$prefix, $params);
    }

    /**
     * @param array $params
     * @return void
     */
    public static function createSection(array $params)
    {
        CSF::createSection(self::$prefix, $params);
    }

    /**
     * @param null|string $key
     * @return mixed
     */
    public static function get(?string $key = null)
    {
        return Plugin::$instance->setting($key);
    }

    /**
     * @return void
     */
    protected function licensed() : void
    {
        if ($expireTime = get_option('licenseExpireTime_' . $this->pluginKey)) {
            if (time() > strtotime($expireTime)) {
                self::deleteLicense();
                delete_option('licenseExpireTime_' . $this->pluginKey);
            }
        }

        add_action("csf_".self::$prefix."_save_after", function($data, $opt) {
            if (isset($opt->errors['license'])) self::deleteLicense();
        }, 10, 2);

        $verifier = new LicenseVerifier('https://beycanpress.com/');

        self::createSection(array(
            'id'     => 'license', 
            'title'  => esc_html__('License'),
            'icon'   => 'fa fa-key',
            'priority' => 99999,
            'fields' => array(
                array(
                    'id'    => 'license',
                    'type'  => 'text',
                    'title' => esc_html__('License (Purchase code)'),
                    'sanitize' => function($val) {
                        return sanitize_text_field($val);
                    },
                    'validate' => function($val) use ($verifier) {
                        $val = sanitize_text_field($val);
                        if (empty($val)) {
                            return esc_html__('License cannot be empty.');
                        } elseif (strlen($val) < 36 || strlen($val) > 36) {
                            return esc_html__('License must consist of 36 characters.');
                        }

                        /** @var object $data */
                        $data = $verifier->verify($val, $this->pluginKey);
                        if (isset($data->data->expireTime) && $data->data->expireTime) {
                            update_option('licenseExpireTime_' . $this->pluginKey, $data->data->expireTime);
                        }
                        
                        if (!$data->success) {
                            return esc_html__($data->message . " - Error code: " . $data->errorCode);
                        }
                    }
                ),
            ) 
        ));
    }

    /**
     * @return void
     */
    public static function deleteLicense(): void
    {
        $settings = self::get();
        if (isset($settings['license'])) {
            unset($settings['license']);
            update_option(Plugin::$instance->settingKey, $settings);
        }
    }
}