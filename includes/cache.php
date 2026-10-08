<?php
namespace ALRP_Related_Posts;
if ( !defined('ABSPATH') ) { exit;}

class cache {

    const VERSION_OPTION = 'alrp_cache_version';

    public function __construct()
    {
        // any change that can alter a related list bumps the version, old entries simply expire
        add_action('transition_post_status', array( $this, 'on_status' ), 10, 3);
        add_action('deleted_post', array( __CLASS__, 'flush' ));
        add_action('set_object_terms', array( $this, 'on_terms_change' ), 10, 6);
        add_action('edited_term', array( $this, 'on_term_edit' ), 10, 3);
        add_action('delete_term', array( $this, 'on_term_edit' ), 10, 3);
    }

    public static function flush() {
        update_option( self::VERSION_OPTION, (int) get_option( self::VERSION_OPTION, 0 ) + 1 );
    }

    // drafts never appear in related lists, so only changes to or from published flush
    public function on_status($new_status, $old_status, $post) {
        if ( ( 'publish' === $new_status || 'publish' === $old_status ) && is_post_type_viewable( $post->post_type ) ) {
            self::flush();
        }
    }

    public function on_terms_change($object_id, $terms, $tt_ids, $taxonomy) {
        $tax = get_taxonomy( $taxonomy );
        if ( $tax && $tax->public ) {
            self::flush();
        }
    }

    public function on_term_edit($term_id, $tt_id, $taxonomy) {
        $this->on_terms_change( 0, array(), array(), $taxonomy );
    }

    public static function get($key) {
        return get_transient( self::name($key) );
    }

    public static function set($key, $ids) {
        set_transient( self::name($key), $ids, (int) apply_filters('alrp_cache_ttl', DAY_IN_SECONDS) );
    }

    private static function name($key) {
        return 'alrp_' . md5( get_option( self::VERSION_OPTION, 0 ) . '|' . $key );
    }
}
