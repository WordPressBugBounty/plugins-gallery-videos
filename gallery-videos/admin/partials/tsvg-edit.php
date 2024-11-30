<?php
echo sprintf(
    '
    <div class="tsvg_content tsvg_flex_row active" data-tsvg-section="field_style">
		<div class="tsvg_preview_content">
			<div class="tsvg_preview_content_sizes tsvg_flex_row">
				<div data-tsvg-size="desktop" class="tsvg_flex_col tsvg_preview_content_size tsvg_preview_content_size_active ">
					<img data-tsvg-size="desktop" src="%1$s" alt="desktop">
				</div>
				<div data-tsvg-size="tablet" class="tsvg_flex_col tsvg_preview_content_size ">
					<img data-tsvg-size="tablet" src="%2$s" alt="tablet">
				</div>
				<div data-tsvg-size="mobile" class="tsvg_flex_col tsvg_preview_content_size ">
					<img data-tsvg-size="mobile" src="%3$s" alt="mobile">
				</div>
			</div>
			<div class="tsvg_preview_content_shortcode">
				<div class="tsvg_preview_content_shortcode_inner">
    ',
    esc_url( plugin_dir_url( __DIR__ ) . 'img/desktop.svg' ),
    esc_url( plugin_dir_url( __DIR__ ) . 'img/tablet.svg' ),
    esc_url( plugin_dir_url( __DIR__ ) . 'img/mobile.svg' ),
);
echo do_shortcode( sprintf( '[TS_Video_Gallery id="%s" edit="true"]', esc_attr( $this->tsvg_build_id ) ) );
$tsvg_builder_params = $this->tsvg_build_proporties;
echo sprintf(
    '
                </div>
			</div>
		</div> 
		<div class="tsvg_flex_col tsvg_content_subsection active" data-tsvg-subsection="field">
			<main class="tsvg_content_fields_menu">
				<div aria-tsvg-use="field" class="tsvg_flex_col active">
					<div class="tsvg-add_video-div tsvg_flex_row">
						<div id="tsvg-add_video">
							<span class="tsvg-add_video_text">
								<span></span>
								%1$s
							</span>
							<span class="right ts-vgallery ts-vgallery-plus"></span>
						</div>
					</div>
					<div class="tsvg-list tsvg_flex_col" id="tsvg-list" >
    ',
	esc_html__( 'Add video','gallery-videos' )
);
$tsvg_sort_order = explode( ',', $tsvg_builder_params['TS_VG_Sort'] );
uksort(
    $tsvg_builder_params['tsvg_video_records'],
    function( $x, $y ) use ( $tsvg_sort_order ) {
        if ( (int) array_search( $x, $tsvg_sort_order ) == (int) array_search( $y, $tsvg_sort_order ) ) {
            return 0;
        }
        return ( (int) array_search( $x, $tsvg_sort_order ) < (int) array_search( $y, $tsvg_sort_order ) ) ? -1 : 1;
    }
);
foreach ($tsvg_builder_params['tsvg_video_records'] as $key => $value) {
    echo sprintf(
        '
        <div class="tsvg-list-item" aria-tsvg-video="%1$s">
            <div class="tsvg_handle_list tsvg_list_action flex-center">
                <img src="%2$s">
            </div>
            <div class="details tsvg_analytics_flex_r">
                <h2>%3$s</h2>
            </div>
            <div class="tsvg_list_action flex-center" data-tsvg-action="edit">
                <img src="%4$s">
            </div>
            <div class="tsvg_list_action flex-center" data-tsvg-action="copy">
                <img src="%5$s">
            </div>
            <div class="tsvg_list_action flex-center" data-tsvg-action="delete">
                <img src="%6$s">
            </div>
        </div>
        ',
        esc_attr($value['id']),
        esc_url( plugin_dir_url( __DIR__ ) . 'img/move.svg' ),
        esc_attr($value['TS_VG_SetName']),
        esc_url( plugin_dir_url( __DIR__ ) . 'img/edit.svg'),
        esc_url( plugin_dir_url( __DIR__ ) . 'img/copy.svg' ),
        esc_url( plugin_dir_url( __DIR__ ) . 'img/recycle.svg' )
    );
}
echo sprintf(
    '
                    </div>
                </div>
            </main>
            <main class="tsvg_content_fields_edit" style="display:none;">
                <div class="tsvg_back_to_videos tsvg_flex_row ts-vgallery ts-vgallery-home">
                    %1$s
                </div>
                <div class="tsvg_TS_VG_Optionss">
                    <div class="tsvg_select_div_edit">
                        <span class="tsvg_select_div_title tsvg_field_title">
                            %2$s
                        </span>
                        <input id="tsvg_TS_VG_SetName" name="tsvg_TS_VG_SetName" type="text"  value="" placeholder="%2$s"/>
                    </div>
    ',
	esc_html__( 'Back to video','gallery-videos' ),
	esc_html__( 'Add title','gallery-videos' )
);
$tsvg_elem    = 'tsvg_content_area';
$tsvg_content = '';
$tsvg_args    = array(
    'tinymce'       => array(
        'toolbar1'         => 'formatselect, fontselect, fontsizeselect, alignleft, aligncenter, alignright, pastetext, bold, italic, underline, strikethrough, removeformat, link, code, hr, forecolor, backcolor, charmap, outdent, indent, undo, redo',
        'fontsize_formats' => '8px 10px 12px 14px 16px 18px 20px 22px 24px 26px 28px 30px 32px 34px 36px 38px 40px 42px 44px 46px 48px',
        'font_formats'     => 'Arial = arial; Arial Black = arial black; alibri = calibri; alibri Light = calibri light; Calisto MT = calisto mt; Cambria = cambria; Century Gothic = century gothic; Comic Sans MS = comic sans ms; onsolas = consolas; Constantia = constantia; Copperplate Gothic = copperplate gothic; Copperplate Gothic Light = copperplate gothic light; orbel = corbel; Courier New = courier new; Ebrima = ebrima; abriola = gabriola; adugi = gadugi; Georgia = georgia; mpact = impact; Leelawadee = leelawadee; ucida Console = lucida console; Microsoft Himalaya = microsoft himalaya; icrosoft JhengHei = microsoft jhenghei; Microsoft New Tai Lue = microsoft new tai lue; icrosoft PhagsPa = microsoft phagspa; Microsoft Sans Serif = microsoft sans serif; icrosoft Tai Le = microsoft tai le; icrosoft Uighur = microsoft uighur; icrosoft YaHei = microsoft yahei; icrosoft YaHei UI = microsoft yahei ui; Microsoft Yi Baiti = microsoft yi baiti; iriam = miriam; Mongolian Baiti = mongolian baiti; MS UI Gothic = ms ui gothic; V Boli = mv boli; yanmar Text = myanmar text; arkisim = narkisim; SimSun = nsimsun; yala = nyala; alatino Linotype = palatino linotype; egoe Print = segoe print; egoe Script = segoe script; egoe UI Symbol = segoe ui symbol; SimSun = simsun; ylfaen = sylfaen; ahoma = tahoma; rebuchet MS = trebuchet ms'
    ),
    'quicktags'     => false,
    'media_buttons' => false
);
wp_editor( $tsvg_content, $tsvg_elem, $tsvg_args );
echo sprintf(
    '
                <div class="tsvg_select_div_edit">
                    <span class="tsvg_select_div_link tsvg_field_link">
                        %1$s
                    </span>
                    <input id="tsvg_video_link" name="tsvg_video_link" type="text"  value="" placeholder="%1$s"/>
                </div>
                <div class="tsvg_color_div_edit">
                    <label class="tsvg_color_label" for="tsvg_video_Video_ONT">%2$s</label>
                    <input  type="checkbox" id="tsvg_video_Video_ONT" name="tsvg_video_Video_ONT"  value=""  data-tsvg-change=""/>
                </div>
                <div class="tsvg_video_div_edit">
                    <span class="tsvg_field_title">%3$s</span>
                    <div class="tsvg_vd_change">
                        <img src="%4$s" style="display:none;">
                        <div class="tsvg_vd_hover_div">
                            <span>%5$s</span>
                            <input type="text"  id="tsvg_video_video_attachment_id" style="display:none;">
                        </div>
                        <div class="tsvg_vd_loading_div tsvg_flex_col"  style="display:none;">
                            <img src="%6$s" >
                        </div>
                    </div>
                </div>
                <div class="tsvg_img_div_edit">
                    <span class="tsvg_field_title">%7$s</span>
                    <div class="tsvg_img_change">
                        <img src="%8$s">
                        <div class="tsvg_img_hover_div">
                            <span>%9$s</span>
                            <input type="text"  id="tsvg_video_attachment_id" style="display:none;">
                        </div>
                        <div class="tsvg_img_loading_div tsvg_flex_col"  style="display:none;">
                            <img src="%10$s" >
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div class="tsvg_flex_col tsvg_content_subsection" data-tsvg-subsection="style">
		<div class="tsvg_styles_sidebar">
    ',
	esc_html__( 'Target link','gallery-videos' ),
	esc_html__( 'New Tab','gallery-videos' ),
	esc_html__( 'Add video','gallery-videos' ),
    esc_url( TSVG_PLUGIN_DIR . 'public/img/tsvg_no_video.png' ),
	esc_html__( 'Choose video','gallery-videos' ),
    esc_url( TSVG_PLUGIN_DIR . 'public/img/tsvg_loading.gif' ),
	esc_html__( 'Add Image','gallery-videos' ),
    esc_url( TSVG_PLUGIN_DIR . 'public/img/tsvg_no_img.jpg' ),
	esc_html__( 'Choose image','gallery-videos' ),
    esc_url( TSVG_PLUGIN_DIR . 'public/img/tsvg_loading.gif' )
);
$tsvg_builder_styles_base_keys  = array_keys( $tsvg_builder_styles_base );
$tsvg_builder_styles_base_count = count( $tsvg_builder_styles_base_keys );
$tsvg_builder_styles_base       = array_merge( $tsvg_builder_styles_base, $tsvg_builder_settings_base );
foreach ( $tsvg_builder_arr as $fields_key => $fields_value ) {
    if ( count( array_diff( $tsvg_builder_styles_base_keys, array_keys( $tsvg_builder_arr[ $fields_key ] ) ) ) == $tsvg_builder_styles_base_count ) {
        continue;
    }
    echo sprintf(
        '
        <div class="tsvg_accordion_item %1$s">
            <header class="tsvg_accordion_header ">
                <i class="ts-vgallery ts-vgallery-angle-right tsvg_accordion_header_icon"></i>
                <h3 class="tsvg_accordion_header_title">%2$s</h3>
            </header>
            <div class="tsvg_accordion_item_content">
                <div class="tsvg_accordion_items tsvg_analytics_flex_c">
        ',
        esc_attr( str_replace( ' ', '-', strtolower( $fields_key ) ) ),
        esc_attr( $fields_key )
    );
    foreach ( $fields_value as $field_key => $field_value ) {
        $fieldname = '';
        $field = '';
        $value = '';
        if ( array_key_exists($field_key, $tsvg_builder_styles_base ) ) {
            $fieldname = $field_key;
            $field = $field_value;
            $value = strval( $tsvg_builder_styles_base[$field_key ] );
        } else if ($field_key == 'TotalSoftvgallery_Q_Im' || $field_key == 'TotalSoftvgallery_Q_Vd' ) {
            $fieldname = $field_key;
            $field = $field_value;
            $value = $tsvg_builder_param_base[$field_key ];
        }else{
            continue;
        }
        switch ($field['type']):
			case 'range':
				echo sprintf(
					'
					<div class="length tsvg_range_div" data-tsvg-min="%1$s" data-tsvg-max="%2$s" >
						<div class="tsvg_range_div_title tsvg_field_title" data-tsvg-field="%4$s" data-tsvg-length="%3$s(%7$s)">length:</div>
						<label class="tsvg_range_label" for="%4$s">%5$s</label>
						<input id="%4$s" class="tsvg_range_input" type="range" min="%1$s" max="%2$s" value="%3$s" step="%8$s" data-tsvg-change="%6$s" data-tsvg-param="%7$s"  />
					</div>
					',
					esc_attr($field['options']['min']),
					esc_attr($field['options']['max']),
					array_key_exists('time', $field) ? esc_attr(strpos($value, '.')) !== false ? esc_attr($value) : esc_attr($value / 10) : esc_attr($value),
					esc_attr($fieldname),
					esc_attr($field['label']),
					array_key_exists('change', $field) ? esc_attr($field['change']) : '',
					array_key_exists('change_param', $field) ? esc_attr($field['change_param']) : '',
					array_key_exists('step', $field) ? esc_attr($field['step']) : ''
				);
				break;
			case 'text':
			case 'date':
				echo sprintf(
					'<div class="tsvg_select_div">
						<span class="tsvg_select_div_title tsvg_field_title">%1$s</span>
						<input id="%2$s" name="%3$s" type="%4$s" class="tsvg_text_input" value="%5$s" data-tsvg-elem="%6$s" data-change-prop="%7$s"/>
					</div>
					',
					esc_attr($field['label']),
					esc_attr($fieldname),
					esc_attr($fieldname),
					$field['type'] == 'text' ? 'text' : 'date',
					esc_attr($value),
					array_key_exists('change_elem', $field) ? esc_attr($field['change_elem']) : '',
					array_key_exists('change_attr', $field) ? esc_attr($field['change_attr']) : ''
				);
				break;
			case 'color':
				echo sprintf(
					'
					<div class="tsvg_color_div">
						<label class="tsvg_color_label" for="%1$s">%2$s</label>
						<input id="%1$s" name="%1$s" data-tsvg-field="color" value="%3$s" data-tsvg-change="%4$s" />
					</div>
					',
					esc_attr($fieldname),
					esc_attr($field['label']),
					esc_attr($value),
					array_key_exists('change_prop', $field) ? esc_attr($field['change_prop']) : ''
				);
				break;
			case 'input-toggle':
				echo sprintf(
					'
					<div class="tsvg_checkbox_div" data-tsvg-check="%6$s" data-tsvg-uncheck="%7$s">
						<input class="tsvg_checkbox_input" type="checkbox" id="%1$s" name="%1$s" %2$s data-change-elem="%4$s" data-change-prop="%5$s"/>
						<label class="tsvg_checkbox_label" for="%1$s">%3$s</label>
					</div>
					',
					esc_attr($fieldname),
					$value == 'true' ? esc_attr('checked') : '',
					esc_attr($field['label']),
					array_key_exists('change_elem', $field) ? esc_attr($field['change_elem']) : '',
					array_key_exists('change_attr', $field) ? esc_attr($field['change_attr']) : '',
					array_key_exists('yes', $field['options']) ? esc_attr($field['options']['yes']) : 'true',
					array_key_exists('no', $field['options']) ? esc_attr($field['options']['no']) : 'false'
				);
				break;
			case 'select-icon':
				echo sprintf(
					'
					<div class="tsvg_icon_picker_div">
						<label id="%s" for="%s">%s</label>
						<div class="ts-vgallery-icon-picker-wrap" id="%s" data-tsvg-field="%s">
							<ul class="icon-picker">
								%s
								<li id="%s" class="ts-vgallery-select-icon" title="Icon Library"><i class="%s"></i></li>
								<input type="hidden" name="icon_value" id="%s" value="%s" data-tsvg-elem="%s" data-change-prop="%s">
							</ul>
						</div>
					</div>
					',
					esc_attr($fieldname) . '-icon-picker-wrap-label',
					esc_attr($fieldname) . '-icon_value',
					esc_attr($field['label']),
					esc_attr($fieldname) . '-icon-picker-wrap',
					esc_attr($fieldname),
					sprintf('<li class="tsvg-set-icon-none" id="%s" title="None"><i class="ts-vgallery ts-vgallery-ban"></i></li>', esc_attr($fieldname) . '-icon-none'),
					esc_attr($fieldname),
					esc_attr($value),
					esc_attr($fieldname) . '-icon_value',
					esc_attr($value),
					array_key_exists('change_elem', $field) ? esc_attr($field['change_elem']) : '',
					array_key_exists('change_attr', $field) ? esc_attr($field['change_attr']) : ''
				);
				break;
			case 'select':
				echo sprintf(
                    '
                    <div class="tsvg_select_div">
                        <span class="tsvg_select_div_title tsvg_field_title">%s</span>
                        <select id="%s" name="%s" class="%s" data-change-elem="%s" data-change-prop="%s" />
                        ',
                    esc_attr($field['label']),
                    esc_attr($fieldname),
                    esc_attr($fieldname),
                    array_key_exists('change_elem', $field) ? esc_attr('tsvg_elem_data') : esc_attr('tsvg_root_elem'),
                    array_key_exists('change_elem', $field) ? esc_attr($field['change_elem']) : '',
                    array_key_exists('change_attr', $field) ? esc_attr($field['change_attr']) : ''
                );
                foreach ($field['options'] as $opt_value => $opt_name) {
					echo sprintf(
						'<option value="%s" %s>%s</option>',
						esc_attr($opt_value),
						$opt_value == $value ? esc_attr('selected') : esc_attr(''),
						esc_html($opt_name)
					);
				}
                echo '</select></div>';
				break;
			case 'select-position':
				echo sprintf(
					'
					<div class="tsvg_select_div"><span class="tsvg_select_div_title tsvg_field_title">%s</span>
						<div class="tsvg_position_select  tsvg_flex_row" data-tsvg="%s" data-tsvg-select="%s" data-change-elem="%s" data-change-prop="%s">
					',
					esc_attr($field['label']),
					array_key_exists('full', $field['options']) ? 'btn' : 'div',
					esc_attr($fieldname),
					array_key_exists('change_elem', $field) ? esc_attr($field['change_elem']) : '',
					array_key_exists('change_attr', $field) ? esc_attr($field['change_attr']) : ''
				);
                foreach ($field['options'] as $opt_value => $opt_name) {
					echo sprintf(
						'
                        <div class="tsvg_position_item %s" data-tsvg-pos="%s">
                            <p class="tsvg_flex_col">%s</p>
                        </div>
                        ',
						$opt_value == $value ? esc_attr('tsvg_active') : esc_attr(''),
						esc_attr($opt_value),
						esc_html($opt_name)
					);
				}
                echo '</div></div>';
				break;
			case 'select-position-efect':
				echo sprintf(
					'
					<div class="tsvg_select_div tsvg_select_div_ef">
						<span class="tsvg_select_div_title tsvg_field_title">
							%s
						</span>
						<div class="tsvg_position_select tsvg_position_select-ef  tsvg_flex_row" data-tsvg="%s" data-tsvg-select="%s" data-change-elem="%s" data-change-prop="%s">
							<div class="tsvg-style-inner-pro tsvg_flex_col tsvg_flex_col_hide">
								<h2>This photo for illustration. Feature are available in the Professional version of the plugin only.</h2>
								<a href="%s" target="_blank">
									GET THE FULL VERSION
								</a>
							</div>	
					',
					esc_attr($field['label']),
					array_key_exists('full', $field['options']) ? 'btn' : 'div',
					esc_attr($fieldname),
					array_key_exists('change_elem', $field) ? esc_attr($field['change_elem']) : '',
					array_key_exists('change_attr', $field) ? esc_attr($field['change_attr']) : '',
					esc_url("https://total-soft.com/wp-video-gallery/")
				);
                foreach ($field['options'] as $opt_value => $opt_name) {
					echo sprintf(
						'<div class="tsvg_position_item %s" data-tsvg-pos="%s">
							<p class="tsvg_flex_col">
								%s
							</p>
						</div>',
						$opt_value == $value ? esc_attr('tsvg_active') : esc_attr(''),
						esc_attr($opt_value),
						esc_html($opt_name)
					);
				}
                echo '</div></div>';
				break;
			case 'select-position-image':
				echo sprintf(
					'
					<div class="tsvg_select_div">
						<span class="tsvg_select_div_title tsvg_field_title">
							%s
						</span>
						<div class="tsvg_position_select tsvg_flex_row" data-tsvg="image" data-tsvg-select="%s" data-change-elem="%s" data-change-prop="%s">
							<div class="tsvg-style-inner-pro tsvg_flex_col">
								<h2>This photo for illustration. Feature are available in the Professional version of the plugin only.</h2>
								<a href="%s" target="_blank">
									GET THE FULL VERSION
								</a>
							</div>	
					',
					esc_attr($field['label']),
					esc_attr($fieldname),
					array_key_exists('change_elem', $field) ? esc_attr($field['change_elem']) : '',
					array_key_exists('change_attr', $field) ? esc_attr($field['change_attr']) : '',
					esc_url("https://total-soft.com/wp-video-gallery/")
				);
				foreach ($field['options'] as $opt_value => $opt_name) {
					echo sprintf(
						'
						<div class="tsvg_position_item %s" data-tsvg-pos="%s">
							<img class="tsvg_position_image tsvg_flex_col" src="%s">
						</div>
						',
						$opt_value == $value ? esc_attr('tsvg_active') : esc_attr(''),
						esc_attr($opt_value),
						esc_html($opt_name)
					);
				}
                echo '</div></div>';
				break;
		endswitch;
    }
    echo '
                 </div>
            </div>
        </div>
    ';
}
echo sprintf(
    '
    			</div>
		</div>
		<div class="tsvg_flex_col  tsvg_content_subsection" data-tsvg-subsection="pagination" title="%1$s">
			<div class="tsvg_styles_sidebar">
    ',
	esc_html__( 'Pagination','gallery-videos' )
);
foreach ( $tsvg_pagination_arr as $fields_key => $fields_value ) {
    echo sprintf(
        '
        <div class="tsvg_accordion_item %1$s">
            <header class="tsvg_accordion_header ">
                <i class="ts-vgallery ts-vgallery-angle-right tsvg_accordion_header_icon"></i>
                <h3 class="tsvg_accordion_header_title">%2$s</h3>
            </header>
            <div class="tsvg_accordion_item_content">
                <div class="tsvg_accordion_items tsvg_analytics_flex_c">
        ',
        esc_attr( str_replace( ' ', '-', strtolower( $fields_key ) ) ),
        esc_attr( $fields_key )
    );
    foreach ( $fields_value as $field_key => $field_value ) {
        $fieldname = '';
        $field = '';
        $value = '';
        if ( array_key_exists($field_key, $tsvg_builder_settings_base ) ) {
            $fieldname = $field_key;
            $field = $field_value;
            $value = $tsvg_builder_settings_base[$field_key ];
        } else if (array_key_exists( $field_key, $tsvg_builder_pagination_base )) {
            $fieldname = $field_key;
            $field = $field_value;
            $value = $tsvg_builder_pagination_base[$field_key ];
        }else{
            continue;
        }
        switch ($field['type']):
			case 'range':
				echo sprintf(
					'
					<div class="length tsvg_range_div" data-tsvg-min="%1$s" data-tsvg-max="%2$s" >
						<div class="tsvg_range_div_title tsvg_field_title" data-tsvg-field="%4$s" data-tsvg-length="%3$s(%7$s)">length:</div>
						<label class="tsvg_range_label" for="%4$s">%5$s</label>
						<input id="%4$s" class="tsvg_range_input" type="range" min="%1$s" max="%2$s" value="%3$s" step="%8$s" data-tsvg-change="%6$s" data-tsvg-param="%7$s"  />
					</div>
					',
					esc_attr($field['options']['min']),
					esc_attr($field['options']['max']),
					array_key_exists('time', $field) ? esc_attr(strpos($value, '.')) !== false ? esc_attr($value) : esc_attr($value / 10) : esc_attr($value),
					esc_attr($fieldname),
					esc_attr($field['label']),
					array_key_exists('change', $field) ? esc_attr($field['change']) : '',
					array_key_exists('change_param', $field) ? esc_attr($field['change_param']) : '',
					array_key_exists('step', $field) ? esc_attr($field['step']) : ''
				);
				break;
			case 'text':
			case 'date':
				echo sprintf(
					'<div class="tsvg_select_div">
						<span class="tsvg_select_div_title tsvg_field_title">%1$s</span>
						<input id="%2$s" name="%3$s" type="%4$s" class="tsvg_text_input" value="%5$s" data-tsvg-elem="%6$s" data-change-prop="%7$s"/>
					</div>
					',
					esc_attr($field['label']),
					esc_attr($fieldname),
					esc_attr($fieldname),
					$field['type'] == 'text' ? 'text' : 'date',
					esc_attr($value),
					array_key_exists('change_elem', $field) ? esc_attr($field['change_elem']) : '',
					array_key_exists('change_attr', $field) ? esc_attr($field['change_attr']) : ''
				);
				break;
			case 'color':
				echo sprintf(
					'
					<div class="tsvg_color_div">
						<label class="tsvg_color_label" for="%1$s">%2$s</label>
						<input id="%1$s" name="%1$s" data-tsvg-field="color" value="%3$s" data-tsvg-change="%4$s" />
					</div>
					',
					esc_attr($fieldname),
					esc_attr($field['label']),
					esc_attr($value),
					array_key_exists('change_prop', $field) ? esc_attr($field['change_prop']) : ''
				);
				break;
			case 'input-toggle':
				echo sprintf(
					'
					<div class="tsvg_checkbox_div" data-tsvg-check="%6$s" data-tsvg-uncheck="%7$s">
						<input class="tsvg_checkbox_input" type="checkbox" id="%1$s" name="%1$s" %2$s data-change-elem="%4$s" data-change-prop="%5$s"/>
						<label class="tsvg_checkbox_label" for="%1$s">%3$s</label>
					</div>
					',
					esc_attr($fieldname),
					$value == 'true' ? esc_attr('checked') : '',
					esc_attr($field['label']),
					array_key_exists('change_elem', $field) ? esc_attr($field['change_elem']) : '',
					array_key_exists('change_attr', $field) ? esc_attr($field['change_attr']) : '',
					array_key_exists('yes', $field['options']) ? esc_attr($field['options']['yes']) : 'true',
					array_key_exists('no', $field['options']) ? esc_attr($field['options']['no']) : 'false'
				);
				break;
			case 'select-icon':
				echo sprintf(
					'
					<div class="tsvg_icon_picker_div">
						<label id="%s" for="%s">%s</label>
						<div class="ts-vgallery-icon-picker-wrap" id="%s" data-tsvg-field="%s">
							<ul class="icon-picker">
								%s
								<li id="%s" class="ts-vgallery-select-icon" title="Icon Library"><i class="%s"></i></li>
								<input type="hidden" name="icon_value" id="%s" value="%s" data-tsvg-elem="%s" data-change-prop="%s">
							</ul>
						</div>
					</div>
					',
					esc_attr($fieldname) . '-icon-picker-wrap-label',
					esc_attr($fieldname) . '-icon_value',
					esc_attr($field['label']),
					esc_attr($fieldname) . '-icon-picker-wrap',
					esc_attr($fieldname),
					sprintf('<li class="tsvg-set-icon-none" id="%s" title="None"><i class="ts-vgallery ts-vgallery-ban"></i></li>', esc_attr($fieldname) . '-icon-none'),
					esc_attr($fieldname),
					esc_attr($value),
					esc_attr($fieldname) . '-icon_value',
					esc_attr($value),
					array_key_exists('change_elem', $field) ? esc_attr($field['change_elem']) : '',
					array_key_exists('change_attr', $field) ? esc_attr($field['change_attr']) : ''
				);
				break;
			case 'select':
				echo sprintf(
                    '
                    <div class="tsvg_select_div">
                        <span class="tsvg_select_div_title tsvg_field_title">%s</span>
                        <select id="%s" name="%s" class="%s" data-change-elem="%s" data-change-prop="%s" />
                        ',
                    esc_attr($field['label']),
                    esc_attr($fieldname),
                    esc_attr($fieldname),
                    array_key_exists('change_elem', $field) ? esc_attr('tsvg_elem_data') : esc_attr('tsvg_root_elem'),
                    array_key_exists('change_elem', $field) ? esc_attr($field['change_elem']) : '',
                    array_key_exists('change_attr', $field) ? esc_attr($field['change_attr']) : ''
                );
                foreach ($field['options'] as $opt_value => $opt_name) {
					echo sprintf(
						'<option value="%s" %s>%s</option>',
						esc_attr($opt_value),
						$opt_value == $value ? esc_attr('selected') : esc_attr(''),
						esc_html($opt_name)
					);
				}
                echo '</select></div>';
				break;
			case 'select-position':
				echo sprintf(
					'
					<div class="tsvg_select_div"><span class="tsvg_select_div_title tsvg_field_title">%s</span>
						<div class="tsvg_position_select  tsvg_flex_row" data-tsvg="%s" data-tsvg-select="%s" data-change-elem="%s" data-change-prop="%s">
					',
					esc_attr($field['label']),
					array_key_exists('full', $field['options']) ? 'btn' : 'div',
					esc_attr($fieldname),
					array_key_exists('change_elem', $field) ? esc_attr($field['change_elem']) : '',
					array_key_exists('change_attr', $field) ? esc_attr($field['change_attr']) : ''
				);
                foreach ($field['options'] as $opt_value => $opt_name) {
					echo sprintf(
						'
                        <div class="tsvg_position_item %s" data-tsvg-pos="%s">
                            <p class="tsvg_flex_col">%s</p>
                        </div>
                        ',
						$opt_value == $value ? esc_attr('tsvg_active') : esc_attr(''),
						esc_attr($opt_value),
						esc_html($opt_name)
					);
				}
                echo '</div></div>';
				break;
			case 'select-position-efect':
				echo sprintf(
					'
					<div class="tsvg_select_div tsvg_select_div_ef">
						<span class="tsvg_select_div_title tsvg_field_title">
							%s
						</span>
						<div class="tsvg_position_select tsvg_position_select-ef  tsvg_flex_row" data-tsvg="%s" data-tsvg-select="%s" data-change-elem="%s" data-change-prop="%s">
							<div class="tsvg-style-inner-pro tsvg_flex_col tsvg_flex_col_hide">
								<h2>This photo for illustration. Feature are available in the Professional version of the plugin only.</h2>
								<a href="%s" target="_blank">
									GET THE FULL VERSION
								</a>
							</div>	
					',
					esc_attr($field['label']),
					array_key_exists('full', $field['options']) ? 'btn' : 'div',
					esc_attr($fieldname),
					array_key_exists('change_elem', $field) ? esc_attr($field['change_elem']) : '',
					array_key_exists('change_attr', $field) ? esc_attr($field['change_attr']) : '',
					esc_url("https://total-soft.com/wp-video-gallery/")
				);
                foreach ($field['options'] as $opt_value => $opt_name) {
					echo sprintf(
						'<div class="tsvg_position_item %s" data-tsvg-pos="%s">
							<p class="tsvg_flex_col">
								%s
							</p>
						</div>',
						$opt_value == $value ? esc_attr('tsvg_active') : esc_attr(''),
						esc_attr($opt_value),
						esc_html($opt_name)
					);
				}
                echo '</div></div>';
				break;
			case 'select-position-image':
				echo sprintf(
					'
					<div class="tsvg_select_div">
						<span class="tsvg_select_div_title tsvg_field_title">
							%s
						</span>
						<div class="tsvg_position_select tsvg_flex_row" data-tsvg="image" data-tsvg-select="%s" data-change-elem="%s" data-change-prop="%s">
							<div class="tsvg-style-inner-pro tsvg_flex_col">
								<h2>This photo for illustration. Feature are available in the Professional version of the plugin only.</h2>
								<a href="%s" target="_blank">
									GET THE FULL VERSION
								</a>
							</div>	
					',
					esc_attr($field['label']),
					esc_attr($fieldname),
					array_key_exists('change_elem', $field) ? esc_attr($field['change_elem']) : '',
					array_key_exists('change_attr', $field) ? esc_attr($field['change_attr']) : '',
					esc_url("https://total-soft.com/wp-video-gallery/")
				);
				foreach ($field['options'] as $opt_value => $opt_name) {
					echo sprintf(
						'
						<div class="tsvg_position_item %s" data-tsvg-pos="%s">
							<img class="tsvg_position_image tsvg_flex_col" src="%s">
						</div>
						',
						$opt_value == $value ? esc_attr('tsvg_active') : esc_attr(''),
						esc_attr($opt_value),
						esc_html($opt_name)
					);
				}
                echo '</div></div>';
				break;
		endswitch;
    }
    echo '
                </div>
            </div>
        </div>
    ';
}
echo sprintf(
    '
    			</div>
		</div>
		<div class="tsvg_flex_col tsvg_content_subsection" data-tsvg-subsection="shortcode">
			<div class="tsvg_flex_col" data-tsvg-field="shortcode" >
				<p>%1$s</p>
				<div class="tsvg_flex_row tsvg_shortcode_div">
					<input type="text" id="tsvg_global_shortcode" disabled="">
					<span class="ts-vgallery ts-vgallery-files-o" data-tsvg-copy="tsvg_global_shortcode"></span>
				</div>
			</div>
			<div class="tsvg_flex_col" data-tsvg-field="shortcode" >
				<p>%2$s</p>
				<div class="tsvg_flex_row tsvg_shortcode_div">
					<input type="text" id="tsvg_global_theme_shortcode" disabled="">
					<span class="ts-vgallery ts-vgallery-files-o" data-tsvg-copy="tsvg_global_theme_shortcode"></span>
				</div>
			</div>
			<div class="tsvg_flex_col" data-tsvg-field="notice" >
				<div class="tsvg_notice_div">
					<p>%3$s</p>
				</div>
			</div>
			<div class="tsvg_flex_col" data-tsvg-field="title" >
				<p>%4$s</p>
				<div class="tsvg_flex_row tsvg_shortcode_div">
					<input type="text" id="tsvg_global_title" value="%5$s">
				</div>
			</div>
		</div>
	</div>
    ',
	esc_html__( 'Copy & paste the shortcode directly into any WordPress post or page.','gallery-videos' ),
	esc_html__( 'Copy & paste this code into a template file to include the video gallery within your theme.','gallery-videos' ),
	esc_html__( 'Please save gallery for getting shortcode.','gallery-videos' ),
	esc_html__( 'Video gallery title','gallery-videos' ),
    esc_html( html_entity_decode( htmlspecialchars_decode( $this->tsvg_build_proporties['TS_VG_Title'] ), ENT_QUOTES ) )
);
