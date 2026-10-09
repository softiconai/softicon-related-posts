<?php
/*
* 
* Plugin Name: SoftIcon Related Posts – Similar Posts & Internal Linking
* Description: Displays related posts based on categories or tags to enhance engagement and navigation.
* Version: 1.7.0
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

if ( ! function_exists( 'alrp_fs' ) ) {
    // Freemius SDK: optional usage opt-in and the Add-Ons page where the Pro add-on is offered
    function alrp_fs() {
        global $alrp_fs;

        if ( ! isset( $alrp_fs ) ) {
            require_once dirname( __FILE__ ) . '/vendor/freemius/start.php';

            $alrp_fs = fs_dynamic_init( array(
                'id'               => '41092',
                'slug'             => 'softicon-related-posts',
                'type'             => 'plugin',
                'public_key'       => 'pk_533b8e0e365ac618a720a5f5bbeff',
                'is_premium'       => false,
                'has_addons'       => true,
                'has_paid_plans'   => false,
                'is_org_compliant' => true,
                'menu'             => array(
                    'slug'       => 'alrp-settings',
                    'first-path' => 'admin.php?page=alrp-settings',
                    'account'    => false,
                    'contact'    => false,
                    'support'    => false,
                ),
            ) );
        }

        return $alrp_fs;
    }

    alrp_fs();
    do_action( 'alrp_fs_loaded' );
}

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
        define('ALRP_PLUGIN_VERSION', '1.7.0');
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

        // add-ons such as SoftIcon Related Posts Pro hook in here
        do_action( 'alrp_loaded' );
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

// Freemius runs this on uninstall, so it can also record the uninstall reason
function alrp_uninstall_cleanup() {
    delete_option( 'alrp_settings' );
    delete_post_meta_by_key( '_alrp_hide' );
    delete_post_meta_by_key( '_alrp_manual_ids' );
    delete_option( 'alrp_cache_version' );
    delete_option( 'widget_alrp_widget' );
    delete_transient( 'alrp_activation_redirect' );

    global $wpdb;
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- one-off cleanup of this plugin's transients, no API lists them by prefix
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_alrp\_%' OR option_name LIKE '\_transient\_timeout\_alrp\_%'" );
}
alrp_fs()->add_action( 'after_uninstall', 'alrp_uninstall_cleanup' );
 

