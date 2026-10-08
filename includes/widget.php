<?php
namespace ALRP_Related_Posts;
if ( !defined('ABSPATH') ) { exit;}

// classic sidebar widget, block themes and block widget areas can use the block instead
class widget extends \WP_Widget {

    public function __construct()
    {
        parent::__construct(
            'alrp_widget',
            __('SoftIcon Related Posts', 'softicon-related-posts'),
            array( 'description' => __('Related posts for the post being viewed.', 'softicon-related-posts') )
        );
    }

    public static function register() {
        register_widget( __CLASS__ );
    }

    private function defaults() {
        return array(
            'title'      => __('Related Posts', 'softicon-related-posts'),
            'posts'      => 5,
            'layout'     => 'minimal',
            'show_date'  => 0,
            'show_image' => 1,
        );
    }

    public function widget($args, $instance) {
        if ( ! is_singular() ) {
            return;
        }

        $instance = wp_parse_args( $instance, $this->defaults() );

        // the widget title replaces the section heading, sidebars get one column
        $html = get_category_show_posts::instance()->render_atts( array(
            'post_id'         => get_queried_object_id(),
            'posts'           => $instance['posts'],
            'layout'          => $instance['layout'],
            'show_date'       => $instance['show_date'],
            'show_image'      => $instance['show_image'],
            'show_excerpt'    => 0,
            'show_author'     => 0,
            'show_read_more'  => 0,
            'show_categories' => 0,
            'columns'         => 1,
            'title'           => '',
        ) );

        if ( ! $html ) {
            return;
        }

        echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme markup
        if ( $instance['title'] ) {
            echo $args['before_title'] . esc_html( apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme markup
        }
        echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the templates
        echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme markup
    }

    public function form($instance) {
        $instance = wp_parse_args( $instance, $this->defaults() );
        $layouts  = admin_settings::choices()['layout'];
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php esc_html_e('Title:', 'softicon-related-posts'); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id('posts') ); ?>"><?php esc_html_e('Number of posts:', 'softicon-related-posts'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id('posts') ); ?>" name="<?php echo esc_attr( $this->get_field_name('posts') ); ?>" type="number" min="1" max="24" value="<?php echo (int) $instance['posts']; ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id('layout') ); ?>"><?php esc_html_e('Layout:', 'softicon-related-posts'); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id('layout') ); ?>" name="<?php echo esc_attr( $this->get_field_name('layout') ); ?>">
                <?php foreach ( $layouts as $value => $label ) : ?>
                    <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $instance['layout'], $value ); ?>><?php echo esc_html( $label ); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name('show_image') ); ?>" value="1" <?php checked( $instance['show_image'] ); ?>> <?php esc_html_e('Show image (grid and list)', 'softicon-related-posts'); ?></label><br>
            <label><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name('show_date') ); ?>" value="1" <?php checked( $instance['show_date'] ); ?>> <?php esc_html_e('Show date', 'softicon-related-posts'); ?></label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $layouts = admin_settings::choices()['layout'];

        return array(
            'title'      => sanitize_text_field( $new_instance['title'] ?? '' ),
            'posts'      => min( 24, max( 1, absint( $new_instance['posts'] ?? 5 ) ) ),
            'layout'     => isset( $new_instance['layout'], $layouts[ $new_instance['layout'] ] ) ? $new_instance['layout'] : 'minimal',
            'show_date'  => empty( $new_instance['show_date'] ) ? 0 : 1,
            'show_image' => empty( $new_instance['show_image'] ) ? 0 : 1,
        );
    }
}
