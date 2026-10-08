<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>
<div class="alrp_category">
    <h3><?php esc_html_e('Categories', 'softicon-related-posts'); ?></h3>
    <ul>
        <?php foreach($terms as $term):
            $link = get_term_link( $term, 'category' );
            if ( is_wp_error( $link ) ) {
                continue;
            }
        ?>
             <li>
                <a href="<?php echo esc_url( $link ); ?>">
                    <?php echo esc_html( $term->name ); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
