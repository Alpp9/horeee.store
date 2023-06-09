<?php

namespace BeycanPress\YAIA;

class ChatGPT 
{
    use PluginHero\Helpers;

    /**
     * @return void
     */
    public function __construct()
    {
        add_action('init', function() {
            add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
            add_shortcode('chat-gpt', [$this, 'chatGPT']);
        });
    }

    /**
     * Enqueue scripts
     * @return void
     */
    public function enqueueScripts()
    {
        $this->addStyle('css/chat-gpt.css');
        $key = $this->addScript('js/chat-gpt.js');
        wp_localize_script($key, 'YAIA', [
            'bot' => $this->getImageUrl('bot.svg'),
            'user' => $this->getImageUrl('user.svg'),
            'lang' => Lang::get(),
            'apiUrl' => admin_url('admin-ajax.php'),
            'nonce'  => $this->createNewNonce(),
        ]);
    }

    /**
     * @return void
     */
    public function chatGPT()
    {
        return $this->view('chat-gpt-fe');
    }
}