<?php
namespace ALRP_Related_Posts;
if ( !defined('ABSPATH') ) { exit;}

class admin_settings {

    const OPTION   = 'alrp_settings';
    const PAGE   = 'alrp-settings';

    private $settings_hook = '';

    public function __construct()
    {
        add_action('admin_menu', array( $this, 'add_menu' ));
        add_action('admin_init', array( $this, 'redirect_old_url' ));
        add_filter('plugin_action_links_' . ALRP_PLUGIN_BASENAME, array( $this, 'action_links' ));
        add_action('admin_enqueue_scripts', array( $this, 'enqueue_assets' ));
    }

    // add-ons add their own keys through the alrp_default_settings filter
    public static function defaults() {
        return apply_filters( 'alrp_default_settings', array(
            'enable'          => 1,
            'post_types'      => array('post'),
            'show_in_feed'    => 0,
            'position'        => 'after',
            'title'           => __('Related Posts', 'softicon-related-posts'),
            'posts_per_page'  => 6,
            'columns'         => 3,
            'relation'        => 'category',
            'orderby'         => 'rand',
            'show_categories' => 1,
            'show_image'      => 1,
            'image_size'      => 'medium_large',
            'fallback_image'    => '',
            'fallback_image_id' => 0,
            'show_excerpt'    => 1,
            'excerpt_length'  => 15,
            'show_date'       => 0,
            'show_author'     => 1,
            'show_read_more'  => 1,
            'read_more_text'  => __('Read More', 'softicon-related-posts'),
            'exclude_categories' => array(),
            'exclude_posts'      => array(),
            'max_age'            => 0,
            'layout'             => 'grid',
            'image_ratio'        => 'fixed',
            'card_style'         => 'none',
            'radius'             => 0,
            'hover_zoom'         => 0,
            'title_color'        => '',
            'text_color'         => '',
            'card_bg'            => '',
        ) );
    }

    public static function get() {
        $saved    = get_option(self::OPTION, array());
        $settings = wp_parse_args( is_array($saved) ? $saved : array(), self::defaults() );

        // a value saved by an add-on that is no longer active (e.g. a Pro layout) falls back to the default
        $defaults = self::defaults();
        foreach ( self::choices() as $key => $options ) {
            if ( ! isset( $options[ $settings[ $key ] ] ) ) {
                $settings[ $key ] = $defaults[ $key ];
            }
        }

        return $settings;
    }

    // add-ons can add options, e.g. a carousel layout, through the alrp_setting_choices filter
    public static function choices() {
        return apply_filters( 'alrp_setting_choices', array(
            'position' => array(
                'after'  => __('After content', 'softicon-related-posts'),
                'before' => __('Before content', 'softicon-related-posts'),
                'manual' => __('Manual (shortcode only)', 'softicon-related-posts'),
            ),
            'relation' => array(
                'category' => __('Same category', 'softicon-related-posts'),
                'tag'      => __('Same tag', 'softicon-related-posts'),
                'both'     => __('Same category or tag', 'softicon-related-posts'),
                'all'      => __('Any shared taxonomy (for custom post types)', 'softicon-related-posts'),
            ),
            'layout' => array(
                'grid'    => __('Grid', 'softicon-related-posts'),
                'list'    => __('List (image left)', 'softicon-related-posts'),
                'minimal' => __('Minimal (titles only)', 'softicon-related-posts'),
            ),
            'image_ratio' => array(
                'fixed' => __('Fixed height (200px)', 'softicon-related-posts'),
                '16-9'  => '16:9',
                '4-3'   => '4:3',
                '3-2'   => '3:2',
                '1-1'   => __('Square (1:1)', 'softicon-related-posts'),
            ),
            'card_style' => array(
                'none'   => __('None', 'softicon-related-posts'),
                'border' => __('Border', 'softicon-related-posts'),
                'shadow' => __('Shadow', 'softicon-related-posts'),
            ),
            'orderby' => array(
                'rand'          => __('Random', 'softicon-related-posts'),
                'relevance'     => __('Most relevant (most shared tags and categories)', 'softicon-related-posts'),
                'date'          => __('Latest', 'softicon-related-posts'),
                'modified'      => __('Recently updated', 'softicon-related-posts'),
                'comment_count' => __('Most commented', 'softicon-related-posts'),
                'title'         => __('Title (A-Z)', 'softicon-related-posts'),
            ),
        ) );
    }

    // public post types the related posts can be shown on
    public static function post_type_options() {
        $types = array();
        foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $type ) {
            if ( 'attachment' !== $type->name ) {
                $types[ $type->name ] = $type->labels->name;
            }
        }
        return $types;
    }

    public static function enabled_post_types() {
        return self::get()['post_types'];
    }

    public static function url($page = self::PAGE) {
        return admin_url( 'admin.php?page=' . $page );
    }

    public function add_menu() {
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="#a7aaad" d="M3 4h8v7H3V4Zm10 0h8v7h-8V4ZM3 13h8v7H3v-7Zm10 2h8v1.5h-8V15Zm0 3h5v1.5h-5V18Z"/></svg>';

        add_menu_page(
            __('Related Posts', 'softicon-related-posts'),
            __('Related Posts', 'softicon-related-posts'),
            'manage_options',
            self::PAGE,
            array( $this, 'render_page' ),
            'data:image/svg+xml;base64,' . base64_encode( $icon ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- menu icon data URI
            26
        );

        // same slug as the parent, so the first submenu item reads "Settings" instead of repeating the menu title
        $this->settings_hook = add_submenu_page( self::PAGE, __('Related Posts Settings', 'softicon-related-posts'), __('Settings', 'softicon-related-posts'), 'manage_options', self::PAGE, array( $this, 'render_page' ) );

        add_submenu_page( self::PAGE, __('Help & Videos', 'softicon-related-posts'), __('Help & Videos', 'softicon-related-posts'), 'manage_options', help_page::PAGE, array( 'ALRP_Related_Posts\help_page', 'render' ) );
    }

    // settings lived under Settings → Related Posts before 1.6.0, keep old bookmarks working
    public function redirect_old_url() {
        global $pagenow;
        if ( 'options-general.php' === $pagenow && isset( $_GET['page'] ) && self::PAGE === sanitize_key( wp_unslash( $_GET['page'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only redirect
            wp_safe_redirect( self::url() );
            exit;
        }
    }

    public function action_links($links) {
        $url = self::url();
        array_unshift($links, '<a href="' . esc_url($url) . '">' . esc_html__('Settings', 'softicon-related-posts') . '</a>');
        return $links;
    }

    public static function image_sizes() {
        $sizes = array();
        foreach ( get_intermediate_image_sizes() as $size ) {
            $sizes[ $size ] = $size;
        }
        $sizes['full'] = 'full';
        return $sizes;
    }

    public static function sanitize($input) {
        $input    = is_array($input) ? $input : array();
        $defaults = self::defaults();
        $choices  = self::choices();
        $clean    = array();

        foreach ( array('enable', 'show_in_feed', 'show_categories', 'show_image', 'show_excerpt', 'show_date', 'show_author', 'show_read_more', 'hover_zoom') as $key ) {
            $clean[ $key ] = empty( $input[ $key ] ) ? 0 : 1;
        }

        foreach ( $choices as $key => $options ) {
            $clean[ $key ] = isset( $input[ $key ], $options[ $input[ $key ] ] ) ? $input[ $key ] : $defaults[ $key ];
        }

        $clean['posts_per_page'] = min( 24, max( 1, absint( $input['posts_per_page'] ?? $defaults['posts_per_page'] ) ) );
        $clean['columns']        = min( 6, max( 1, absint( $input['columns'] ?? $defaults['columns'] ) ) );
        $clean['excerpt_length'] = min( 100, max( 1, absint( $input['excerpt_length'] ?? $defaults['excerpt_length'] ) ) );
        $clean['max_age']        = min( 120, absint( $input['max_age'] ?? 0 ) );
        $clean['radius']         = min( 40, absint( $input['radius'] ?? 0 ) );

        foreach ( array('title_color', 'text_color', 'card_bg') as $key ) {
            $clean[ $key ] = self::sanitize_color( $input[ $key ] ?? '' );
        }

        $clean['post_types'] = array_values( array_intersect( (array) ( $input['post_types'] ?? array() ), array_keys( self::post_type_options() ) ) );

        $clean['exclude_categories'] = array_values( array_filter( wp_parse_id_list( $input['exclude_categories'] ?? array() ) ) );
        $clean['exclude_posts']      = array_values( array_filter( wp_parse_id_list( $input['exclude_posts'] ?? array() ) ) );

        $sizes               = self::image_sizes();
        $clean['image_size'] = isset( $input['image_size'], $sizes[ $input['image_size'] ] ) ? $input['image_size'] : $defaults['image_size'];

        $clean['title']          = sanitize_text_field( $input['title'] ?? '' );
        $clean['read_more_text'] = sanitize_text_field( $input['read_more_text'] ?? '' );
        $clean['fallback_image'] = esc_url_raw( $input['fallback_image'] ?? '' );

        $image_id                   = absint( $input['fallback_image_id'] ?? 0 );
        $clean['fallback_image_id'] = $image_id && wp_attachment_is_image( $image_id ) ? $image_id : 0;

        // add-ons sanitize the keys they added to the defaults
        return apply_filters( 'alrp_sanitize_settings', $clean, $input );
    }

    public function enqueue_assets($hook) {
        if ( $hook !== $this->settings_hook ) {
            return;
        }
        $asset = include ALRP_PLUGIN_PATH . 'build/admin-settings.asset.php';

        wp_enqueue_media();
        wp_enqueue_style('wp-components');
        wp_enqueue_style('alrp_main');
        wp_enqueue_style('alrp_admin_settings', ALRP_PLUGIN_URL . 'build/admin-settings.css', array('wp-components'), $asset['version']);
        wp_enqueue_script('alrp_admin_settings', ALRP_PLUGIN_URL . 'build/admin-settings.js', $asset['dependencies'], $asset['version'], true);
        wp_add_inline_script('alrp_admin_settings', 'window.alrpSettingsData = ' . wp_json_encode( self::app_data() ) . ';', 'before');
        wp_set_script_translations('alrp_admin_settings', 'softicon-related-posts');

        // add-ons enqueue their settings tabs here, with alrp_admin_settings as a dependency
        do_action('alrp_admin_enqueue_scripts');
    }

    // everything the React settings app needs on first paint
    private static function app_data() {
        $settings = self::get();
        $image    = $settings['fallback_image_id'] ? wp_get_attachment_image_url( $settings['fallback_image_id'], 'thumbnail' ) : '';

        $categories = array();
        foreach ( get_categories( array( 'hide_empty' => false ) ) as $cat ) {
            $categories[] = array( 'id' => (int) $cat->term_id, 'name' => $cat->name );
        }

        $palette = array();
        foreach ( (array) wp_get_global_settings( array( 'color', 'palette', 'theme' ) ) as $color ) {
            if ( isset( $color['color'] ) ) {
                $palette[] = $color['color'];
            }
        }

        return apply_filters( 'alrp_admin_app_data', array(
            'settings'   => $settings,
            'choices'    => self::choices(),
            'postTypes'  => self::post_type_options(),
            'imageSizes' => array_keys( self::image_sizes() ),
            'categories' => $categories,
            'palette'    => array_slice( $palette, 0, 12 ),
            'fallback'   => $image ? $image : $settings['fallback_image'],
            'version'    => ALRP_PLUGIN_VERSION,
            'helpUrl'    => self::url( help_page::PAGE ),
            'isPro'      => false,
            'proUrl'     => self::pro_url(),
        ) );
    }

    // Freemius lists the Pro add-on on its Add-Ons page
    public static function pro_url() {
        return function_exists('alrp_fs') ? alrp_fs()->get_addons_url() : '';
    }

    // hex from the settings page, rgb()/rgba() from the block's color picker
    public static function sanitize_color($color) {
        $color = trim( (string) $color );
        if ( sanitize_hex_color( $color ) ) {
            return sanitize_hex_color( $color );
        }
        if ( preg_match( '/^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(,\s*(0|1|0?\.\d+|1\.0+)\s*)?\)$/', $color, $m ) && max( $m[1], $m[2], $m[3] ) <= 255 ) {
            return $color;
        }
        return '';
    }

    public function render_page() {
        if ( ! current_user_can('manage_options') ) {
            return;
        }
        ?>
        <div class="wrap alrp-settings-wrap">
            <div id="alrp-settings-app"></div>
            <noscript><p><?php esc_html_e('The Related Posts settings need JavaScript.', 'softicon-related-posts'); ?></p></noscript>
        </div>
        <?php
    }
}
