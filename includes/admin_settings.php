<?php
namespace ALRP_Related_Posts;
if ( !defined('ABSPATH') ) { exit;}

class admin_settings {

    const OPTION = 'alrp_settings';
    const PAGE   = 'alrp-settings';

    private $settings_hook = '';

    public function __construct()
    {
        add_action('admin_menu', array( $this, 'add_menu' ));
        add_action('admin_init', array( $this, 'register_settings' ));
        add_action('admin_init', array( $this, 'redirect_old_url' ));
        add_filter('plugin_action_links_' . ALRP_PLUGIN_BASENAME, array( $this, 'action_links' ));
        add_action('admin_enqueue_scripts', array( $this, 'enqueue_assets' ));
    }

    public static function defaults() {
        return array(
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
        );
    }

    public static function get() {
        $saved = get_option(self::OPTION, array());
        return wp_parse_args( is_array($saved) ? $saved : array(), self::defaults() );
    }

    public static function choices() {
        return array(
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
        );
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

    public function register_settings() {
        register_setting('alrp_settings_group', self::OPTION, array(
            'type'              => 'array',
            'sanitize_callback' => array( __CLASS__, 'sanitize' ),
            'default'           => self::defaults(),
        ));

        add_settings_section('alrp_general', __('General', 'softicon-related-posts'), '__return_false', 'alrp-settings');
        add_settings_section('alrp_filters', __('Filters', 'softicon-related-posts'), '__return_false', 'alrp-settings');
        add_settings_section('alrp_display', __('Display', 'softicon-related-posts'), '__return_false', 'alrp-settings');
        add_settings_section('alrp_design', __('Design', 'softicon-related-posts'), '__return_false', 'alrp-settings');

        $choices = self::choices();

        // general fields
        $this->add_field('enable', __('Enable', 'softicon-related-posts'), 'checkbox', 'alrp_general', array( 'label' => __('Show related posts on single posts', 'softicon-related-posts') ));
        $this->add_field('post_types', __('Post types', 'softicon-related-posts'), 'post_types', 'alrp_general', array( 'desc' => __('Pages usually have no categories or tags, so use hand-picked related posts there.', 'softicon-related-posts') ));
        $this->add_field('position', __('Position', 'softicon-related-posts'), 'select', 'alrp_general', array( 'options' => $choices['position'] ));
        $this->add_field('relation', __('Related by', 'softicon-related-posts'), 'select', 'alrp_general', array( 'options' => $choices['relation'] ));
        $this->add_field('orderby', __('Order by', 'softicon-related-posts'), 'select', 'alrp_general', array( 'options' => $choices['orderby'] ));
        $this->add_field('posts_per_page', __('Number of posts', 'softicon-related-posts'), 'number', 'alrp_general', array( 'min' => 1, 'max' => 24 ));
        $this->add_field('show_in_feed', __('RSS feed', 'softicon-related-posts'), 'checkbox', 'alrp_general', array( 'label' => __('Add related post links to items in your RSS feed', 'softicon-related-posts') ));

        // filter fields
        $this->add_field('exclude_categories', __('Exclude categories', 'softicon-related-posts'), 'categories', 'alrp_filters', array( 'desc' => __('Posts in these categories never appear as related posts.', 'softicon-related-posts') ));
        $this->add_field('exclude_posts', __('Exclude posts', 'softicon-related-posts'), 'ids', 'alrp_filters', array( 'desc' => __('Comma separated post IDs, e.g. 12, 45', 'softicon-related-posts') ));
        $this->add_field('max_age', __('Only posts from the last', 'softicon-related-posts'), 'number', 'alrp_filters', array( 'min' => 0, 'max' => 120, 'desc' => __('Months. 0 shows posts of any age.', 'softicon-related-posts') ));

        // display fields
        $this->add_field('title', __('Section title', 'softicon-related-posts'), 'text', 'alrp_display');
        $this->add_field('columns', __('Columns', 'softicon-related-posts'), 'number', 'alrp_display', array( 'min' => 1, 'max' => 6 ));
        $this->add_field('show_categories', __('Categories', 'softicon-related-posts'), 'checkbox', 'alrp_display', array( 'label' => __('Show current post categories above related posts', 'softicon-related-posts') ));
        $this->add_field('show_image', __('Image', 'softicon-related-posts'), 'checkbox', 'alrp_display', array( 'label' => __('Show featured image', 'softicon-related-posts') ));
        $this->add_field('image_size', __('Image size', 'softicon-related-posts'), 'select', 'alrp_display', array( 'options' => self::image_sizes() ));
        $this->add_field('fallback_image_id', __('Fallback image', 'softicon-related-posts'), 'media', 'alrp_display', array( 'desc' => __('Used when a post has no featured image. Leave empty to show a placeholder.', 'softicon-related-posts') ));
        $this->add_field('show_excerpt', __('Excerpt', 'softicon-related-posts'), 'checkbox', 'alrp_display', array( 'label' => __('Show excerpt', 'softicon-related-posts') ));
        $this->add_field('excerpt_length', __('Excerpt length (words)', 'softicon-related-posts'), 'number', 'alrp_display', array( 'min' => 1, 'max' => 100 ));
        $this->add_field('show_date', __('Date', 'softicon-related-posts'), 'checkbox', 'alrp_display', array( 'label' => __('Show publish date', 'softicon-related-posts') ));
        $this->add_field('show_author', __('Author', 'softicon-related-posts'), 'checkbox', 'alrp_display', array( 'label' => __('Show author avatar and name', 'softicon-related-posts') ));
        $this->add_field('show_read_more', __('Read more', 'softicon-related-posts'), 'checkbox', 'alrp_display', array( 'label' => __('Show read more link', 'softicon-related-posts') ));
        $this->add_field('read_more_text', __('Read more text', 'softicon-related-posts'), 'text', 'alrp_display');

        // design fields
        $this->add_field('layout', __('Layout', 'softicon-related-posts'), 'select', 'alrp_design', array( 'options' => $choices['layout'], 'desc' => __('Minimal shows only titles (and dates if enabled).', 'softicon-related-posts') ));
        $this->add_field('image_ratio', __('Image ratio', 'softicon-related-posts'), 'select', 'alrp_design', array( 'options' => $choices['image_ratio'] ));
        $this->add_field('card_style', __('Card style', 'softicon-related-posts'), 'select', 'alrp_design', array( 'options' => $choices['card_style'] ));
        $this->add_field('radius', __('Corner radius (px)', 'softicon-related-posts'), 'number', 'alrp_design', array( 'min' => 0, 'max' => 40 ));
        $this->add_field('hover_zoom', __('Hover effect', 'softicon-related-posts'), 'checkbox', 'alrp_design', array( 'label' => __('Zoom image on hover', 'softicon-related-posts') ));
        $this->add_field('title_color', __('Title color', 'softicon-related-posts'), 'color', 'alrp_design');
        $this->add_field('text_color', __('Text color', 'softicon-related-posts'), 'color', 'alrp_design');
        $this->add_field('card_bg', __('Card background', 'softicon-related-posts'), 'color', 'alrp_design', array( 'desc' => __('Leave colors empty to use your theme colors.', 'softicon-related-posts') ));
    }

    private function add_field($key, $label, $type, $section, $extra = array()) {
        add_settings_field(
            'alrp_' . $key,
            $label,
            array( $this, 'render_field' ),
            'alrp-settings',
            $section,
            array_merge( $extra, array( 'key' => $key, 'type' => $type, 'label_for' => 'alrp_' . $key ) )
        );
    }

    private static function image_sizes() {
        $sizes = array();
        foreach ( get_intermediate_image_sizes() as $size ) {
            $sizes[ $size ] = $size;
        }
        $sizes['full'] = 'full';
        return $sizes;
    }

    public function render_field($args) {
        $settings = self::get();
        $key      = $args['key'];
        $id       = 'alrp_' . $key;
        $name     = self::OPTION . '[' . $key . ']';
        $value    = $settings[ $key ];

        switch ( $args['type'] ) {
            case 'checkbox':
                printf(
                    '<label><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s> %4$s</label>',
                    esc_attr($id), esc_attr($name), checked( 1, (int) $value, false ), esc_html( $args['label'] )
                );
                break;

            case 'select':
                printf('<select id="%1$s" name="%2$s">', esc_attr($id), esc_attr($name));
                foreach ( $args['options'] as $opt_value => $opt_label ) {
                    printf('<option value="%1$s" %2$s>%3$s</option>', esc_attr($opt_value), selected( $value, $opt_value, false ), esc_html($opt_label));
                }
                echo '</select>';
                break;

            case 'number':
                printf(
                    '<input type="number" id="%1$s" name="%2$s" value="%3$s" min="%4$d" max="%5$d" class="small-text">',
                    esc_attr($id), esc_attr($name), esc_attr($value), (int) $args['min'], (int) $args['max']
                );
                break;

            case 'post_types':
                foreach ( self::post_type_options() as $type => $label ) {
                    printf(
                        '<label style="display:block"><input type="checkbox" name="%1$s[]" value="%2$s" %3$s> %4$s</label>',
                        esc_attr($name), esc_attr($type), checked( in_array( $type, $value, true ), true, false ), esc_html( $label )
                    );
                }
                break;

            case 'categories':
                echo '<fieldset style="max-height:180px;overflow:auto;padding:8px;border:1px solid #dcdcde;background:#fff;max-width:400px">';
                foreach ( get_categories( array( 'hide_empty' => false ) ) as $cat ) {
                    printf(
                        '<label style="display:block"><input type="checkbox" name="%1$s[]" value="%2$d" %3$s> %4$s</label>',
                        esc_attr($name), (int) $cat->term_id, checked( in_array( (int) $cat->term_id, $value, true ), true, false ), esc_html( $cat->name )
                    );
                }
                echo '</fieldset>';
                break;

            case 'color':
                printf(
                    '<input type="text" id="%1$s" name="%2$s" value="%3$s" class="alrp-color">',
                    esc_attr($id), esc_attr($name), esc_attr($value)
                );
                break;

            case 'ids':
                printf(
                    '<input type="text" id="%1$s" name="%2$s" value="%3$s" class="regular-text">',
                    esc_attr($id), esc_attr($name), esc_attr( implode( ', ', $value ) )
                );
                break;

            case 'media':
                // fallback_image keeps a URL saved by 1.1.0 until a library image replaces it
                $preview = $value ? wp_get_attachment_image( $value, 'thumbnail' ) : '';
                if ( ! $preview && $settings['fallback_image'] ) {
                    $preview = '<img src="' . esc_url( $settings['fallback_image'] ) . '" alt="">';
                }
                printf(
                    '<div class="alrp-media"><input type="hidden" id="%1$s" name="%2$s" value="%3$s"><input type="hidden" class="alrp-media-url" name="%4$s" value="%5$s"><div class="alrp-media-preview">%6$s</div><button type="button" class="button alrp-media-select">%7$s</button> <button type="button" class="button-link alrp-media-remove"%8$s>%9$s</button></div>',
                    esc_attr($id), esc_attr($name), esc_attr($value),
                    esc_attr( self::OPTION . '[fallback_image]' ), esc_attr( $settings['fallback_image'] ),
                    wp_kses_post( $preview ),
                    esc_html__('Select image', 'softicon-related-posts'),
                    $preview ? '' : ' hidden',
                    esc_html__('Remove', 'softicon-related-posts')
                );
                break;

            default:
                printf(
                    '<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" class="regular-text">',
                    esc_attr( $args['type'] ), esc_attr($id), esc_attr($name), esc_attr($value)
                );
        }

        if ( ! empty( $args['desc'] ) ) {
            printf('<p class="description">%s</p>', esc_html( $args['desc'] ));
        }
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

        return $clean;
    }

    public function enqueue_assets($hook) {
        if ( $hook !== $this->settings_hook ) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('alrp_admin', ALRP_PLUGIN_ASSETS . 'js/admin.js', array('jquery', 'wp-color-picker'), ALRP_PLUGIN_VERSION, true);
        wp_localize_script('alrp_admin', 'alrpAdmin', array(
            'title'  => __('Select fallback image', 'softicon-related-posts'),
            'button' => __('Use this image', 'softicon-related-posts'),
        ));
        wp_add_inline_style('wp-admin', '.alrp-media-preview img{display:block;max-width:150px;height:auto;margin-bottom:8px}');
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
        <div class="wrap">
            <h1><?php esc_html_e('SoftIcon Related Posts', 'softicon-related-posts'); ?></h1>
            <?php settings_errors(); // top-level pages don't get the notice that options-general.php prints ?>
            <p>
                <?php esc_html_e('New here? Watch the short videos and see the shortcode and block options.', 'softicon-related-posts'); ?>
                <a href="<?php echo esc_url( self::url( help_page::PAGE ) ); ?>"><?php esc_html_e('Help & Videos', 'softicon-related-posts'); ?></a>
            </p>
            <form method="post" action="options.php">
                <?php
                settings_fields('alrp_settings_group');
                do_settings_sections('alrp-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
