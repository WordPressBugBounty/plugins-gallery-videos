<div class="tsvg_content active" data-tsvg-section="theme">
    <?php
    foreach ( $this->tsvg_themes as $theme_key => $theme_value ) {
        $tsvg_is_pro_theme = $theme_key === "effective_gallery" || $theme_key === "gallery_album" || $theme_key === "video_portfolio" || $theme_key === "image_portfolio" || $theme_key === "image_gallery" || $theme_key === "mix_portfolio";
        echo sprintf(
            '
            <div class="tsvg_theme tsvg_flex_col">
                <div class="tsvg_theme_header">
                    <a class="tsvg_theme_demo tsvg_flex_col %1$s" href="%2$s" target="_blank">
                        <i class="ts-vgallery ts-vgallery-eye"></i>
                    </a>
                    <div class="tsvg_theme_version %3$s">%4$s</div>
                    <img src="%5$s" alt="%6$s"/>
                </div>
                <div class="tsvg_theme_main">
                    <h1 class="tsvg_theme_name">%7$s</h1>
                    <div class="tsvg_theme_choose tsvg_flex_col">
                        <a href="%8$s" %9$s class="tsvg_theme_use %10$s"><span><i class="ts-vgallery ts-vgallery-magic"></i>%11$s</span></a>
                    </div>
                </div>
            </div>
            ',
            $tsvg_is_pro_theme ? 'tsvg_theme_demo_pro' : '',
            esc_url( "https://total-soft.com/" . $this->tsvg_themes_links[$theme_key] ),
            $tsvg_is_pro_theme ? 'tsvg_theme_version_pro' : '',
            $tsvg_is_pro_theme ? esc_html('PRO THEME') : esc_html('FREE THEME'),
            esc_url(plugin_dir_url( __DIR__ ) ."img/".$theme_key.".png"),
            esc_attr($theme_key),
            esc_html($theme_value),
            $tsvg_is_pro_theme ? esc_url("https://total-soft.com/wp-video-gallery/") : esc_url(add_query_arg('tsvg-theme',$theme_key)),
            $tsvg_is_pro_theme ? 'target="_blank"' : '',
            $tsvg_is_pro_theme ? 'tsvg_theme_use_pro' : '',
            $tsvg_is_pro_theme ? esc_html('Pro Theme') : esc_html('Use Theme')
        );
    }
    ?>
</div>