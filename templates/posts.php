<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>

<div class="alrp_main_section">
    <?php if ( $settings['title'] ) : ?>
        <h3><?php echo esc_html( $settings['title'] ); ?></h3>
    <?php endif; ?>
    <div class="all_posts" style="--alrp-columns: <?php echo (int) $settings['columns']; ?>;">
        <?php foreach($posts as $post):
            $permalink = get_permalink( $post->ID );
            $image     = '';
            if ( $settings['show_image'] ) {
                $image_id = get_post_thumbnail_id( $post ) ?: $settings['fallback_image_id'];
                $image    = $image_id ? wp_get_attachment_image( $image_id, $settings['image_size'], false, array( 'class' => 'post-image', 'alt' => $post->post_title, 'loading' => 'lazy' ) ) : '';
                if ( ! $image && $settings['fallback_image'] ) {
                    $image = '<img class="post-image" src="' . esc_url( $settings['fallback_image'] ) . '" alt="' . esc_attr( $post->post_title ) . '" loading="lazy">';
                }
            }
            $user = get_user_by('id', $post->post_author );
            // core's excerpt pipeline, so membership and paywall filters apply
            $text = get_the_excerpt( $post );
        ?>
            <div class="single_item">
                <?php if ( $settings['show_image'] ) : ?>
                    <a class="alrp_img_link" href="<?php echo esc_url( $permalink ); ?>">
                        <div class="img">
                            <?php if ( $image ) : ?>
                                <?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by wp_get_attachment_image() or escaped above ?>
                            <?php else : ?>
                                <span class="img_placeholder" aria-hidden="true"></span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endif; ?>
                <a class="title" href="<?php echo esc_url( $permalink ); ?>">
                    <?php echo esc_html($post->post_title); ?>
                </a>
                <?php if ( $settings['show_date'] ) : ?>
                    <time class="date" datetime="<?php echo esc_attr( get_the_date( 'c', $post ) ); ?>">
                        <?php echo esc_html( get_the_date( '', $post ) ); ?>
                    </time>
                <?php endif; ?>
                <?php if ( $settings['show_excerpt'] && ! post_password_required( $post ) ) : ?>
                    <p class="content">
                    <?php echo esc_html( wp_trim_words( strip_shortcodes( $text ), $settings['excerpt_length'], '...' ) ); ?>
                    </p>
                <?php endif; ?>
                <?php if ( $settings['show_read_more'] && $settings['read_more_text'] ) : ?>
                    <a class="read_more_btn" href="<?php echo esc_url( $permalink ) ?>"><?php echo esc_html( $settings['read_more_text'] ); ?></a>
                <?php endif; ?>
                <?php if ( $settings['show_author'] && $user ) : ?>
                    <div class="profile">
                        <img src="<?php echo esc_url( get_avatar_url( $user->ID ) ); ?>" alt="<?php echo esc_attr( $user->display_name ); ?>">
                        <h4><?php echo esc_html($user->display_name);?></h4>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
