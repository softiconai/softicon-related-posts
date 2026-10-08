<?php
namespace ALRP_Related_Posts;
if ( !defined('ABSPATH') ) { exit;}

class block {

    public function __construct()
    {
        add_action('init', array( $this, 'register' ));
        add_action('enqueue_block_editor_assets', array( $this, 'editor_data' ));
    }

    public function register() {
        // the block's "style" handle must exist before the block is registered
        wp_register_style('alrp_main', ALRP_PLUGIN_ASSETS . 'css/style.css', array(), ALRP_PLUGIN_VERSION );

        register_block_type( ALRP_PLUGIN_PATH . 'build' );
    }

    public function editor_data() {
        $handle   = generate_block_asset_handle('softicon/related-posts', 'editorScript');
        $settings = admin_settings::get();

        $data = array(
            'defaults' => array(
                'posts'          => (int) $settings['posts_per_page'],
                'columns'        => (int) $settings['columns'],
                'relation'       => $settings['relation'],
                'orderby'        => $settings['orderby'],
                'title'          => $settings['title'],
                'showCategories' => (bool) $settings['show_categories'],
                'showImage'      => (bool) $settings['show_image'],
                'showExcerpt'    => (bool) $settings['show_excerpt'],
                'showDate'       => (bool) $settings['show_date'],
                'showAuthor'     => (bool) $settings['show_author'],
                'showReadMore'   => (bool) $settings['show_read_more'],
                'layout'         => $settings['layout'],
                'imageRatio'     => $settings['image_ratio'],
                'cardStyle'      => $settings['card_style'],
                'radius'         => (int) $settings['radius'],
                'hoverZoom'      => (bool) $settings['hover_zoom'],
                'titleColor'     => $settings['title_color'],
                'textColor'      => $settings['text_color'],
                'cardBg'         => $settings['card_bg'],
            ),
            'choices'  => admin_settings::choices(),
        );

        wp_add_inline_script( $handle, 'window.alrpBlockData = ' . wp_json_encode( $data ) . ';', 'before' );
        wp_set_script_translations( $handle, 'softicon-related-posts' );
    }
}
