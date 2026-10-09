<?php
namespace ALRP_Related_Posts;
if ( !defined('ABSPATH') ) { exit;}

// tutorial videos shown on the settings page; a video without a url is not shown
class videos {

    const URLS = array(
        'overview'   => '',
        'setup'      => '',
        'block'      => '',
        'design'     => '',
        'smart'      => '',
        'everywhere' => '',
        'settings'   => '',
    );

    public function __construct()
    {
        add_filter('plugin_row_meta', array( $this, 'row_meta' ), 10, 2);
    }

    public static function all() {
        $videos = array(
            'overview'   => array( 'thumb' => '00-overview', 'length' => '1:12', 'title' => __('Overview: every feature in a minute', 'softicon-related-posts') ),
            'setup'      => array( 'thumb' => '01-setup', 'length' => '0:37', 'title' => __('Setup & settings', 'softicon-related-posts') ),
            'block'      => array( 'thumb' => '02-block', 'length' => '0:28', 'title' => __('The Related Posts block', 'softicon-related-posts') ),
            'design'     => array( 'thumb' => '03-design', 'length' => '0:26', 'title' => __('Layouts & design', 'softicon-related-posts') ),
            'smart'      => array( 'thumb' => '04-smart', 'length' => '0:23', 'title' => __('Most relevant & hand-picked posts', 'softicon-related-posts') ),
            'everywhere' => array( 'thumb' => '05-everywhere', 'length' => '0:28', 'title' => __('Pages, products, widget & RSS', 'softicon-related-posts') ),
            'settings'   => array( 'thumb' => '06-all-settings', 'length' => '2:19', 'title' => __('Every setting explained', 'softicon-related-posts') ),
        );

        $urls = apply_filters('alrp_video_urls', self::URLS);
        foreach ( $videos as $key => $video ) {
            $url = isset( $urls[ $key ] ) ? esc_url_raw( $urls[ $key ] ) : '';
            if ( ! $url ) {
                unset( $videos[ $key ] );
                continue;
            }
            $videos[ $key ]['url'] = $url;
        }
        return $videos;
    }

    public function row_meta($links, $file) {
        if ( ALRP_PLUGIN_BASENAME === $file && self::all() ) {
            $links[] = '<a href="' . esc_url( admin_settings::url( help_page::PAGE ) . '#alrp-videos' ) . '">' . esc_html__('Video tutorials', 'softicon-related-posts') . '</a>';
        }
        return $links;
    }

    public static function render() {
        $videos = self::all();
        if ( ! $videos ) {
            return;
        }
        ?>
        <div id="alrp-videos" class="alrp-videos">
            <h2><?php esc_html_e('Video tutorials', 'softicon-related-posts'); ?></h2>
            <div class="alrp-videos-grid">
                <?php foreach ( $videos as $video ) : ?>
                    <a class="alrp-video" href="<?php echo esc_url( $video['url'] ); ?>" target="_blank" rel="noopener noreferrer">
                        <span class="alrp-video-thumb">
                            <img src="<?php echo esc_url( ALRP_PLUGIN_ASSETS . 'images/videos/' . $video['thumb'] . '.jpg' ); ?>" alt="" width="480" height="270" loading="lazy">
                            <span class="alrp-video-play" aria-hidden="true"></span>
                            <span class="alrp-video-length"><?php echo esc_html( $video['length'] ); ?></span>
                        </span>
                        <span class="alrp-video-title"><?php echo esc_html( $video['title'] ); ?><span class="screen-reader-text"> <?php esc_html_e('(opens in a new tab)', 'softicon-related-posts'); ?></span></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    public static function styles() {
        return '.alrp-videos{margin:20px 0 10px;max-width:1100px}
.alrp-videos-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px}
.alrp-video{display:block;text-decoration:none;color:inherit;background:#fff;border:1px solid #dcdcde;border-radius:8px;overflow:hidden}
.alrp-video:hover,.alrp-video:focus{border-color:#2271b1;box-shadow:0 0 0 1px #2271b1}
.alrp-video-thumb{position:relative;display:block}
.alrp-video-thumb img{display:block;width:100%;height:auto}
.alrp-video-play{position:absolute;left:8px;bottom:8px;width:34px;height:34px;border-radius:50%;background:#000c}
.alrp-video-play:after{content:"";position:absolute;left:13px;top:10px;border-style:solid;border-width:7px 0 7px 11px;border-color:transparent transparent transparent #fff}
.alrp-video-length{position:absolute;right:8px;bottom:8px;background:#000c;color:#fff;font-size:12px;padding:1px 6px;border-radius:4px}
.alrp-video-title{display:block;padding:10px 12px;font-weight:600}';
    }
}
