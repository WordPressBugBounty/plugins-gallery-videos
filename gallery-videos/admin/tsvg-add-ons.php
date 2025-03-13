<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}
$data = array(
    "active" => "10000",
    "review" => "4.8",
    "downloads" => "700000+"
);
$username = 'totalsoft';
$params = array(
    'timeout'   => 10,
    'sslverify' => false
);
$raw = wp_remote_retrieve_body( wp_remote_get( 'https://wptally.com/api/' . $username, $params ) );
$raw = json_decode( $raw, true );
if ( is_array( $raw ) && !array_key_exists( 'error', $raw ) ) {
    $raw = $raw["plugins"]["gallery-videos"];
    $data["active"] = $raw["installs"];
    $data["review"] = $raw["rating"];
    $data["downloads"] = $raw["downloads"];
}
$tsvg_addons_cards = "";
$tsvg_addons_array = array(
    "Video Portfolio Theme" => [
        "src" => esc_url(plugin_dir_url( __FILE__ ) . 'img/add-ons/video_portfolio.png'),
        "desc" => "",
        "link" => esc_url( "https://total-soft.com/wp-video-portfolio-11/" )
    ],
    "Image Portfolio Theme" => [
        "src" => esc_url(plugin_dir_url( __FILE__ ) . 'img/add-ons/image_portfolio.png'),
        "desc" => "",
        "link" => esc_url( "https://total-soft.com/wp-video-image-portfolio-8/" )
    ],
    "Image Gallery Theme" => [
        "src" => esc_url(plugin_dir_url( __FILE__ ) . 'img/add-ons/image_gallery.png'),
        "desc" => "",
        "link" => esc_url( "https://total-soft.com/wp-video-image-gallery-3/" )
    ],
    "Mix Portfolio Theme" => [
        "src" => esc_url(plugin_dir_url( __FILE__ ) . 'img/add-ons/mix_portfolio.png'),
        "desc" => "",
        "link" => esc_url( "https://total-soft.com/wp-video-mix-portfolio-4/" )
    ],
    "Album Gallery Theme" => [
        "src" => esc_url(plugin_dir_url( __FILE__ ) . 'img/add-ons/album_gallery.png'),
        "desc" => "",
        "link" => esc_url( "https://total-soft.com/wp-video-gallery-album/" )
    ],
    "Effective Gallery Theme" => [
        "src" => esc_url(plugin_dir_url( __FILE__ ) . 'img/add-ons/effective_gallery.png'),
        "desc" => "",
        "link" => esc_url( "https://total-soft.com/wp-video-gallery-effective/" )
    ],
    "Menu 1" => [
        "src" => esc_url(plugin_dir_url( __FILE__ ) . 'img/add-ons/menu_1.png'),
        "desc" => "",
        "link" => esc_url( "https://total-soft.com/wp-video-image-portfolio-8/" ),
        "type" => "menu"
    ],
    "Menu 2" => [
        "src" => esc_url(plugin_dir_url( __FILE__ ) . 'img/add-ons/menu_2.png'),
        "desc" => "",
        "link" => esc_url( "https://total-soft.com/wp-video-portfolio-16/" ),
        "type" => "menu"
    ],
    "Menu 3" => [
        "src" => esc_url(plugin_dir_url( __FILE__ ) . 'img/add-ons/menu_3.png'),
        "desc" => "",
        "link" => esc_url( "https://total-soft.com/wp-video-portfolio-11/" ),
        "type" => "menu"
    ],
    "Menu 4" => [
        "src" => esc_url(plugin_dir_url( __FILE__ ) . 'img/add-ons/menu_4.png'),
        "desc" => "",
        "link" => esc_url( "https://total-soft.com/wp-video-portfolio-3/" ),
        "type" => "menu"
    ],
    "Menu 5" => [
        "src" => esc_url(plugin_dir_url( __FILE__ ) . 'img/add-ons/menu_5.png'),
        "desc" => "",
        "link" => esc_url( "https://total-soft.com/wp-video-portfolio-4/" ),
        "type" => "menu"
    ],
    "Menu 6" => [
        "src" => esc_url(plugin_dir_url( __FILE__ ) . 'img/add-ons/menu_6.png'),
        "desc" => "",
        "link" => esc_url( "https://total-soft.com/wp-video-portfolio-4/" ),
        "type" => "menu"
    ],
    "Menu 7" => [
        "src" => esc_url(plugin_dir_url( __FILE__ ) . 'img/add-ons/menu_7.png'),
        "desc" => "",
        "link" => esc_url( "https://total-soft.com/wp-video-portfolio-6/" ),
        "type" => "menu"
    ],
    "Menu 8" => [
        "src" => esc_url(plugin_dir_url( __FILE__ ) . 'img/add-ons/menu_8.png'),
        "desc" => "",
        "link" => esc_url( "https://total-soft.com/wp-video-portfolio-2/" ),
        "type" => "menu"
    ],
    "Menu 9" => [
        "src" => esc_url(plugin_dir_url( __FILE__ ) . 'img/add-ons/menu_9.png'),
        "desc" => "",
        "link" => esc_url( "https://total-soft.com/wp-video-image-portfolio-9/" ),
        "type" => "menu"
    ]
);
foreach ($tsvg_addons_array as $key => $value) {
    $tsvg_addons_cards .= sprintf('
        <div class="tsvg_addon_card %5$s">
            <div class="tsvg_card__image-holder">
                <img class="tsvg_card__image" src="%1$s" alt="%2$s" />
            </div>
            <div class="tsvg_addon_card-title">
                <a  class="tsvg_toggle-info tsvg_addon_btn">
                    <span class="tsvg_left"></span>
                    <span class="tsvg_right"></span>
                </a>
                <h2>
                    %2$s
                </h2>
                </br>
            </div>
            <div class="tsvg_addon_card-flap tsvg_flap1">
                <div class="tsvg_addon_card-description">
                    %3$s
                </div>
                <div class="tsvg_addon_card-flap tsvg_flap2">
                    <div class="tsvg_addon_card-actions">
                        <a href="%4$s" class="tsvg_addon_btn" target="_blank">See demo</a>
                    </div>
                </div>
            </div>
        </div>
        ',
        esc_url($value["src"]),
        esc_html($key),
        esc_html($value["desc"]),
        esc_html($value["link"]),
        array_key_exists('type',$value) ? "tsvg_addon_card_" . $value["type"] : ""
    );
}
echo sprintf('
    <header class="tsvg_addon_header">
        <div class="tsvg_addon_inner">
            <div class="tsvg_addon_header_info">
                <h1>Upgrade TS Video Gallery Pro</h1>
                The software is responsive user friendly and can really enhance the reiting of your site when people search for related topics and videos whether on Youtube, Vimeo and Wistia or search engines in general. Getting your Youtube, Vimeo or Wistia extension working is straightforward with a few simple steps required to build this great video resources on your WordPress site. The benefits of the gallery plugin are already making a real difference for all kinds of sites from business oriented to hobby or entertainment pages. The great thing about the Gallery is that it allows users to express their creative editing skills when composing a video collection slideshow or even workshop and information content. Being able to engage with site visitors this way directly with gallery also helps to make your site much more reiting.            </div>
            <div class="tsvg_addon_header_img">
            </div>
        </div>
    </header>
    <div class="tsvg_plugin_with_numbers">
        <div class="tsvg_flex_field">
            <p class="tsvg_count">%3$s +</p>
            <p class="tsvg_type">active installations</p>
        </div>
        <div class="tsvg_flex_field">
            <p class="tsvg_count">%4$s / 5</p>
            <p class="tsvg_type">user reviews</p>
        </div>
        <div class="tsvg_flex_field">
            <p class="tsvg_count">%5$s</p>
            <p class="tsvg_type">all time downloads</p>
        </div>
    </div>
    <div class="tsvg_addon_cards">
        %1$s
    </div>
    ',
    $tsvg_addons_cards,
    esc_url(plugin_dir_url( __FILE__ ) . 'img/add-ons/addons.gif'),
    $data["active"],
    $data["review"],
    $data["downloads"]
);
