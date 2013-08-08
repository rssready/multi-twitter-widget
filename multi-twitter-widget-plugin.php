<?php
/*
Plugin Name: Twitter Stream Widget
Plugin URI: https://github.com/rssready/multi-twitter-widget
Description: A widget for multiple twitter accounts with oauth support for Twitter API v1.1
Author: Clayton McIlrath, Roger Hamilton, Matt Senate, and Mike Feineman
Version: 2.0.0
*/

require_once('multi-twitter-widget.php');

if(!class_exists('Multi_Twitter_Widget_Plugin')) { 
    class Multi_Twitter_Widget_Plugin {     
        /** * Construct the plugin object */ 
        public function __construct()
        {
            add_action( 'widgets_init', array('Multi_Twitter_Widget_Plugin', 'register_widgets') );
        } 
        // END public function __construct 
        
        /** * Activate the plugin */ 
        public static function activate() 
        { 

        }
        // END public static function activate
        
        /** * Deactivate the plugin */
        public static function deactivate() 
        { 
            // Do nothing 
        }
        // END public static function deactivate
        
        public static function register_widgets() {
            register_widget( 'Multi_Twitter_Widget' );
        }
    } 
    // END class Multi_Twitter_Widget_Plugin 
}
// END if(!class_exists('Multi_Twitter_Widget_Plugin')


if(class_exists('Multi_Twitter_Widget_Plugin')) {
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('Multi_Twitter_Widget_Plugin', 'activate'));
    register_deactivation_hook(__FILE__, array('Multi_Twitter_Widget_Plugin', 'deactivate'));
     
    // instantiate the plugin class
    $multi_twitter_widget_plugin = new Multi_Twitter_Widget_Plugin();
}