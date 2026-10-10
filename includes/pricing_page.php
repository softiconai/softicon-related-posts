<?php
namespace ALRP_Related_Posts;
if ( !defined('ABSPATH') ) { exit;}

// Free vs Pro comparison and Pro pricing; buttons open the Freemius checkout in a new tab
class pricing_page {

    const PAGE     = 'alrp-pricing';
    const CHECKOUT = 'https://checkout.freemius.com/plugin/41135/?billing_cycle=lifetime&licenses=';

    public function __construct()
    {
        add_action('admin_enqueue_scripts', array( $this, 'enqueue_assets' ));
    }

    public static function url($anchor = '') {
        return admin_settings::url( self::PAGE ) . ( $anchor ? '#' . $anchor : '' );
    }

    // the Pro add-on reports its license here, so the page can say Pro is already active
    public static function pro_active() {
        return (bool) apply_filters('alrp_pro_license_active', false);
    }

    public static function plans() {
        return apply_filters('alrp_pricing_plans', array(
            array( 'name' => __('Single site', 'softicon-related-posts'), 'for' => __('For your own website', 'softicon-related-posts'), 'price' => '19.99', 'licenses' => '1', 'featured' => false ),
            array( 'name' => __('3 sites', 'softicon-related-posts'), 'for' => __('For freelancers and small studios', 'softicon-related-posts'), 'price' => '59.99', 'licenses' => '3', 'featured' => true ),
            array( 'name' => __('Unlimited sites', 'softicon-related-posts'), 'for' => __('For agencies: every site you build', 'softicon-related-posts'), 'price' => '199.99', 'licenses' => 'unlimited', 'featured' => false ),
        ));
    }

    // group => rows of [feature, in free, in Pro]
    public static function features() {
        return array(
            __('Related posts', 'softicon-related-posts') => array(
                array( __('Automatic related posts below every article', 'softicon-related-posts'), true, true ),
                array( __('Match by category, tag or any taxonomy', 'softicon-related-posts'), true, true ),
                array( __('"Most relevant" order and hand-picked posts', 'softicon-related-posts'), true, true ),
                array( __('Exclude categories, posts and older posts', 'softicon-related-posts'), true, true ),
                array( __('Smart order: keywords, tags, freshness and adjustable weights', 'softicon-related-posts'), false, true ),
                array( __('Yoast SEO and Rank Math cornerstone posts first', 'softicon-related-posts'), false, true ),
                array( __('Popular and Trending order', 'softicon-related-posts'), false, true ),
                array( __('Custom field and ACF rules (same city, similar price, upcoming events)', 'softicon-related-posts'), false, true ),
            ),
            __('Placement', 'softicon-related-posts') => array(
                array( __('After or before the content, block, shortcode and widget', 'softicon-related-posts'), true, true ),
                array( __('Pages, custom post types and WooCommerce products', 'softicon-related-posts'), true, true ),
                array( __('"Read also" boxes inside the article', 'softicon-related-posts'), false, true ),
                array( __('"Up next" slide-in as readers finish', 'softicon-related-posts'), false, true ),
                array( __('Display rules: only in chosen categories, hide on phones', 'softicon-related-posts'), false, true ),
            ),
            __('Design', 'softicon-related-posts') => array(
                array( __('Grid, list and minimal layouts', 'softicon-related-posts'), true, true ),
                array( __('Image ratio, cards, corners, hover zoom and colors', 'softicon-related-posts'), true, true ),
                array( __('Carousel, photo overlay, magazine and numbered layouts', 'softicon-related-posts'), false, true ),
                array( __('One-click design presets and custom CSS', 'softicon-related-posts'), false, true ),
            ),
            __('Insights and stores', 'softicon-related-posts') => array(
                array( __('Click analytics without cookies (views, clicks, click rate)', 'softicon-related-posts'), false, true ),
                array( __('Internal link report: find posts nothing links to', 'softicon-related-posts'), false, true ),
                array( __('WooCommerce: smarter related products with price and add to cart', 'softicon-related-posts'), false, true ),
                array( __('"Shop this post" and blog posts on product pages', 'softicon-related-posts'), false, true ),
                array( __('REST API for headless sites and apps', 'softicon-related-posts'), false, true ),
            ),
            __('Support', 'softicon-related-posts') => array(
                array( __('Updates', 'softicon-related-posts'), true, true ),
                array( __('Priority support', 'softicon-related-posts'), false, true ),
            ),
        );
    }

    public function enqueue_assets($hook) {
        if ( false !== strpos( $hook, self::PAGE ) ) {
            wp_add_inline_style('wp-admin', self::styles());
            wp_add_inline_script('common', self::script());
        }
    }


    // live filter for the feature table; plain JS, no dependencies
    private static function script() {
        return "document.addEventListener('DOMContentLoaded',function(){var f=document.getElementById('alrp-filter');if(!f)return;var rows=[].slice.call(document.querySelectorAll('.alrp-compare tbody tr'));var empty=document.getElementById('alrp-filter-empty');f.addEventListener('input',function(){var q=f.value.trim().toLowerCase(),shown=0,group=null,groupHas=false;rows.forEach(function(r){if(r.classList.contains('alrp-group')){if(group)group.hidden=!groupHas;group=r;groupHas=false;return;}var hit=!q||r.textContent.toLowerCase().indexOf(q)>-1;r.hidden=!hit;if(hit){groupHas=true;shown++;}});if(group)group.hidden=!groupHas;empty.hidden=shown>0;});});";
    }

    private static function styles() {
        return '.alrp-pricing{max-width:1120px;margin:10px auto 40px;padding-right:20px;color:#1e1b4b}
.alrp-pricing .screen-reader-text{position:absolute}
.alrp-eyebrow{display:inline-block;margin-bottom:10px;padding:4px 10px;border-radius:4px;background:#eef2ff;color:#4f46e5;font-size:12px;font-weight:700;letter-spacing:.08em}
.alrp-eyebrow.is-green{background:#dcfce7;color:#166534}
.alrp-head{margin:26px auto 26px;max-width:720px;text-align:center}
.alrp-head h1,.alrp-head h2{margin:0 0 10px;color:#1e1b4b;font-size:32px;font-weight:800;line-height:1.2}
.alrp-head p{margin:0;color:#475569;font-size:15px;line-height:1.6}
.alrp-head strong{color:#4f46e5}
.alrp-duo{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;margin-bottom:28px}
.alrp-card{padding:28px;border:1px solid #e2e8f0;border-radius:14px;background:#fff}
.alrp-card.is-pro{border:0;color:#fff;background:radial-gradient(120% 140% at 0% 0%,#4f46e5 0%,#312e81 55%,#1e1b4b 100%);box-shadow:0 18px 40px #312e8133}
.alrp-tag{display:inline-block;padding:3px 9px;border-radius:4px;background:#f1f5f9;color:#334155;font-size:11px;font-weight:700;letter-spacing:.08em}
.alrp-card.is-pro .alrp-tag{background:#ffffff26;color:#fff}
.alrp-card h3{margin:14px 0 6px;font-size:22px;color:inherit}
.alrp-card p{margin:0;color:#475569}
.alrp-card.is-pro p{color:#c7d2fe}
.alrp-price{display:flex;align-items:baseline;gap:6px;margin:16px 0 18px!important}
.alrp-price b{font-size:44px;line-height:1;color:#1e1b4b}
.alrp-card.is-pro .alrp-price b{color:#fff}
.alrp-price sup{align-self:flex-start;font-size:18px;font-weight:700;margin-top:4px}
.alrp-price small{font-size:13px;color:#64748b}
.alrp-card.is-pro .alrp-price small{color:#a5b4fc}
.alrp-cta{display:flex;align-items:center;justify-content:center;gap:6px;width:100%;box-sizing:border-box;padding:13px;border:0;border-radius:8px;font-size:15px;font-weight:700;text-decoration:none;cursor:pointer}
.alrp-cta.is-have{border:1px solid #86efac;background:#f0fdf4;color:#166534;cursor:default}
.alrp-cta.is-white{background:#fff;color:#4338ca}
.alrp-cta.is-white:hover,.alrp-cta.is-white:focus{color:#312e81;box-shadow:0 0 0 3px #a5b4fc}
.alrp-cta.is-solid{background:linear-gradient(135deg,#4f46e5,#4338ca);color:#fff}
.alrp-cta.is-solid:hover,.alrp-cta.is-solid:focus{color:#fff;box-shadow:0 0 0 3px #c7d2fe}
.alrp-box{overflow:hidden;margin-bottom:40px;border:1px solid #e2e8f0;border-radius:14px;background:#fff}
.alrp-box-head{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;padding:18px 24px;border-bottom:1px solid #e2e8f0}
.alrp-box-head h3{margin:0;font-size:17px}
.alrp-filter{position:relative}
.alrp-filter input{width:240px;max-width:100%;padding:8px 12px 8px 34px!important;border:1px solid #e2e8f0!important;border-radius:8px!important;background:#f8fafc}
.alrp-filter .dashicons{position:absolute;left:10px;top:9px;color:#94a3b8;font-size:18px}
.alrp-compare{width:100%;border-collapse:collapse}
.alrp-compare th,.alrp-compare td{padding:13px 24px;border-bottom:1px solid #f1f5f9;text-align:left;font-size:14px}
.alrp-compare thead th{background:#f8fafc;color:#334155;font-size:12px;letter-spacing:.06em;text-transform:uppercase}
.alrp-compare thead th.is-pro{color:#4f46e5}
.alrp-compare .alrp-col{width:90px;text-align:center}
.alrp-compare tr.alrp-group th{padding-top:20px;background:#fff;color:#4f46e5;font-size:12px;letter-spacing:.06em;text-transform:uppercase}
.alrp-compare a.alrp-pro-feature{color:inherit;text-decoration:none}
.alrp-compare a.alrp-pro-feature:hover,.alrp-compare a.alrp-pro-feature:focus{color:#4f46e5;text-decoration:underline}
.alrp-pro-tag{display:inline-block;margin-left:8px;padding:1px 7px;border-radius:4px;background:#eef2ff;color:#4f46e5;font-size:10px;font-weight:700;vertical-align:1px}
.alrp-check,.alrp-dash{display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:5px;font-size:15px;font-weight:700}
.alrp-check{background:#dcfce7;color:#16a34a}
.alrp-dash{background:#f1f5f9;color:#94a3b8}
.alrp-empty{margin:0;padding:24px;text-align:center;color:#64748b}
.alrp-plans{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));align-items:stretch;gap:20px;margin-bottom:28px}
.alrp-plan{position:relative;display:flex;flex-direction:column;padding:28px;border:1px solid #e2e8f0;border-radius:14px;background:#fff}
.alrp-plan.is-featured{border:0;color:#fff;background:radial-gradient(120% 140% at 0% 0%,#4f46e5 0%,#312e81 55%,#1e1b4b 100%);box-shadow:0 18px 40px #312e8133;transform:translateY(-8px)}
.alrp-badge{position:absolute;top:-12px;left:50%;transform:translateX(-50%);padding:4px 12px;border-radius:4px;background:linear-gradient(135deg,#22d3ee,#a5b4fc);color:#1e1b4b;font-size:11px;font-weight:800;letter-spacing:.08em;white-space:nowrap}
.alrp-plan h3{margin:0 0 4px;font-size:19px;color:inherit}
.alrp-plan .alrp-for{color:#64748b}
.alrp-plan.is-featured .alrp-for,.alrp-plan.is-featured .alrp-plan-note{color:#c7d2fe}
.alrp-plan.is-featured .alrp-price b{color:#fff}
.alrp-plan-note{margin:0 0 20px;padding-bottom:20px;border-bottom:1px solid #e2e8f0;color:#64748b;font-size:13px}
.alrp-plan.is-featured .alrp-plan-note{border-color:#ffffff26}
.alrp-plan .alrp-cta{margin-top:auto}
.alrp-every{margin:0 auto 28px;max-width:980px;padding:30px 34px;border:1px solid #e2e8f0;border-radius:14px;background:#fff;text-align:center}
.alrp-every h3{margin:0 0 8px;font-size:22px}
.alrp-every > p{margin:0 auto 22px;max-width:620px;color:#475569}
.alrp-every ul{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:0 28px;margin:0;text-align:left}
.alrp-every li{display:flex;align-items:flex-start;gap:10px;margin:0;padding:11px 0;border-bottom:1px dashed #e2e8f0}
.alrp-every li .alrp-check{flex-shrink:0;width:22px;height:22px;font-size:13px}
.alrp-trust{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:40px;padding:20px 24px;border:1px solid #e2e8f0;border-radius:14px;background:#fff}
.alrp-trust div{display:flex;align-items:center;gap:12px}
.alrp-trust .dashicons{display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:8px;background:#eef2ff;color:#4f46e5;font-size:20px}
.alrp-trust strong{display:block;font-size:14px}
.alrp-trust span.alrp-sub{color:#64748b;font-size:12px}
.alrp-faq{max-width:780px;margin:0 auto}
.alrp-faq h2{margin:0 0 18px;text-align:center;font-size:24px}
.alrp-faq details{margin-bottom:10px;border:1px solid #e2e8f0;border-radius:10px;background:#fff}
.alrp-faq details[open]{border-color:#a5b4fc}
.alrp-faq summary{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;font-weight:600;cursor:pointer;list-style:none}
.alrp-faq summary::-webkit-details-marker{display:none}
.alrp-faq summary::after{content:"\f347";font-family:dashicons;color:#64748b}
.alrp-faq details[open] summary::after{content:"\f343";color:#4f46e5}
.alrp-faq details p{margin:0;padding:0 20px 16px;color:#475569;line-height:1.6}
@media (max-width:782px){.alrp-plan.is-featured{transform:none}.alrp-compare th,.alrp-compare td{padding:11px 14px}}';
    }

    public static function render() {
        if ( ! current_user_can('manage_options') ) {
            return;
        }
        $active   = self::pro_active();
        $plans    = self::plans();
        $features = self::features();
        $pro_only = 0;
        foreach ( $features as $rows ) {
            foreach ( $rows as $row ) {
                $pro_only += $row[1] ? 0 : 1;
            }
        }
        $check = '<span class="alrp-check" aria-label="' . esc_attr__('Included', 'softicon-related-posts') . '">&#10003;</span>';
        $dash  = '<span class="alrp-dash" aria-label="' . esc_attr__('Not included', 'softicon-related-posts') . '">&ndash;</span>';
        $new   = ' <span class="screen-reader-text">' . esc_html__('(opens in a new tab)', 'softicon-related-posts') . '</span>';
        ?>
        <div class="wrap alrp-pricing">
            <h1 class="screen-reader-text"><?php esc_html_e('Pricing & comparison', 'softicon-related-posts'); ?></h1>

            <div class="alrp-head">
                <span class="alrp-eyebrow"><?php esc_html_e('COMPARE', 'softicon-related-posts'); ?></span>
                <h2><?php esc_html_e('Free vs Pro at a glance', 'softicon-related-posts'); ?></h2>
                <p>
                    <?php
                    printf(
                        /* translators: %s: number of Pro-only features */
                        esc_html__('See exactly what Pro adds. %s features are exclusive to Pro.', 'softicon-related-posts'),
                        '<strong>' . (int) $pro_only . '</strong>'
                    );
                    ?>
                </p>
            </div>

            <div class="alrp-duo">
                <div class="alrp-card">
                    <span class="alrp-tag"><?php esc_html_e('FREE', 'softicon-related-posts'); ?></span>
                    <h3><?php esc_html_e('Free', 'softicon-related-posts'); ?></h3>
                    <p><?php esc_html_e('Free forever: smart related posts, the block, layouts and design options.', 'softicon-related-posts'); ?></p>
                    <p class="alrp-price"><sup>$</sup><b>0</b> <small><?php esc_html_e('forever', 'softicon-related-posts'); ?></small></p>
                    <span class="alrp-cta is-have"><span class="dashicons dashicons-yes" aria-hidden="true"></span><?php esc_html_e('You already have it', 'softicon-related-posts'); ?></span>
                </div>
                <div class="alrp-card is-pro">
                    <span class="alrp-tag"><?php esc_html_e('PRO', 'softicon-related-posts'); ?></span>
                    <h3><?php esc_html_e('Pro', 'softicon-related-posts'); ?></h3>
                    <p><?php esc_html_e('Everything in Free, plus new placements, layouts, analytics, smarter matching and priority support.', 'softicon-related-posts'); ?></p>
                    <p class="alrp-price"><small><?php esc_html_e('from', 'softicon-related-posts'); ?></small><sup>$</sup><b><?php echo esc_html( $plans[0]['price'] ); ?></b> <small><?php esc_html_e('one-time', 'softicon-related-posts'); ?></small></p>
                    <?php if ( $active ) : ?>
                        <span class="alrp-cta is-have"><span class="dashicons dashicons-yes" aria-hidden="true"></span><?php esc_html_e('Active on this site', 'softicon-related-posts'); ?></span>
                    <?php else : ?>
                        <a class="alrp-cta is-white" href="#alrp-plans"><?php esc_html_e('Get Pro', 'softicon-related-posts'); ?> &rarr;</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="alrp-box">
                <div class="alrp-box-head">
                    <h3><?php esc_html_e('Feature breakdown', 'softicon-related-posts'); ?></h3>
                    <label class="alrp-filter">
                        <span class="screen-reader-text"><?php esc_html_e('Filter features', 'softicon-related-posts'); ?></span>
                        <span class="dashicons dashicons-search" aria-hidden="true"></span>
                        <input id="alrp-filter" type="search" placeholder="<?php esc_attr_e('Filter features…', 'softicon-related-posts'); ?>">
                    </label>
                </div>
                <table class="alrp-compare">
                    <thead>
                        <tr>
                            <th scope="col"><?php esc_html_e('Feature', 'softicon-related-posts'); ?></th>
                            <th scope="col" class="alrp-col"><?php esc_html_e('Free', 'softicon-related-posts'); ?></th>
                            <th scope="col" class="alrp-col is-pro"><?php esc_html_e('Pro', 'softicon-related-posts'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $features as $group => $rows ) : ?>
                            <tr class="alrp-group"><th scope="rowgroup" colspan="3"><?php echo esc_html( $group ); ?></th></tr>
                            <?php foreach ( $rows as $row ) : ?>
                                <tr>
                                    <td>
                                        <?php if ( $row[1] ) : ?>
                                            <?php echo esc_html( $row[0] ); ?>
                                        <?php else : ?>
                                            <a class="alrp-pro-feature" href="#alrp-plans"><?php echo esc_html( $row[0] ); ?><span class="alrp-pro-tag">PRO</span></a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="alrp-col"><?php echo $row[1] ? $check : $dash; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped above ?></td>
                                    <td class="alrp-col"><?php echo $check; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped above ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p id="alrp-filter-empty" class="alrp-empty" hidden><?php esc_html_e('No feature matches your search.', 'softicon-related-posts'); ?></p>
            </div>

            <div id="alrp-plans" class="alrp-head">
                <span class="alrp-eyebrow"><?php esc_html_e('PRICING', 'softicon-related-posts'); ?></span>
                <h2><?php esc_html_e('Pick the plan that fits your sites', 'softicon-related-posts'); ?></h2>
                <p><?php esc_html_e('Every plan has every Pro feature. Just choose how many websites you need. Pay once, use it forever.', 'softicon-related-posts'); ?></p>
            </div>

            <div class="alrp-plans">
                <?php foreach ( $plans as $plan ) : ?>
                    <div class="alrp-plan<?php echo $plan['featured'] ? ' is-featured' : ''; ?>">
                        <?php if ( $plan['featured'] ) : ?>
                            <span class="alrp-badge"><?php esc_html_e('MOST POPULAR', 'softicon-related-posts'); ?></span>
                        <?php endif; ?>
                        <h3><?php echo esc_html( $plan['name'] ); ?></h3>
                        <span class="alrp-for"><?php echo esc_html( $plan['for'] ); ?></span>
                        <p class="alrp-price"><sup>$</sup><b><?php echo esc_html( $plan['price'] ); ?></b></p>
                        <p class="alrp-plan-note"><?php esc_html_e('One-time payment, lifetime updates.', 'softicon-related-posts'); ?></p>
                        <a class="alrp-cta <?php echo $plan['featured'] ? 'is-white' : 'is-solid'; ?>" href="<?php echo esc_url( self::CHECKOUT . rawurlencode( $plan['licenses'] ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Buy now', 'softicon-related-posts'); ?> &rarr;<?php echo $new; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above ?></a>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="alrp-every">
                <span class="alrp-eyebrow is-green"><?php esc_html_e('SAME ON EVERY PLAN', 'softicon-related-posts'); ?></span>
                <h3><?php esc_html_e('Everything you get, on every license', 'softicon-related-posts'); ?></h3>
                <p><?php esc_html_e('The only difference between the plans is how many websites you can activate Pro on.', 'softicon-related-posts'); ?></p>
                <ul>
                    <?php foreach ( $features as $rows ) : ?>
                        <?php foreach ( $rows as $row ) : ?>
                            <?php if ( ! $row[1] ) : ?>
                                <li><span class="alrp-check" aria-hidden="true">&#10003;</span><?php echo esc_html( $row[0] ); ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="alrp-trust">
                <div><span class="dashicons dashicons-update" aria-hidden="true"></span><p><strong><?php esc_html_e('Lifetime updates', 'softicon-related-posts'); ?></strong><span class="alrp-sub"><?php esc_html_e('On every plan', 'softicon-related-posts'); ?></span></p></div>
                <div><span class="dashicons dashicons-format-chat" aria-hidden="true"></span><p><strong><?php esc_html_e('Priority support', 'softicon-related-posts'); ?></strong><span class="alrp-sub"><?php esc_html_e('Help when you need it', 'softicon-related-posts'); ?></span></p></div>
                <div><span class="dashicons dashicons-admin-site-alt3" aria-hidden="true"></span><p><strong><?php esc_html_e('Staging sites are free', 'softicon-related-posts'); ?></strong><span class="alrp-sub"><?php esc_html_e('Local and staging don’t count', 'softicon-related-posts'); ?></span></p></div>
                <div><span class="dashicons dashicons-lock" aria-hidden="true"></span><p><strong><?php esc_html_e('Secure checkout', 'softicon-related-posts'); ?></strong><span class="alrp-sub"><?php esc_html_e('Powered by Freemius', 'softicon-related-posts'); ?></span></p></div>
            </div>

            <div class="alrp-faq">
                <h2><?php esc_html_e('Frequently asked questions', 'softicon-related-posts'); ?></h2>
                <details open>
                    <summary><?php esc_html_e('Do I keep the free features?', 'softicon-related-posts'); ?></summary>
                    <p><?php esc_html_e('Yes. Pro is an add-on: the free plugin keeps working exactly as before, and Pro adds its features on top.', 'softicon-related-posts'); ?></p>
                </details>
                <details>
                    <summary><?php esc_html_e('Is it really a one-time payment?', 'softicon-related-posts'); ?></summary>
                    <p><?php esc_html_e('Yes. A lifetime license includes every future update. There is no subscription to cancel.', 'softicon-related-posts'); ?></p>
                </details>
                <details>
                    <summary><?php esc_html_e('Can I upgrade to more sites later?', 'softicon-related-posts'); ?></summary>
                    <p><?php esc_html_e('Yes. Buy a bigger plan any time from your account; contact us and we will help with the difference.', 'softicon-related-posts'); ?></p>
                </details>
                <details>
                    <summary><?php esc_html_e('How do I install Pro after buying?', 'softicon-related-posts'); ?></summary>
                    <p><?php esc_html_e('You get an email with the download link and your license key. Upload the Pro plugin in Plugins → Add New, activate it next to this plugin, and enter the key.', 'softicon-related-posts'); ?></p>
                </details>
                <details>
                    <summary><?php esc_html_e('Can I move my license to another site?', 'softicon-related-posts'); ?></summary>
                    <p><?php esc_html_e('Yes. Deactivate the license on the old site and activate it on the new one.', 'softicon-related-posts'); ?></p>
                </details>
            </div>
        </div>
        <?php
    }
}
