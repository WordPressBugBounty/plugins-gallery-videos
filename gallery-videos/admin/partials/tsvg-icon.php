<?php
echo sprintf(
    '
    <div class="ts-vgallery-aim-modal" id="ts-vgallery-aim-modal" style="display:none;">
        <div class="ts-vgallery-aim-modal--content">
            <div class="ts-vgallery-aim-modal--header">
                <div class="ts-vgallery-aim-modal--header-logo-area">
                    <span class="ts-vgallery-aim-modal--header-logo-title">
                        %1$s
                    </span>
                </div>
                <div class="ts-vgallery-aim-modal--header-close-btn">
                    <i class="ts-vgallery-fas ts-vgallery-times" title="%2$s"></i>
                </div>
            </div>
            <div class="ts-vgallery-aim-modal--body">
                <div id="ts-vgallery-aim-modal--sidebar" class="ts-vgallery-aim-modal--sidebar">
                    <div class="ts-vgallery-aim-modal--sidebar-tabs">
                        <div class="ts-vgallery-aim-modal--sidebar-tab-item aesthetic-active" data-library-id="all">
                            <i class="ts-vgallery ts-vgallery-star"></i>
                            %3$s
                        </div>
                        <div class="ts-vgallery-aim-modal--sidebar-tab-item" data-library-id="ts-vgallery-regular">
                            <i class="ts-vgallery ts-vgallery-font-awesome"></i>
                            font awesome
                        </div>
                        <div class="ts-vgallery-aim-modal--sidebar-tab-item" data-library-id="ts-vgallery-emoji-regular">
                            <i class="ts-vgallery-emoji ts-vgallery-emoji-grinning"></i>
                            %4$s
                        </div>
                    </div>
                </div>
                <div id="ts-vgallery-aim-modal--icon-preview-wrap" class="ts-vgallery-aim-modal--icon-preview-wrap">
                    <div class="ts-vgallery-aim-modal--icon-search">
                        <input type="text" id="ts-vgallery-aim-modal--search_input"  placeholder="%5$s">
                        <i class="ts-vgallery-fas ts-vgallery-search"></i>
                    </div>
                    <div class="ts-vgallery-aim-modal--icon-preview-inner">
                        <div id="ts-vgallery-aim-modal--icon-preview">
    ',
	esc_html__( 'Icon Picker','gallery-videos' ),
	esc_html__( 'Close','gallery-videos' ),
	esc_html__( 'all','gallery-videos' ),
	esc_html__( 'emojies','gallery-videos' ),
	esc_html__( 'Filter by name...','gallery-videos' )
);
foreach ( $tsvg_all_fonts_arr['tsvg_fonts'] as $key => $value ) {
    foreach ( $value as $icon_key => $icon_value ) {
        echo sprintf(
            '
            <div class="ts-vgallery-aim-icon-item" data-library-id="%1$s" data-filter="%2$s">
                <div class="ts-vgallery-aim-icon-item-inner">
                    <i class="%3$s"></i>
                    <div class="ts-vgallery-aim-icon-item-name" title="%4$s">%4$s</div>
                </div>
            </div>
            ',
            $key == 'tsvg_emojies' ? esc_attr( 'ts-vgallery-emoji-regular' ) : esc_attr( 'ts-vgallery-regular' ),
            $key == 'tsvg_emojies' ? esc_attr( str_replace( 'ts-vgallery-emoji ts-vgallery-emoji-', '', $icon_value ) ) : esc_attr( str_replace( 'ts-vgallery ts-vgallery-', '', $icon_value ) ),
            esc_attr( $icon_value ),
            $key == 'tsvg_emojies' ? esc_attr( str_replace( '-', ' ', str_replace( 'ts-vgallery-emoji ts-vgallery-emoji-', '', $icon_value ) ) ) : esc_attr( str_replace( '-', ' ', str_replace( 'ts-vgallery ts-vgallery-', '', $icon_value ) ) )
        );
    }
}
echo sprintf(
    '
                        </div>
                    </div>
                </div>
            </div>
            <div class="ts-vgallery-aim-modal--footer">
                <button class="ts-vgallery-aim-insert-icon-button" id="ts-vgallery-aim-insert-icon-button">
                    %1$s
                </button>
            </div>
        </div>
    </div>
    ',
	esc_html__( 'Select','gallery-videos' )
);
