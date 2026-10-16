<?php
namespace ALRP_Related_Posts;
if ( !defined('ABSPATH') ) { exit;}

// first activation: a one-time opt-in dialog on the Plugins page, then the settings page
class onboarding {

    const FLAG   = 'alrp_onboarding';
    const CHOICE = 'alrp_optin_choice';
    const ACTION = 'alrp_optin';

    public function __construct()
    {
        // the dialog replaces Freemius' redirect to its full-page opt-in screen
        alrp_fs()->add_filter('redirect_on_activation', '__return_false');
        add_action('admin_init', array( $this, 'maybe_start' ));
        add_action('admin_post_' . self::ACTION, array( $this, 'save' ));
    }

    public static function activate($network_wide) {
        if ( ! $network_wide ) {
            set_transient( self::FLAG, 1, 5 * MINUTE_IN_SECONDS );
        }
    }

    public function maybe_start() {
        global $pagenow;

        if ( ! get_transient(self::FLAG) || 'plugins.php' !== $pagenow || wp_doing_ajax() || is_network_admin() || ! current_user_can('manage_options') ) {
            return;
        }
        delete_transient(self::FLAG);

        $fs = alrp_fs();
        if ( ! $fs->is_activation_mode() ) {
            return;
        }

        // asked before: Freemius resets "skipped" when the same version is activated again, keep the answer
        if ( 'skip' === get_option(self::CHOICE) ) {
            $fs->skip_connection();
            return;
        }

        // bulk activation keeps the plain Plugins page, the "Opt In" link stays in the plugin row
        if ( isset( $_GET['activate-multi'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- only checks which page WordPress redirected to
            return;
        }

        add_action('admin_enqueue_scripts', array( $this, 'enqueue_assets' ));
        add_action('admin_footer', array( $this, 'render' ));
    }

    public function enqueue_assets() {
        wp_add_inline_style('wp-admin', self::styles());
        wp_add_inline_script('common', self::script());
    }

    public function save() {
        if ( ! current_user_can('manage_options') ) {
            wp_die( esc_html__('Sorry, you are not allowed to do that.', 'softicon-related-posts'), 403 );
        }
        check_admin_referer(self::ACTION);

        $choice = isset( $_POST['choice'] ) && 'allow' === $_POST['choice'] ? 'allow' : 'skip';
        $fs     = alrp_fs();

        if ( $fs->is_activation_mode() ) {
            if ( 'allow' === $choice ) {
                // sends the current admin's name and email; on an API error Freemius falls back to "skipped"
                $fs->opt_in( false, false, false, false, false, false, false, null, array(), false );
            } else {
                $fs->skip_connection();
            }
        }
        update_option( self::CHOICE, $choice, false );

        wp_safe_redirect( admin_settings::url() );
        exit;
    }

    private static function styles() {
        return '.alrp-optin{position:fixed;inset:0;z-index:100000;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(15,23,42,.6)}
.alrp-optin-box{box-sizing:border-box;width:100%;max-width:520px;max-height:calc(100vh - 32px);overflow:auto;padding:32px;border-radius:12px;background:#fff;box-shadow:0 20px 50px rgba(15,23,42,.3);text-align:center}
.alrp-optin-box img{width:64px;height:64px;border-radius:14px}
.alrp-optin-box h2{margin:16px 0 8px;font-size:20px;line-height:1.35;color:#1e1b4b}
.alrp-optin-box p{margin:0 0 20px;font-size:14px;line-height:1.6;color:#475569}
.alrp-optin-allow{width:100%;padding:12px 16px;border:0;border-radius:8px;background:#4f46e5;color:#fff;font-size:15px;font-weight:600;cursor:pointer}
.alrp-optin-allow:hover,.alrp-optin-allow:focus{background:#4338ca}
.alrp-optin-allow:focus-visible{outline:2px solid #a5b4fc;outline-offset:2px}
.alrp-optin-allow[disabled]{opacity:.7;cursor:wait}
.alrp-optin-links{display:flex;justify-content:center;gap:12px;margin-top:14px;font-size:13px;color:#cbd5e1}
.alrp-optin-links button{padding:0;border:0;background:none;color:#64748b;font-size:13px;text-decoration:underline;cursor:pointer}
.alrp-optin-links button:hover,.alrp-optin-links button:focus{color:#1e1b4b}
.alrp-optin-details{margin:18px 0 0;padding:16px 18px;border-radius:8px;background:#f1f5f9;text-align:left;font-size:13px;line-height:1.55;color:#475569}
.alrp-optin-details[hidden]{display:none}
.alrp-optin-details ul{margin:0 0 10px 18px;list-style:disc}
.alrp-optin-details li{margin:0 0 6px}
.alrp-optin-details p{margin:0;font-size:13px}
@media (max-width:600px){.alrp-optin-box{padding:24px 20px}}';
    }

    private static function script() {
        return 'document.addEventListener("DOMContentLoaded",function(){var d=document.getElementById("alrp-optin");if(!d){return;}
var form=d.querySelector("form"),more=d.querySelector(".alrp-optin-more"),details=document.getElementById("alrp-optin-details"),allow=d.querySelector(".alrp-optin-allow");
allow.focus();
more.addEventListener("click",function(){details.hidden=!details.hidden;more.setAttribute("aria-expanded",details.hidden?"false":"true");});
form.addEventListener("submit",function(e){var b=e.submitter;if(b&&b===allow){allow.textContent=allow.getAttribute("data-busy");}
var c=document.createElement("input");c.type="hidden";c.name="choice";c.value=b?b.value:"skip";form.appendChild(c);
form.querySelectorAll("button").forEach(function(x){x.disabled=true;});});});';
    }

    public function render() {
        ?>
        <div class="alrp-optin" id="alrp-optin" role="dialog" aria-modal="true" aria-labelledby="alrp-optin-title">
            <div class="alrp-optin-box">
                <img src="<?php echo esc_url( ALRP_PLUGIN_ASSETS . 'images/icon-128x128.png' ); ?>" alt="">
                <h2 id="alrp-optin-title"><?php esc_html_e('Never miss an important update', 'softicon-related-posts'); ?></h2>
                <p><?php esc_html_e('Get emails about security fixes, new features and special offers for SoftIcon Related Posts, and share a few basic details about your site so we can keep the plugin compatible and make it better. Your posts and content are never shared.', 'softicon-related-posts'); ?></p>

                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                    <input type="hidden" name="action" value="<?php echo esc_attr( self::ACTION ); ?>">
                    <?php wp_nonce_field(self::ACTION); ?>
                    <button type="submit" name="choice" value="allow" class="alrp-optin-allow" data-busy="<?php esc_attr_e('Connecting…', 'softicon-related-posts'); ?>"><?php esc_html_e('Allow & Continue', 'softicon-related-posts'); ?> &rarr;</button>
                    <div class="alrp-optin-links">
                        <button type="button" class="alrp-optin-more" aria-expanded="false" aria-controls="alrp-optin-details"><?php esc_html_e('What is shared?', 'softicon-related-posts'); ?></button>
                        <span aria-hidden="true">|</span>
                        <button type="submit" name="choice" value="skip"><?php esc_html_e('Skip', 'softicon-related-posts'); ?></button>
                    </div>
                </form>

                <div class="alrp-optin-details" id="alrp-optin-details" hidden>
                    <ul>
                        <li><strong><?php esc_html_e('Your profile:', 'softicon-related-posts'); ?></strong> <?php esc_html_e('name and email address', 'softicon-related-posts'); ?></li>
                        <li><strong><?php esc_html_e('Your site:', 'softicon-related-posts'); ?></strong> <?php esc_html_e('homepage URL and title, WordPress and PHP versions, site language', 'softicon-related-posts'); ?></li>
                        <li><strong><?php esc_html_e('This plugin:', 'softicon-related-posts'); ?></strong> <?php esc_html_e('its version, and whether it is active or uninstalled', 'softicon-related-posts'); ?></li>
                        <li><strong><?php esc_html_e('Plugins and themes:', 'softicon-related-posts'); ?></strong> <?php esc_html_e('names, versions and whether they are active, to spot conflicts', 'softicon-related-posts'); ?></li>
                    </ul>
                    <p>
                        <?php
                        printf(
                            /* translators: %s: link to the Freemius privacy policy */
                            esc_html__('The data is handled by Freemius, see their %s. Skip and nothing is sent; the plugin works the same either way.', 'softicon-related-posts'),
                            '<a href="https://freemius.com/privacy/" target="_blank" rel="noopener noreferrer">' . esc_html__('privacy policy', 'softicon-related-posts') . '</a>'
                        );
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }
}
