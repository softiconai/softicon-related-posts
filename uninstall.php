<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'alrp_settings' );
delete_post_meta_by_key( '_alrp_hide' );
delete_post_meta_by_key( '_alrp_manual_ids' );
delete_option( 'alrp_cache_version' );

global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_alrp\_%' OR option_name LIKE '\_transient\_timeout\_alrp\_%'" );
