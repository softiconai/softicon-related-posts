<?php
/*
* 
* Plugin Name: SoftIcon Related Posts – Similar Posts & Internal Linking
* Description: Displays related posts based on categories or tags to enhance engagement and navigation.
* Version: 1.6.1
* Requires at least: 6.3
* Requires PHP: 7.1
* Author: Al Amin
* Author URI: https://profiles.wordpress.org/alamincmt/
* License: GPLv3
* License URI: https://www.gnu.org/licenses/gpl-3.0.txt
* Text Domain: softicon-related-posts
* Domain Path: /languages
*
*/


if ( !defined('ABSPATH') ) { exit; }

class ALRP_Related_posts{

    public static $instance;

    private function __construct()
    {
        $this->defined_constants();
        $this->load_classes();

        // assets enqueue js&css
        add_action( 'wp_enqueue_scripts', array($this, 'alrp_plugins_enqueue_assets') );
        
    }

    public static function get_instance() {
        if( self::$instance ) {
            return self::$instance;
        }

        self::$instance = new self();
        return self::$instance;
    }

    public function defined_constants() {
        define('ALRP_PLUGIN_VERSION', '1.6.1');
        define('ALRP_PLUGIN_PATH', plugin_dir_path(__FILE__));
        define('ALRP_PLUGIN_URL', plugin_dir_url(__FILE__));
        define('ALRP_PLUGIN_ASSETS', plugin_dir_url(__FILE__) . 'assets/');
        define('ALRP_PLUGIN_BASENAME', plugin_basename(__FILE__));
    }

    public function load_classes() {

        require_once ALRP_PLUGIN_PATH . 'includes/cache.php';
        require_once ALRP_PLUGIN_PATH . 'includes/admin_settings.php';
        require_once ALRP_PLUGIN_PATH . 'includes/post_meta_box.php';
        require_once ALRP_PLUGIN_PATH . 'includes/get_category_show_posts.php';
        require_once ALRP_PLUGIN_PATH . 'includes/block.php';
        require_once ALRP_PLUGIN_PATH . 'includes/widget.php';
        require_once ALRP_PLUGIN_PATH . 'includes/videos.php';
        require_once ALRP_PLUGIN_PATH . 'includes/help_page.php';
        require_once ALRP_PLUGIN_PATH . 'includes/rest.php';

        new ALRP_Related_Posts\cache();
        new ALRP_Related_Posts\admin_settings();
        new ALRP_Related_Posts\post_meta_box();
        new ALRP_Related_Posts\get_category_show_posts();
        new ALRP_Related_Posts\block();
        new ALRP_Related_Posts\videos();
        new ALRP_Related_Posts\help_page();
        new ALRP_Related_Posts\rest();
        add_action( 'widgets_init', array( 'ALRP_Related_Posts\widget', 'register' ) );

    }

    public function alrp_plugins_enqueue_assets(){
        // alrp_main is registered on init in includes/block.php, shortcode output enqueues the style itself when used elsewhere
        $types = ALRP_Related_Posts\admin_settings::enabled_post_types();
        if ( $types && is_singular( $types ) ) {
            wp_enqueue_style('alrp_main');
        }
    }
}
ALRP_Related_posts::get_instance();
register_activation_hook( __FILE__, array( 'ALRP_Related_Posts\\admin_settings', 'on_activate' ) );
 

