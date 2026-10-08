<?php
namespace ALRP_Related_Posts;
if ( !defined('ABSPATH') ) { exit;}

class post_meta_box {

    const META_KEY   = '_alrp_hide';
    const MANUAL_KEY = '_alrp_manual_ids';
    const MAX_MANUAL = 24;

    public function __construct()
    {
        add_action('add_meta_boxes', array( $this, 'add_meta_box' ));
        add_action('save_post', array( $this, 'save' ));
        add_action('admin_enqueue_scripts', array( $this, 'enqueue_assets' ));
    }

    public static function is_hidden($post_id) {
        return (bool) get_post_meta( $post_id, self::META_KEY, true );
    }

    public static function manual_ids($post_id) {
        $ids = get_post_meta( $post_id, self::MANUAL_KEY, true );
        return is_array($ids) ? array_map( 'absint', $ids ) : array();
    }

    public function add_meta_box() {
        add_meta_box('alrp_meta_box', __('Related Posts', 'softicon-related-posts'), array( $this, 'render' ), admin_settings::enabled_post_types(), 'side', 'low');
    }

    public function enqueue_assets($hook) {
        if ( ! in_array( $hook, array('post.php', 'post-new.php'), true ) || ! in_array( get_current_screen()->post_type, admin_settings::enabled_post_types(), true ) ) {
            return;
        }
        wp_enqueue_script('alrp_meta_box', ALRP_PLUGIN_ASSETS . 'js/meta-box.js', array('jquery', 'wp-api-fetch', 'wp-url'), ALRP_PLUGIN_VERSION, true);
        wp_localize_script('alrp_meta_box', 'alrpMetaBox', array(
            'remove'    => __('Remove', 'softicon-related-posts'),
            'noResults' => __('No posts found', 'softicon-related-posts'),
            'max'       => self::MAX_MANUAL,
        ));
    }

    public function render($post) {
        wp_nonce_field('alrp_save_meta', 'alrp_meta_nonce');
        $manual = self::manual_ids( $post->ID );
        ?>
        <p>
            <label>
                <input type="checkbox" name="alrp_hide" value="1" <?php checked( self::is_hidden( $post->ID ) ); ?>>
                <?php esc_html_e('Hide related posts on this post', 'softicon-related-posts'); ?>
            </label>
        </p>
        <div class="alrp-manual" data-post-id="<?php echo (int) $post->ID; ?>" data-post-type="<?php echo esc_attr( $post->post_type ); ?>">
            <p><strong><?php esc_html_e('Pick related posts', 'softicon-related-posts'); ?></strong><br>
            <span class="description"><?php esc_html_e('Shown first. Remaining slots are filled automatically.', 'softicon-related-posts'); ?></span></p>
            <input type="hidden" name="alrp_manual_ids" value="<?php echo esc_attr( implode( ',', $manual ) ); ?>">
            <input type="search" class="widefat alrp-manual-search" placeholder="<?php esc_attr_e('Search posts…', 'softicon-related-posts'); ?>" autocomplete="off">
            <ul class="alrp-manual-results" style="margin:4px 0;max-height:160px;overflow:auto"></ul>
            <ol class="alrp-manual-list" style="margin:8px 0 0 20px">
                <?php foreach ( $manual as $id ) : ?>
                    <li data-id="<?php echo (int) $id; ?>">
                        <?php echo esc_html( get_the_title( $id ) ?: '#' . $id ); ?>
                        <button type="button" class="button-link alrp-manual-remove" aria-label="<?php esc_attr_e('Remove', 'softicon-related-posts'); ?>">&times;</button>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
        <?php
    }

    public function save($post_id) {
        if ( ! isset( $_POST['alrp_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['alrp_meta_nonce'] ), 'alrp_save_meta' ) ) {
            return;
        }
        if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) || ! current_user_can('edit_post', $post_id) ) {
            return;
        }
        // since WP 6.4 the revision is saved after the post with the same $_POST, which would wipe the picks
        if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
            return;
        }

        if ( ! empty( $_POST['alrp_hide'] ) ) {
            update_post_meta( $post_id, self::META_KEY, 1 );
        } else {
            delete_post_meta( $post_id, self::META_KEY );
        }

        // keep only other existing posts of the same type, order as picked
        $type = get_post_type( $post_id );
        $ids  = wp_parse_id_list( sanitize_text_field( wp_unslash( $_POST['alrp_manual_ids'] ?? '' ) ) );
        $ids  = array_filter( $ids, function ($id) use ($post_id, $type) {
            return $id !== $post_id && $type === get_post_type( $id ) && current_user_can( 'read_post', $id );
        } );
        $ids = array_slice( array_values( $ids ), 0, self::MAX_MANUAL );

        if ( $ids ) {
            update_post_meta( $post_id, self::MANUAL_KEY, $ids );
        } else {
            delete_post_meta( $post_id, self::MANUAL_KEY );
        }
    }
}
