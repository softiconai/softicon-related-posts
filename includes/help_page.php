<?php
namespace ALRP_Related_Posts;
if ( !defined('ABSPATH') ) { exit;}

class help_page {

    const PAGE = 'alrp-help';

    public function __construct()
    {
        add_action('admin_enqueue_scripts', array( $this, 'enqueue_assets' ));
    }

    public function enqueue_assets($hook) {
        if ( false === strpos( $hook, self::PAGE ) ) {
            return;
        }
        wp_add_inline_style('wp-admin', videos::styles() . self::styles());
    }

    private static function styles() {
        return '.alrp-help{max-width:1100px}
.alrp-help-cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;margin:16px 0 24px}
.alrp-help-card{background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:16px 20px}
.alrp-help-card h2{margin-top:0;font-size:16px}
.alrp-help-card ol,.alrp-help-card ul{margin-left:18px}
.alrp-help-card code{display:inline-block;margin:2px 0;white-space:normal;word-break:break-word}
.alrp-help-links a{display:block;margin:6px 0}';
    }

    public static function render() {
        if ( ! current_user_can('manage_options') ) {
            return;
        }
        $settings_url = admin_settings::url();
        ?>
        <div class="wrap alrp-help">
            <h1><?php esc_html_e('Related Posts: Help & Videos', 'softicon-related-posts'); ?></h1>

            <div class="alrp-help-cards">
                <div class="alrp-help-card">
                    <h2><?php esc_html_e('Getting started', 'softicon-related-posts'); ?></h2>
                    <ol>
                        <li><?php esc_html_e('Related posts already appear below every post. Open one of your posts to see them.', 'softicon-related-posts'); ?></li>
                        <li>
                            <?php
                            printf(
                                /* translators: %s: link to the settings page */
                                esc_html__('Choose how posts are matched, ordered and displayed in %s.', 'softicon-related-posts'),
                                '<a href="' . esc_url( $settings_url ) . '">' . esc_html__('Related Posts → Settings', 'softicon-related-posts') . '</a>'
                            );
                            ?>
                        </li>
                        <li><?php esc_html_e('Want them somewhere else? Add the Related Posts block, or use the shortcode below.', 'softicon-related-posts'); ?></li>
                    </ol>
                </div>

                <div class="alrp-help-card">
                    <h2><?php esc_html_e('Related Posts block', 'softicon-related-posts'); ?></h2>
                    <p><?php esc_html_e('In the block editor, click + and search for "Related Posts". The General and Style tabs in the sidebar change only that block; options you leave untouched follow the settings page.', 'softicon-related-posts'); ?></p>
                    <p><?php esc_html_e('Block theme? Set Position to "Manual (shortcode only)", then add the block once to the Single Posts template in Appearance → Editor.', 'softicon-related-posts'); ?></p>
                </div>

                <div class="alrp-help-card">
                    <h2><?php esc_html_e('Shortcode', 'softicon-related-posts'); ?></h2>
                    <p><?php esc_html_e('Every attribute is optional and falls back to the settings page.', 'softicon-related-posts'); ?></p>
                    <p><code>[softicon_related_posts]</code></p>
                    <p><code>[softicon_related_posts posts="4" columns="2" relation="tag" orderby="relevance" layout="list" title="You may also like"]</code></p>
                </div>

                <div class="alrp-help-card alrp-help-links">
                    <h2><?php esc_html_e('Need more help?', 'softicon-related-posts'); ?></h2>
                    <a href="https://wordpress.org/plugins/softicon-related-posts/#faq" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Frequently asked questions', 'softicon-related-posts'); ?></a>
                    <a href="https://wordpress.org/support/plugin/softicon-related-posts/" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Support forum', 'softicon-related-posts'); ?></a>
                    <a href="https://github.com/softiconai/softicon-related-posts" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Source code on GitHub', 'softicon-related-posts'); ?></a>
                    <a href="https://wordpress.org/support/plugin/softicon-related-posts/reviews/#new-post" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Enjoying it? Leave a review', 'softicon-related-posts'); ?></a>
                </div>
            </div>

            <?php videos::render(); ?>
        </div>
        <?php
    }
}
