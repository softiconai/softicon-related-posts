<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// unset attributes fall back to the settings page
$alrp_map = array(
    'posts'          => 'posts',
    'columns'        => 'columns',
    'relation'       => 'relation',
    'orderby'        => 'orderby',
    'title'          => 'title',
    'showCategories' => 'show_categories',
    'showImage'      => 'show_image',
    'showExcerpt'    => 'show_excerpt',
    'showDate'       => 'show_date',
    'showAuthor'     => 'show_author',
    'showReadMore'   => 'show_read_more',
    'layout'         => 'layout',
    'imageRatio'     => 'image_ratio',
    'cardStyle'      => 'card_style',
    'radius'         => 'radius',
    'hoverZoom'      => 'hover_zoom',
    'titleColor'     => 'title_color',
    'textColor'      => 'text_color',
    'cardBg'         => 'card_bg',
);

// widget areas and template parts have no postId context, use the post being viewed
$alrp_atts = array( 'post_id' => $block->context['postId'] ?? ( is_singular() ? get_queried_object_id() : get_the_ID() ) );
foreach ( $alrp_map as $alrp_attr => $alrp_key ) {
    if ( isset( $attributes[ $alrp_attr ] ) ) {
        $alrp_atts[ $alrp_key ] = $attributes[ $alrp_attr ];
    }
}

$alrp_html = ALRP_Related_Posts\get_category_show_posts::instance()->render_atts( $alrp_atts );

if ( $alrp_html ) {
    printf(
        '<div %1$s>%2$s</div>',
        get_block_wrapper_attributes(), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes it
        $alrp_html // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the templates
    );
}
