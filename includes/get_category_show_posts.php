<?php
namespace ALRP_Related_Posts;
if ( !defined('ABSPATH') ) { exit;}

class get_category_show_posts {

    private static $instance;

    // prevents re-entry when other plugins call the_content inside our output
    private $rendering = false;

    public function __construct()
    {
        self::$instance = $this;
        add_filter('the_content', array( $this, 'the_content'));
        add_filter('the_content_feed', array( $this, 'feed_content' ));
        add_shortcode('softicon_related_posts', array( $this, 'shortcode' ));
    }

    public static function instance() {
        return self::$instance;
    }

    public function the_content($contents) {
        // an empty list would make is_singular() match every post type
        $types = admin_settings::enabled_post_types();
        if ( ! $types || ! is_singular( $types ) || ! is_main_query() || get_the_ID() !== get_queried_object_id() ) {
            return $contents;
        }

        // the author already placed related posts in the content
        if ( has_block('softicon/related-posts') || has_shortcode( get_post_field('post_content'), 'softicon_related_posts' ) ) {
            return $contents;
        }

        $settings = admin_settings::get();
        if ( ! $settings['enable'] || 'manual' === $settings['position'] || post_meta_box::is_hidden( get_the_ID() ) ) {
            return $contents;
        }

        // add-ons can take over placement, e.g. to show the list inside the content instead
        if ( ! apply_filters( 'alrp_auto_display', true, get_the_ID(), $settings ) ) {
            return $contents;
        }

        $html = $this->render( get_the_ID(), $settings );
        return 'before' === $settings['position'] ? $html . $contents : $contents . $html;
    }

    // feeds get a plain list of links, themes' CSS doesn't apply there
    public function feed_content($content) {
        $settings = admin_settings::get();
        $post_id  = get_the_ID();
        if ( ! $settings['show_in_feed'] || ! in_array( get_post_type( $post_id ), $settings['post_types'], true ) || post_meta_box::is_hidden( $post_id ) ) {
            return $content;
        }

        $posts = $this->get_related_posts( $post_id, $settings );
        if ( ! $posts ) {
            return $content;
        }

        $html = $settings['title'] ? '<h3>' . esc_html( $settings['title'] ) . '</h3>' : '';
        $html .= '<ul>';
        foreach ( $posts as $post ) {
            $html .= '<li><a href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</a></li>';
        }
        $html .= '</ul>';

        return $content . $html;
    }

    public function shortcode($atts) {
        return $this->render_atts( shortcode_atts( self::default_atts(), $atts, 'softicon_related_posts' ) );
    }

    // shared by the shortcode and the block, missing keys fall back to the settings page
    public function render_atts($atts) {
        $atts                   = array_merge( self::default_atts(), $atts );
        $atts['posts_per_page'] = $atts['posts'];

        // post_id comes from contributors via the shortcode, so only public or readable targets
        $post_id = absint( $atts['post_id'] );
        $target  = $post_id ? get_post( $post_id ) : null;
        if ( ! $target
            || ! is_post_type_viewable( $target->post_type )
            || ( ! is_post_publicly_viewable( $target ) && ! current_user_can( 'read_post', $post_id ) )
            || post_meta_box::is_hidden( $post_id ) ) {
            return '';
        }

        // run the values through the same sanitizer as the settings page
        return $this->render( $post_id, admin_settings::sanitize( array_merge( admin_settings::get(), $atts ) ) );
    }

    private static function default_atts() {
        $settings = admin_settings::get();

        return array(
            'post_id'         => get_the_ID(),
            'posts'           => $settings['posts_per_page'],
            'columns'         => $settings['columns'],
            'relation'        => $settings['relation'],
            'orderby'         => $settings['orderby'],
            'title'           => $settings['title'],
            'show_categories' => $settings['show_categories'],
            'show_image'      => $settings['show_image'],
            'show_excerpt'    => $settings['show_excerpt'],
            'show_date'       => $settings['show_date'],
            'show_author'     => $settings['show_author'],
            'show_read_more'  => $settings['show_read_more'],
            'layout'          => $settings['layout'],
            'image_ratio'     => $settings['image_ratio'],
            'card_style'      => $settings['card_style'],
            'radius'          => $settings['radius'],
            'hover_zoom'      => $settings['hover_zoom'],
            'title_color'     => $settings['title_color'],
            'text_color'      => $settings['text_color'],
            'card_bg'         => $settings['card_bg'],
        );
    }

    public function get_related_posts($post_id, $settings) {
        $post_type = get_post_type( $post_id );
        if ( ! $post_type || ! is_post_type_viewable( $post_type ) ) {
            return array();
        }

        $limit  = (int) $settings['posts_per_page'];
        $manual = post_meta_box::manual_ids( $post_id );
        $auto   = $this->auto_ids( $post_id, $settings, $manual );

        if ( 'rand' === $settings['orderby'] ) {
            shuffle( $auto );
        }

        // hand-picked posts first, automatic ones fill the remaining slots
        $ids = array_values( array_unique( array_merge( $manual, $auto ) ) );
        $ids = apply_filters( 'alrp_related_post_ids', $ids, $post_id, $settings );
        if ( ! $ids ) {
            return array();
        }

        // post_status drops hand-picked posts that are no longer published
        return get_posts( array(
            'post_type'           => get_post_type( $post_id ),
            'post_status'         => 'publish',
            'post__in'            => $ids,
            'orderby'             => 'post__in',
            'posts_per_page'      => $limit,
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
            'suppress_filters'    => false, // lets WPML and Polylang keep the current language
        ) );
    }

    // cached, ordered pool of automatically related post IDs
    private function auto_ids($post_id, $settings, $manual) {
        $post_type  = get_post_type( $post_id );
        $taxonomies = $this->taxonomies( $post_type, $settings['relation'] );

        $tax_query = array( 'relation' => 'OR' );
        $term_ids  = array();
        foreach ( $taxonomies as $taxonomy ) {
            $terms = get_the_terms( $post_id, $taxonomy );
            $ids   = is_array($terms) ? wp_list_pluck( $terms, 'term_id' ) : array();
            if ( $ids ) {
                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $ids,
                );
                $term_ids[ $taxonomy ] = $ids;
            }
        }

        // no terms means nothing is related, don't fall back to all posts
        if ( count($tax_query) < 2 ) {
            return array();
        }

        $orderby = $settings['orderby'];
        $limit   = (int) $settings['posts_per_page'];

        // random and relevance pick from a pool of recent matches instead of ORDER BY RAND() or SQL scoring
        if ( 'rand' === $orderby ) {
            $pool = $limit * 3;
        } elseif ( 'relevance' === $orderby ) {
            $pool = min( 100, $limit * 5 );
        } else {
            $pool = $limit + count( $manual );
        }

        $args = array(
            'post_type'           => $post_type,
            'posts_per_page'      => $pool,
            'tax_query'           => $tax_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- matching shared terms is the core of related posts, IDs are cached
            'orderby'             => in_array( $orderby, array('rand', 'relevance'), true ) ? 'date' : $orderby,
            'order'               => 'title' === $orderby ? 'ASC' : 'DESC',
            'post_status'         => 'publish',
            'exclude'             => array_merge( array( $post_id ), $manual, $settings['exclude_posts'] ), // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- small list: the current post, hand-picked and excluded IDs
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
            'suppress_filters'    => false, // lets WPML and Polylang keep the current language
        );

        // category__not_in would join the OR group above and match everything, so AND it explicitly
        if ( $settings['exclude_categories'] ) {
            $args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- same query, plus the excluded categories
                'relation' => 'AND',
                $tax_query,
                array(
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => $settings['exclude_categories'],
                    'operator' => 'NOT IN',
                ),
            );
        }
        if ( $settings['max_age'] ) {
            $args['date_query'] = array( array( 'after' => $settings['max_age'] . ' months ago' ) );
        }

        $args           = apply_filters('alrp_related_posts_query_args', $args, $post_id, $settings);
        $args['fields'] = 'ids';

        // the order is part of the key: orders that share a query (relevance, add-on orders) sort the result differently
        $key = $post_id . '|' . $orderby . '|' . wp_json_encode( $args );
        $ids = cache::get( $key );
        if ( ! is_array( $ids ) ) {
            $ids = get_posts( $args );
            if ( 'relevance' === $orderby ) {
                $ids = $this->sort_by_relevance( $ids, $term_ids );
            }
            cache::set( $key, $ids );
        }

        return $ids;
    }

    private function taxonomies($post_type, $relation) {
        if ( 'all' === $relation ) {
            $taxonomies = array();
            foreach ( get_object_taxonomies( $post_type, 'objects' ) as $taxonomy ) {
                if ( $taxonomy->public && 'post_format' !== $taxonomy->name ) {
                    $taxonomies[] = $taxonomy->name;
                }
            }
            return $taxonomies;
        }

        $taxonomies = array();
        if ( in_array( $relation, array('category', 'both'), true ) ) {
            $taxonomies[] = 'category';
        }
        if ( in_array( $relation, array('tag', 'both'), true ) ) {
            $taxonomies[] = 'post_tag';
        }
        return array_values( array_filter( $taxonomies, function ($taxonomy) use ($post_type) {
            return is_object_in_taxonomy( $post_type, $taxonomy );
        } ) );
    }

    // more shared terms ranks higher, a shared tag counts double since tags are more specific
    private function sort_by_relevance($ids, $term_ids) {
        if ( ! $ids ) {
            return $ids;
        }

        $weights = apply_filters('alrp_relevance_weights', array( 'category' => 1, 'post_tag' => 2 ));
        $wanted  = array();
        foreach ( $term_ids as $taxonomy => $terms ) {
            foreach ( $terms as $term_id ) {
                $wanted[ $term_id ] = $weights[ $taxonomy ] ?? 1;
            }
        }

        $score = array_fill_keys( $ids, 0 );
        $rows  = wp_get_object_terms( $ids, array_keys( $term_ids ), array( 'fields' => 'all_with_object_id' ) );
        foreach ( is_array($rows) ? $rows : array() as $row ) {
            if ( isset( $wanted[ $row->term_id ] ) ) {
                $score[ $row->object_id ] += $wanted[ $row->term_id ];
            }
        }

        // ties keep the newest first
        $position = array_flip( $ids );
        usort( $ids, function ($a, $b) use ($score, $position) {
            return $score[ $b ] <=> $score[ $a ] ?: $position[ $a ] <=> $position[ $b ];
        } );

        return $ids;
    }

    public function render($post_id, $settings) {
        if ( $this->rendering ) {
            return '';
        }

        $posts = $this->get_related_posts( $post_id, $settings );
        if ( ! $posts ) {
            return '';
        }

        $this->rendering = true;
        wp_enqueue_style('alrp_main');

        // add-on layouts can switch parts of the card on or off
        $settings = apply_filters( 'alrp_render_settings', $settings, $post_id );

        // minimal is a plain title list
        if ( 'minimal' === $settings['layout'] ) {
            $settings = array_merge( $settings, array( 'show_image' => 0, 'show_excerpt' => 0, 'show_author' => 0, 'show_read_more' => 0 ) );
        }

        $terms = get_the_terms( $post_id, 'category' );
        if ( ! is_array($terms) ) {
            $terms = array();
        }

        ob_start();
        // data-alrp-source and data-alrp-post let add-ons (e.g. Pro analytics) tell which link was clicked
        printf( '<div class="%1$s" style="%2$s" data-alrp-source="%3$d">', esc_attr( $this->wrapper_classes( $settings ) ), esc_attr( $this->wrapper_style( $settings ) ), (int) $post_id );
        if ( $settings['show_categories'] && $terms ) {
            include $this->template_path('category.php', $settings);
        }
        include $this->template_path('posts.php', $settings);
        echo '</div>';
        $html = ob_get_clean();

        $this->rendering = false;

        return apply_filters('alrp_related_posts_html', $html, $posts, $post_id, $settings);
    }

    private function wrapper_classes($settings) {
        $classes = array(
            'alrp_wrapper',
            'alrp_layout_' . $settings['layout'],
            'alrp_ratio_' . $settings['image_ratio'],
            'alrp_card_' . $settings['card_style'],
        );
        if ( $settings['hover_zoom'] ) {
            $classes[] = 'alrp_hover_zoom';
        }
        if ( ! $settings['show_image'] ) {
            $classes[] = 'alrp_no_image';
        }
        if ( $settings['card_bg'] ) {
            $classes[] = 'alrp_has_bg';
        }
        return implode( ' ', apply_filters( 'alrp_wrapper_classes', $classes, $settings ) );
    }

    private function wrapper_style($settings) {
        $vars = array(
            '--alrp-title-color' => $settings['title_color'],
            '--alrp-text-color'  => $settings['text_color'],
            '--alrp-card-bg'     => $settings['card_bg'],
            '--alrp-radius'      => $settings['radius'] ? $settings['radius'] . 'px' : '',
        );
        $vars = apply_filters( 'alrp_wrapper_style_vars', $vars, $settings );

        $style = '';
        foreach ( array_filter( $vars ) as $name => $value ) {
            $style .= $name . ':' . $value . ';';
        }
        return $style;
    }

    // themes can override a template by copying it to yourtheme/softicon-related-posts/
    private function template_path($name, $settings = array()) {
        $path = locate_template( 'softicon-related-posts/' . $name );
        return apply_filters('alrp_template_path', $path ?: ALRP_PLUGIN_PATH . 'templates/' . $name, $name, $settings);
    }
}
