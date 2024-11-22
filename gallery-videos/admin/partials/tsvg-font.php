<style id="tsvg_all_styles">
<?php
    foreach ( $tsvg_all_fonts_arr['tsvg_font_families'] as $key => $value ) {
        $tsvg_font_params = $value;
        echo sprintf(
            '
            @font-face {
                font-family: "%1$s";
                font-style: normal;
                font-weight: 400;
                src: url("%2$s"); 
                src: url("%3$s") format("embedded-opentype"), 
                    url("%4$s") format("woff2"), 
                    url("%5$s") format("woff"), 
                    url("%6$s") format("truetype"), 
                    url("%7$s") format("svg");
            }
            ',
            esc_attr( $key ),
            array_key_exists( 'eot', $tsvg_font_params ) ? esc_url( $tsvg_font_params['eot'] ) : '',
            array_key_exists( 'eot', $tsvg_font_params ) ? esc_url( $tsvg_font_params['eot'] ) : '',
            array_key_exists( 'woff2', $tsvg_font_params ) ? esc_url( $tsvg_font_params['woff2'] ) : '',
            array_key_exists( 'woff', $tsvg_font_params ) ? esc_url( $tsvg_font_params['woff'] ) : '',
            array_key_exists( 'ttf', $tsvg_font_params ) ? esc_url( $tsvg_font_params['ttf'] ) : '',
            array_key_exists( 'svg', $tsvg_font_params ) ? esc_url( $tsvg_font_params['svg'] ) : ''
        );
    }
?>
</style>
