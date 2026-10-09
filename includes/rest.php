<?php
namespace ALRP_Related_Posts;
if ( !defined('ABSPATH') ) { exit;}

// REST routes for the React settings page
class rest {

    const NS = 'alrp/v1';

    public function __construct()
    {
        add_action('rest_api_init', array( $this, 'routes' ));
    }

    public function can_manage() {
        return current_user_can('manage_options');
    }

    public function routes() {
        $settings_arg = array(
            'settings' => array(
                'type'     => 'object',
                'required' => true,
            ),
        );

        register_rest_route( self::NS, '/settings', array(
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_settings' ),
                'permission_callback' => array( $this, 'can_manage' ),
            ),
            array(
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'save_settings' ),
                'permission_callback' => array( $this, 'can_manage' ),
                'args'                => $settings_arg,
            ),
        ) );

        register_rest_route( self::NS, '/preview', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'preview' ),
            'permission_callback' => array( $this, 'can_manage' ),
            'args'                => $settings_arg,
        ) );
    }

    public function get_settings() {
        return rest_ensure_response( admin_settings::get() );
    }

    public function save_settings( \WP_REST_Request $request ) {
        $clean = admin_settings::sanitize( $request->get_param('settings') );
        update_option( admin_settings::OPTION, $clean );
        return rest_ensure_response( admin_settings::get() );
    }

    // renders unsaved settings against the recent post with the most related posts, so every option shows its effect
    public function preview( \WP_REST_Request $request ) {
        $settings = admin_settings::sanitize( $request->get_param('settings') );
        $types    = $settings['post_types'] ? $settings['post_types'] : array('post');
        $renderer = get_category_show_posts::instance();

        $candidates = get_posts( array(
            'post_type'      => $types,
            'post_status'    => 'publish',
            'posts_per_page' => 12,
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ) );

        $best = 0;
        $most = 0;
        foreach ( $candidates as $post_id ) {
            $count = count( $renderer->get_related_posts( $post_id, $settings ) );
            if ( $count > $most ) {
                $best = $post_id;
                $most = $count;
            }
            if ( $most >= $settings['posts_per_page'] ) {
                break;
            }
        }

        if ( ! $best ) {
            return rest_ensure_response( array( 'html' => '', 'post' => '', 'link' => '' ) );
        }

        return rest_ensure_response( array(
            'html' => $renderer->render( $best, $settings ),
            'post' => get_the_title( $best ),
            'link' => get_permalink( $best ),
        ) );
    }
}
