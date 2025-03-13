<?php
class TS_Video_Gallery_Admin extends TS_Video_Gallery_Function{
	private $plugin_name;
	private $version;
	public $tsvg_admin_manager;
	public $tsvg_build;
	public $tsvg_build_proporties;
	public $tsvg_build_id;
	private $tsvg_page_slug;
	private $tsvg_themes;
	private $tsvg_styles_array;
	private $tsvg_settings_array;
	private $tsvg_option_styles_array;
	private $tsvg_themes_links;
	protected $tsvg_function_class;
	public function __construct($plugin_name, $version){
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		if (isset($_GET) && isset($_GET['page'])) {
			if (sanitize_text_field(wp_unslash($_GET['page'])) === 'tsvg-admin' || sanitize_text_field(wp_unslash($_GET['page'])) === 'tsvg-builder' || sanitize_text_field(wp_unslash($_GET['page'])) === 'tsvg-pro' || sanitize_text_field(wp_unslash($_GET['page'])) === 'tsvg-add-ons') {
				$this->tsvg_page_slug = sanitize_text_field(wp_unslash($_GET['page']));
			}
		}
		if ($this->tsvg_page_slug == 'tsvg-builder' && is_admin()) {
			add_action('init', [$this, 'tsvg_process_requests']);
		}
		add_action('wp_ajax_tsvg_check_attachment', array($this, 'tsvg_get_attachment_callback'));
		add_action('wp_ajax_tsvg_get_attachment_id', array($this, 'tsvg_get_attachment_id_callback'));
		add_filter('plugin_action_links_' . TSVG_PLUGIN_BASENAME, array($this, 'tsvg_add_action_link'));
		add_filter('set-screen-option', array($this, 'tsvg_set_screen'), 10, 3);
		require_once ABSPATH . 'wp-admin/includes/file.php';
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		global $wp_filesystem;
		WP_Filesystem();
		add_action( 'admin_notices', array($this, 'tsvg_push_notice') );
		add_action( 'admin_init', array($this, 'tsvg_dismissed_notice') );
	}
	function tsvg_push_notice() {
		if ($this->tsvg_page_slug === 'tsvg-admin') {
			$tsvg_dismissed_meta = get_user_meta(  get_current_user_id(), 'tsvg_dismissed_notice' );
			$tsvg_remind_me_meta = get_user_meta(  get_current_user_id(), 'tsvg_remindme_notice' );
			$tsvg_dismissed = !$tsvg_dismissed_meta || $tsvg_dismissed_meta[0] !== $this->version;
			$tsvg_remind_me = !$tsvg_remind_me_meta || time()+(get_option('gmt_offset') * 3600) - (int) $tsvg_remind_me_meta[0] > 86400;
			if ( $tsvg_dismissed && $tsvg_remind_me) {
				echo sprintf(
					'
					<div class="tsvg-banner">
						<div class="tsvg-banner-container">
							<svg class="tsvg-banner-circle tsvg-banner-circle-a" height="160" width="160">
								<circle cx="80" cy="80" r="80" />
							</svg>
							<svg class="tsvg-banner-circle tsvg-banner-circle-b" height="60" width="60">
								<circle cx="30" cy="30" r="30" />
							</svg>
							<svg class="tsvg-banner-circle tsvg-banner-circle-c" height="600" width="600">
								<circle cx="300" cy="300" r="300" />
							</svg>
							<svg class="tsvg-banner-circle tsvg-banner-circle-d" height="60" width="60">
								<circle cx="30" cy="30" r="30" />
							</svg>
							<img class="tsvg-banner-img" src="%1$s" />
							<div class="tsvg-banner-content">
								<p class="tsvg-banner-text">TS Poll - Survey, Versus Poll, Image Poll, Video Poll</p>
								<a target="_blank" href="%2$s" class="tsvg-banner-link">WP Plugin</a>
								<a target="_blank" href="%3$s" class="tsvg-banner-link">See demos</a>
							</div>
						</div>
						<div class="tsvg-banner-btns">
							<a class="tsvg-banner-btn tsvg-remind-btn" href="%4$s">%5$s</a>
							<a class="tsvg-banner-btn tsvg-dismiss-btn" href="%6$s">%7$s</a>
						</div>
					</div>
					',
					esc_url(plugin_dir_url( __FILE__ ) . 'img/ts-poll-logo.png'),
					esc_url('https://wordpress.org/plugins/poll-wp/'),
					esc_url('https://poll-wp.total-soft.com/'),
					esc_url( add_query_arg( 'tsvg-remind-me', 'true' ) ),
					__('Remind me later'),
					esc_url( add_query_arg( 'tsvg-dismissed', 'true' ) ),
					__('Dismiss')
				);
			}
		}
	}
	function tsvg_dismissed_notice() {
		if ( isset( $_GET['tsvg-dismissed'] ) || isset( $_GET['tsvg-remind-me'] )){
			if ( isset( $_GET['tsvg-dismissed'] )){
				$tsvg_dismissed_meta = get_user_meta(  get_current_user_id(), 'tsvg_dismissed_notice' );
				!$tsvg_dismissed_meta ? add_user_meta( get_current_user_id(), 'tsvg_dismissed_notice', $this->version, true ) : update_user_meta( get_current_user_id(), 'tsvg_dismissed_notice', $this->version );
			}else if (isset( $_GET['tsvg-remind-me'] )) {
				$tsvg_remind_me_meta = get_user_meta(  get_current_user_id(), 'tsvg_remindme_notice' );
				!$tsvg_remind_me_meta ? add_user_meta( get_current_user_id(), 'tsvg_remindme_notice', time()+(get_option('gmt_offset') * 3600), true ) : update_user_meta( get_current_user_id(), 'tsvg_remindme_notice', time()+(get_option('gmt_offset') * 3600) ) ;
			}
			wp_redirect(wp_get_referer());
			exit;
		}
	}
	public function tsvg_process_requests(){
		$this->tsvg_themes = array('grid_video_gallery'=>'Grid Video Gallery','lightbox_video_gallery'=>'LightBox Video Gallery','thumbnails_video'=>'Thumbnails Video','content_popup'=>'Content Popup','elastic_gallery'=>'Elastic Gallery','fancy_gallery'=>'Fancy Gallery','parallax_engine'=>'Parallax Engine','classic_gallery'=>'Classic Gallery','space_gallery'=>'Space Gallery','effective_gallery'=>'Effective Gallery','gallery_album'=>'Gallery Album','video_portfolio'=>'Video Portfolio','image_portfolio'=>'Image Portfolio','image_gallery'=>'Image Gallery','mix_portfolio'=>'Mix Portfolio');
		$this->tsvg_styles_array = array('TotalSoft_GV_1_01','TotalSoft_GV_1_02','TotalSoft_GV_1_03','TotalSoft_GV_1_04','TotalSoft_GV_1_05','TotalSoft_GV_1_06','TotalSoft_GV_1_07','TotalSoft_GV_1_08','TotalSoft_GV_1_09','TotalSoft_GV_1_10','TotalSoft_GV_1_11','TotalSoft_GV_1_12','TotalSoft_GV_1_13','TotalSoft_GV_1_14','TotalSoft_GV_1_15','TotalSoft_GV_1_16','TotalSoft_GV_1_PT','TotalSoft_GV_1_17','TotalSoft_GV_1_18','TotalSoft_GV_1_19','TotalSoft_GV_1_20','TotalSoft_GV_1_21','TotalSoft_GV_1_22','TotalSoft_GV_1_23','TotalSoft_GV_1_24','TotalSoft_GV_1_25','TotalSoft_GV_1_26','TotalSoft_GV_1_27','TotalSoft_GV_1_28','TotalSoft_GV_1_29','TotalSoft_GV_1_30','TotalSoft_GV_1_31','TotalSoft_GV_1_32','TotalSoft_GV_1_33','TotalSoft_GV_1_34','TotalSoft_GV_1_35','TotalSoft_GV_1_36','TotalSoft_GV_1_37','TotalSoft_GV_1_38','TotalSoft_GV_1_39','TotalSoft_GV_1_40','TotalSoft_GV_2_01','TotalSoft_GV_2_02','TotalSoft_GV_2_03','TotalSoft_GV_2_04','TotalSoft_GV_2_05','TotalSoft_GV_2_06','TotalSoft_GV_2_07','TotalSoft_GV_2_08','TotalSoft_GV_2_09','TotalSoft_GV_2_10','TotalSoft_GV_2_11','TotalSoft_GV_FG_PT','TotalSoft_GV_FG_PD','TotalSoft_GV_2_12','TotalSoft_GV_2_13','TotalSoft_GV_2_14','TotalSoft_GV_2_15','TotalSoft_GV_2_16','TotalSoft_GV_2_17','TotalSoft_GV_2_18','TotalSoft_GV_2_19','TotalSoft_GV_2_20','TotalSoft_GV_2_21','TotalSoft_GV_2_22','TotalSoft_GV_2_23','TotalSoft_GV_2_24','TotalSoft_GV_2_25','TotalSoft_GV_2_26','TotalSoft_GV_2_27','TotalSoft_GV_2_28','TotalSoft_GV_2_29','TotalSoft_GV_2_30','TotalSoft_GV_2_31','TotalSoft_GV_2_32','TotalSoft_GV_2_33','TotalSoft_GV_2_34','TotalSoft_GV_2_35','TotalSoft_GV_2_36','TotalSoft_GV_2_37','TotalSoft_GV_2_38','TotalSoft_GV_2_39');
		$this->tsvg_settings_array = array('TotalSoft_VGallery_Set_01','TotalSoft_VGallery_Set_02','TotalSoft_VGallery_Set_03','TotalSoft_VGallery_Set_04','TotalSoft_VGallery_Set_05','TotalSoft_VGallery_Set_06','TotalSoft_VGallery_Set_07','TotalSoft_VGallery_Set_08');
		$this->tsvg_option_styles_array = array('TotalSoft_VGallery_Sty_01','TotalSoft_VGallery_Sty_02','TotalSoft_VGallery_Sty_03','TotalSoft_VGallery_Sty_04','TotalSoft_VGallery_Sty_05','TotalSoft_VGallery_Sty_06','TotalSoft_VGallery_Sty_07','TotalSoft_VGallery_Sty_08','TotalSoft_VGallery_Sty_09','TotalSoft_VGallery_Sty_10','TotalSoft_VGallery_Sty_11','TotalSoft_VGallery_Sty_12','TotalSoft_VGallery_Sty_13','TotalSoft_VGallery_Sty_14','TotalSoft_VGallery_Sty_15','TotalSoft_VGallery_Sty_16','TotalSoft_VGallery_Sty_17','TotalSoft_VGallery_Sty_18','TotalSoft_VGallery_Sty_19','TotalSoft_VGallery_Sty_20','TotalSoft_VGallery_Sty_21','TotalSoft_VGallery_Sty_22','TotalSoft_VGallery_Sty_23','TotalSoft_VGallery_Sty_24','TotalSoft_VGallery_Sty_25','TotalSoft_VGallery_Sty_26','TotalSoft_VGallery_Sty_27');
		if ( isset($_POST) && isset($_POST["tsvg_nonce"]) && isset($_POST['tsvg_videos_order']) && isset($_POST['tsvg_theme']) && isset($_POST['tsvg_deleted_videos']) && isset($_POST["tsvg_id"]) && isset($_POST["tsvg_title"]) ) {
			if (sanitize_text_field(wp_unslash($_POST['tsvg_nonce'])) === '' || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tsvg_nonce'])), 'tsvg_builder_nonce_field')) {
				echo 'TS Video Gallery - nonce error.';
				die();
			}
			$tsvg_id = sanitize_text_field(wp_unslash($_POST['tsvg_id']));
			$tsvg_styles = $tsvg_settings = $tsvg_option_styles = $tsvg_options = $tsvg_order_array = $tsvg_videos = [];
			if (is_numeric($tsvg_id) || array_key_exists($tsvg_id, $this->tsvg_themes)) {
				global $wpdb;
				$tsvg_db_manager_table = esc_sql($wpdb->prefix . 'ts_galleryv_manager');
				$tsvg_db_videos_table = esc_sql($wpdb->prefix . 'ts_galleryv_videos');
				foreach ($this->tsvg_styles_array as $key) {
					$tsvg_styles[$key] = isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : '';
				}
				foreach ($this->tsvg_settings_array as $key) {
					$tsvg_settings[$key] = isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : '';
				}
				foreach ($this->tsvg_option_styles_array as $key) {
					$tsvg_option_styles[$key] = isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : '';
				}
				$tsvg_title = sanitize_text_field(wp_unslash($_POST['tsvg_title']));
				$tsvg_videos_order = explode("|", sanitize_text_field(wp_unslash($_POST['tsvg_videos_order'])));
				$tsvg_deleted_videos = sanitize_text_field(wp_unslash($_POST['tsvg_deleted_videos']));
				$tsvg_deleted_videos = $tsvg_deleted_videos == '' ? [] : explode("|", $tsvg_deleted_videos);
				$tsvg_theme = sanitize_text_field(wp_unslash($_POST['tsvg_theme']));
				$tsvg_options['TS_vgallery_Q_Theme'] = $tsvg_theme;
				foreach ($tsvg_videos_order as $video_id) {
					$tsvg_videos[] = [
						'id'            => $video_id,
						'TS_VG_SetType' => isset($_POST['TS_VG_SetType-' . $video_id]) ? sanitize_text_field(wp_unslash($_POST['TS_VG_SetType-' . $video_id])) : '',
						'TS_VG_SetName' => isset($_POST['TS_VG_SetName-' . $video_id]) ? htmlentities(sanitize_text_field(wp_unslash($_POST['TS_VG_SetName-' . $video_id])), ENT_QUOTES) : '',
						'TS_VG_Options' => [
							'TotalSoftVGallery_Vid_Im'    => isset($_POST['TotalSoftVGallery_Vid_Im-' . $video_id]) ? sanitize_url(wp_unslash($_POST['TotalSoftVGallery_Vid_Im-' . $video_id])) : '',
							'TotalSoftVGallery_Vid_Vd'    => isset($_POST['TotalSoftVGallery_Vid_Vd-' . $video_id]) ? sanitize_url(wp_unslash($_POST['TotalSoftVGallery_Vid_Vd-' . $video_id])) : '',
							'TotalSoftVGallery_Vid_vont'  => isset($_POST['TotalSoftVGallery_Vid_vont-' . $video_id]) ? sanitize_text_field(wp_unslash($_POST['TotalSoftVGallery_Vid_vont-' . $video_id])) : '',
							'TotalSoftVGallery_Vid_vd_em' => '',
							'TotalSoftVGallery_Vid_desc'  => isset($_POST['TotalSoftVGallery_Vid_desc-' . $video_id]) ? wp_kses_post(wp_unslash($_POST['TotalSoftVGallery_Vid_desc-' . $video_id])) : '',
							'TotalSoftVGallery_Vid_link'  => isset($_POST['TotalSoftVGallery_Vid_link-' . $video_id]) ? sanitize_url(wp_unslash($_POST['TotalSoftVGallery_Vid_link-' . $video_id])) : '',
							'TotalSoftVGallery_Vid_Cl'    => isset($_POST['TotalSoftVGallery_Vid_Cl-' . $video_id]) ? sanitize_text_field(wp_unslash($_POST['TotalSoftVGallery_Vid_Cl-' . $video_id])) : ''
						]
					];
				}
				if (array_key_exists($tsvg_id, $this->tsvg_themes)) {
					$wpdb->insert(
						$tsvg_db_manager_table,
						array(
							'id'                 => '',
							'TS_VG_Title'        => $tsvg_title,
							'TS_VG_Option'       => json_encode( $tsvg_options ),
							'TS_VG_Style'        => json_encode( $tsvg_styles ),
							'TS_VG_Settings'     => json_encode( $tsvg_settings ),
							'TS_VG_Option_Style' => json_encode( $tsvg_option_styles ),
							'TS_VG_Sort'         => '',
							'TS_VG_Old_User'     => 'no',
							'created_at'         => gmdate( 'd.m.Y h:i:sa' ),
							'updated_at'         => gmdate( 'd.m.Y h:i:sa' )
						),
						array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
					);
					$tsvg_insert_id = $wpdb->insert_id;
					foreach ($tsvg_videos as $tsvg_video) {
						$wpdb->insert(
							$tsvg_db_videos_table,
							array(
								'id'            => '',
								'TS_VG_SetType' => (int) $tsvg_insert_id,
								'TS_VG_SetName' => $tsvg_video['TS_VG_SetName'],
								'TS_VG_Options' => json_encode($tsvg_video['TS_VG_Options'])
							),
							array('%d', '%d', '%s', '%s')
						);
						$tsvg_order_array[] = $wpdb->insert_id;
					}
					$wpdb->update($tsvg_db_manager_table, array('TS_VG_Sort' => implode(',', $tsvg_order_array)), array('id' => (int) $tsvg_insert_id), array('%s'), array('%d'));
					if (wp_safe_redirect(add_query_arg('tsvg-id', $tsvg_insert_id, admin_url('admin.php?page=tsvg-builder')))) exit();
				} else {
					foreach ($tsvg_videos as $tsvg_video) {
						if (strpos($tsvg_video['id'], 'new') !== false) {
							$wpdb->insert(
								$tsvg_db_videos_table,
								array(
									'id'            => '',
									'TS_VG_SetType' => (int) $tsvg_id,
									'TS_VG_SetName' => $tsvg_video['TS_VG_SetName'],
									'TS_VG_Options' => json_encode($tsvg_video['TS_VG_Options'])
								),
								array('%d', '%d', '%s', '%s')
							);
							$tsvg_order_array[] = $wpdb->insert_id;
						} else {
							$wpdb->update(
								$tsvg_db_videos_table,
								array(
									'TS_VG_SetName' => $tsvg_video['TS_VG_SetName'],
									'TS_VG_Options' => json_encode($tsvg_video['TS_VG_Options'])
								),
								array('id' => (int) $tsvg_video['id']),
								array('%s', '%s'),
								array('%d')
							);
							$tsvg_order_array[] = (int) $tsvg_video['id'];
						}
					}
					if (is_array($tsvg_deleted_videos) && count($tsvg_deleted_videos) > 0) {
						foreach ($tsvg_deleted_videos as $deleted_video) {
							if (strpos($deleted_video, 'new') === false) {
								$wpdb->delete(
									$tsvg_db_videos_table,
									array('id' => (int) $deleted_video),
									array('%d')
								);
							}
						}
					}
					$wpdb->update(
						$tsvg_db_manager_table,
						array(
							'TS_VG_Title'        => $tsvg_title,
							'TS_VG_Option'       => json_encode($tsvg_options),
							'TS_VG_Style'        => json_encode($tsvg_styles),
							'TS_VG_Settings'     => json_encode($tsvg_settings),
							'TS_VG_Option_Style' => json_encode($tsvg_option_styles),
							'TS_VG_Sort'         => implode(',', $tsvg_order_array),
							'updated_at'         => gmdate('d.m.Y h:i:sa'),
						),
						array('id' => (int) $tsvg_id),
						array('%s', '%s', '%s', '%s', '%s', '%s'),
						array('%d')
					);
					if (wp_safe_redirect(add_query_arg('tsvg-id', $tsvg_id, admin_url('admin.php?page=tsvg-builder')))) exit();
				}
			} else {
				echo 'TS Video Gallery - unexpected error.';
				die();
			}
		}
		if (isset($_GET['tsvg-id']) || isset($_GET['tsvg-theme'])) {
			$this->tsvg_function_class = new TS_Video_Gallery_Function();
			if (isset($_GET['tsvg-id'])) {
				$tsvg_get_id = sanitize_text_field(wp_unslash($_GET['tsvg-id']));
				if (is_numeric($tsvg_get_id) && is_int((int) $tsvg_get_id) && (int) $tsvg_get_id > 0) {
					global $wpdb;
					$tsvg_db_manager_table = esc_sql($wpdb->prefix . 'ts_galleryv_manager');
					$tsvg_db_videos_table = esc_sql($wpdb->prefix . 'ts_galleryv_videos');
					$tsvg_get_record = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tsvg_db_manager_table} WHERE id = %d ", $tsvg_get_id), ARRAY_A);
					if (is_array($tsvg_get_record)) {
						$tsvg_get_record['TS_VG_Title'] = html_entity_decode(htmlspecialchars_decode($tsvg_get_record['TS_VG_Title']), ENT_QUOTES);
						$tsvg_record_style = json_decode($tsvg_get_record['TS_VG_Style'], true);
						$tsvg_record_settings = json_decode($tsvg_get_record['TS_VG_Settings'], true);
						$tsvg_record_option_style = json_decode($tsvg_get_record['TS_VG_Option_Style'], true);
						foreach ($tsvg_record_style as $key => $value) {
							$tsvg_record_style[$key] = html_entity_decode(htmlspecialchars_decode($value), ENT_QUOTES);
						}
						foreach ($tsvg_record_settings as $key => $value) {
							$tsvg_record_settings[$key] = html_entity_decode(htmlspecialchars_decode($value), ENT_QUOTES);
						}
						foreach ($tsvg_record_option_style as $key => $value) {
							$tsvg_record_option_style[$key] = html_entity_decode(htmlspecialchars_decode($value), ENT_QUOTES);
						}
						$tsvg_get_record['TS_VG_Style'] = json_encode($tsvg_record_style, true);
						$tsvg_get_record['TS_VG_Option_Style'] = json_encode($tsvg_record_option_style, true);
						$tsvg_get_record['TS_VG_Old_User'] = html_entity_decode(htmlspecialchars_decode($tsvg_get_record['TS_VG_Old_User']), ENT_QUOTES);
						$tsvg_get_record['TS_VG_Settings'] = json_encode($tsvg_record_settings, true);
						$tsvg_get_video_records = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$tsvg_db_videos_table} WHERE TS_VG_SetType = %d ", (int) $tsvg_get_id) , ARRAY_A);
						foreach ($tsvg_get_video_records as $key => $value) {
							$tsvg_get_video_records[$key]['TS_VG_SetName'] = html_entity_decode(htmlspecialchars_decode($value['TS_VG_SetName']), ENT_QUOTES);
						}
						$tsvg_get_record['tsvg_video_records'] = $tsvg_get_video_records;
						$tsvg_get_record['TS_VG_Style'] = json_decode($tsvg_get_record['TS_VG_Style'], true);
						$this->tsvg_build = 'edit';
						$this->tsvg_build_proporties = $tsvg_get_record;
						$this->tsvg_build_id = $tsvg_get_id;
					} else {
						$this->tsvg_build = 'not';
					}
				} else {
					$this->tsvg_build = 'not';
				}
			} else if (isset($_GET['tsvg-theme']) && array_key_exists(sanitize_text_field(wp_unslash($_GET['tsvg-theme'])), $this->tsvg_themes)) {
				$this->tsvg_build_id = sanitize_text_field(wp_unslash($_GET['tsvg-theme']));
				$this->tsvg_build = 'edit';
				$tsvg_default_data = $this->tsvg_function_class->tsvg_get_all_params();
				$tsvg_default_data['TS_VG_Option']['TS_vgallery_Q_Theme'] = $this->tsvg_themes[$this->tsvg_build_id];
				$tsvg_default_data['Videos'] = array_values($tsvg_default_data['Videos']);
				foreach ($tsvg_default_data['Videos'] as $key => $value) {
					$tsvg_default_data['Videos'][$key]['TS_VG_SetType'] = $this->tsvg_build_id;
					$tsvg_default_data['Videos'][$key]['TS_VG_Options'] = json_encode($tsvg_default_data['Videos'][$key]['TS_VG_Options']);
				}
				$tsvg_theme_default_data = $this->tsvg_function_class->tsvg_get_theme_params($this->tsvg_build_id);
				$this->tsvg_build_proporties = array(
					'id'                 => $this->tsvg_build_id,
					'TS_VG_Title'        => $tsvg_default_data['TS_VG_Title'],
					'TS_VG_Settings'     => json_encode($tsvg_default_data['TS_VG_Settings']),
					'TS_VG_Option_Style' => json_encode($tsvg_default_data['TS_VG_Style']),
					'TS_VG_Option'       => json_encode($tsvg_default_data['TS_VG_Option']),
					'TS_VG_Style'        => $tsvg_theme_default_data,
					'TS_VG_Sort'         => $tsvg_default_data['TS_VG_Sort'],
					'TS_VG_Old_User'     => 'no',
					'created_at'         => gmdate('d.m.Y h:i:sa'),
					'updated_at'         => gmdate('d.m.Y h:i:sa'),
					'tsvg_video_records' => $tsvg_default_data['Videos']
				);
			} else {
				$this->tsvg_build = '404';
			}
		} else {
			$this->tsvg_build = 'new';
			$this->tsvg_themes_links = array(
				'grid_video_gallery'     => 'wp-video-gallery-grid',
				'lightbox_video_gallery' => 'wp-video-gallery-lightbox',
				'thumbnails_video'       => 'wp-video-gallery-thumbnails',
				'content_popup'          => 'wp-video-gallery-content-popup',
				'elastic_gallery'        => 'wp-video-gallery-elastic',
				'fancy_gallery'          => 'wp-video-gallery-fancy',
				'parallax_engine'        => 'wp-video-gallery-parallax',
				'classic_gallery'        => 'wp-video-gallery-classic',
				'space_gallery'          => 'wp-video-gallery-space',
				'effective_gallery'		 => 'wp-video-gallery-effective',
				'gallery_album'			 => 'wp-video-gallery-album',
				'video_portfolio'        => 'wp-video-portfolio-1',
				'image_portfolio'        => 'wp-video-image-portfolio-1',
				'image_gallery'          => 'wp-video-image-gallery-1',
				'mix_portfolio'          => 'wp-video-mix-portfolio-1'
			);
		}
	}
	public function tsvg_add_action_link($links){
		$links['tsvgallery_support'] = sprintf('<a href="%1$s" style="color: #8bc34a;font-weight: bold;" target="_blank">Support</a>', esc_url('https://wordpress.org/support/plugin/gallery-videos/'));
		$links['tsvgallery_go_pro']  = sprintf('<a href="%1$s" style="color: #ff0000;font-weight: bold;" target="_blank">Go Pro</a>', esc_url('https://total-soft.com/wp-video-gallery/'));
		return $links;
	}
	public function enqueue_styles(){
		wp_enqueue_style('tsvg-fonts', plugin_dir_url(__DIR__) . 'public/css/tsvg-fonts.css', array(), time(), 'all');
		if ($this->tsvg_page_slug == 'tsvg-admin') {
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/tsvg-admin.css', array(), time(), 'all');
		}
		if ($this->tsvg_page_slug == 'tsvg-builder') {
			wp_enqueue_style('tsvg-toastr', plugin_dir_url(__FILE__) . 'css/tsvg-toastr.min.css', array(), time(), 'all');
			wp_enqueue_style('tsvg-builder', plugin_dir_url(__FILE__) . 'css/tsvg-builder.css', array(), time(), 'all');
			if ($this->tsvg_build == 'edit') {
				wp_enqueue_style('tsvg-builder-edit', plugin_dir_url(__FILE__) . 'css/tsvg-edit.css', array(), time(), 'all');
				wp_enqueue_style('tsvg-icon-picker', plugin_dir_url(__FILE__) . 'css/tsvg-aesthetic-icon-picker.css', array(), time(), 'all');
				wp_enqueue_style('tsvg-color-picker', plugin_dir_url(__FILE__) . 'css/tsvg-spectrum.css', array(), time(), 'all');
			} elseif ($this->tsvg_build == 'new') {
				wp_enqueue_style('tsvg-builder-new', plugin_dir_url(__FILE__) . 'css/tsvg-new.css', array(), time(), 'all');
			}
		}
		if ($this->tsvg_page_slug == 'tsvg-pro') {
			wp_enqueue_style( "tsvg-pro", plugin_dir_url( __FILE__ ) . 'css/tsvg-pro.css', array(), time(), 'all' );
		}
		if ($this->tsvg_page_slug == 'tsvg-add-ons') {
			wp_enqueue_style( "tsvg-add-ons", plugin_dir_url( __FILE__ ) . 'css/tsvg-addons.css', array(), time(), 'all' );
			$tsvg_inline_style = sprintf(
				'
				@font-face {
					font-family: "Source Sans Pro";
					font-style: normal;
					font-weight: 200;
					src: url(%1$s) format("truetype");
				}
				@font-face {
					font-family: "Source Sans Pro";
					font-style: normal;
					font-weight: 300;
					src: url(%2$s) format("truetype");
				}
				@font-face {
					font-family: "Source Sans Pro";
					font-style: normal;
					font-weight: 400;
					src: url(%3$s) format("truetype");
				}
				@font-face {
					font-family: "Source Sans Pro";
					font-style: normal;
					font-weight: 600;
					src: url(%4$s) format("truetype");
				}
				@font-face {
					font-family: "Source Sans Pro";
					font-style: normal;
					font-weight: 700;
					src: url(%5$s) format("truetype");
				}
				@font-face {
					font-family: "Source Sans Pro";
					font-style: normal;
					font-weight: 900;
					src: url(%6$s) format("truetype");
				}
				',
				esc_url("https://fonts.gstatic.com/s/sourcesanspro/v21/6xKydSBYKcSV-LCoeQqfX1RYOo3i94_wlxdr.ttf"),
				esc_url("https://fonts.gstatic.com/s/sourcesanspro/v21/6xKydSBYKcSV-LCoeQqfX1RYOo3ik4zwlxdr.ttf"),
				esc_url("https://fonts.gstatic.com/s/sourcesanspro/v21/6xK3dSBYKcSV-LCoeQqfX1RYOo3qOK7g.ttf"),
				esc_url("https://fonts.gstatic.com/s/sourcesanspro/v21/6xKydSBYKcSV-LCoeQqfX1RYOo3i54rwlxdr.ttf"),
				esc_url("https://fonts.gstatic.com/s/sourcesanspro/v21/6xKydSBYKcSV-LCoeQqfX1RYOo3ig4vwlxdr.ttf"),
				esc_url("https://fonts.gstatic.com/s/sourcesanspro/v21/6xKydSBYKcSV-LCoeQqfX1RYOo3iu4nwlxdr.ttf")
			);
			wp_add_inline_style("tsvg-add-ons", $tsvg_inline_style);
		}

	}
	public function enqueue_scripts(){
		if ($this->tsvg_page_slug == 'tsvg-admin') {
			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/tsvg-admin.js', array('jquery'), time(), false);
		}
		if ($this->tsvg_page_slug == 'tsvg-builder') {
			wp_enqueue_media();
			wp_register_script('tsvg-toastr', plugin_dir_url(__FILE__) . 'js/tsvg-toastr.min.js', array(), time(), false);
			wp_enqueue_script('tsvg-color-picker', plugin_dir_url(__FILE__) . 'js/tsvg-spectrum.js', array(), time(), false);
			wp_enqueue_script('tsvg-builder', plugin_dir_url(__FILE__) . 'js/tsvg-builder.js', array('jquery', 'tsvg-toastr', 'jquery-ui-sortable', 'tsvg-color-picker'), time(), true);
			if ($this->tsvg_build == 'edit') {
				$tsvg_videos = array();
				$this->tsvg_build_proporties['tsvg_video_records'] = array_column($this->tsvg_build_proporties['tsvg_video_records'], null, 'id');
				foreach ($this->tsvg_build_proporties['tsvg_video_records'] as $key => $value) :
					$this->tsvg_build_proporties['tsvg_video_records'][$key]['TS_VG_Options'] = json_decode($value['TS_VG_Options']);
					$this->tsvg_build_proporties['tsvg_video_records'][$key]['TS_VG_Options']->TotalSoftVGallery_Vid_desc = wp_unslash(htmlspecialchars_decode($this->tsvg_build_proporties['tsvg_video_records'][$key]['TS_VG_Options']->TotalSoftVGallery_Vid_desc));
				endforeach;
				wp_localize_script(
					'tsvg-builder',
					'tsvg_builder_object',
					array(
						'ajaxurl'         => admin_url('admin-ajax.php'),
						'tsvg_nonce'      => wp_create_nonce('tsvg_builder_nonce_field'),
						'tsvg_proporties' => $this->tsvg_build_proporties,
						'tsvg_id'         => $this->tsvg_build_id,
						'tsvg_creation'   => isset($_GET['tsvg-theme']) ? 'save' : 'update',
						'fonts'           => $this->tsvg_function_class->tsvg_get_all_fonts(),
						'tsvg_svg_move'   => esc_url(plugin_dir_url(__FILE__) . 'img/move.svg'),
						'tsvg_svg_remove' => esc_url(plugin_dir_url(__FILE__) . 'img/recycle.svg'),
						'tsvg_svg_edit'   => esc_url(plugin_dir_url(__FILE__) . 'img/edit.svg'),
						'tsvg_svg_copy'   => esc_url(plugin_dir_url(__FILE__) . 'img/copy.svg'),
						'tsvg_no_img'     => esc_url(plugin_dir_url(__DIR__) . 'public/img/tsvg_no_img.jpg'),
						'tsvg_no_iframe'  => esc_url("https://www.youtube.com/embed/IxxHeAUtcS4"),
						'tsvg_image_load' => esc_url(plugin_dir_url(__DIR__) . 'public/img/loading.gif'),
						'tsvg_no_video'   => esc_url(plugin_dir_url(__DIR__) . 'public/img/tsvg_no_video.png')
					)
				);
			}
		}
		if ($this->tsvg_page_slug == 'tsvg-add-ons') {
			wp_enqueue_script( "tsvg-add-ons", plugin_dir_url( __FILE__ ) . 'js/tsvg-addons.js', array( 'jquery'), time(), true );
		}
	}
	public static function tsvg_set_screen($status, $option, $value){
		return $value;
	}
	function tsvg_get_attachment_callback(){
		if (! wp_unslash(isset($_POST['tsvg_nonce'])) || sanitize_text_field(wp_unslash($_POST['tsvg_nonce'])) === '' || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tsvg_nonce'])), 'tsvg_builder_nonce_field')) {
			wp_send_json_error();
		}
		$tsvg_attachment_url = sanitize_text_field(wp_unslash($_POST['attachment_url']));
		if (is_numeric(attachment_url_to_postid($tsvg_attachment_url)) && attachment_url_to_postid($tsvg_attachment_url) != 0) {
			wp_send_json_success(attachment_url_to_postid($tsvg_attachment_url));
		} else {
			wp_send_json_error();
		}
	}
	function tsvg_get_attachment_id_callback(){
		if (! isset($_POST['tsvg_nonce']) || sanitize_text_field(wp_unslash($_POST['tsvg_nonce'])) === '' || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tsvg_nonce'])), 'tsvg_builder_nonce_field')) {
			wp_send_json_error();
		}
		global $wp_filesystem;
		$tsvg_attachment_url = sanitize_text_field(wp_unslash($_POST['attachment_url']));
		$tsvg_attachment_fopen = $wp_filesystem->get_contents( $tsvg_attachment_url );
		if ($tsvg_attachment_fopen) {
			list($width, $height) = getimagesize($tsvg_attachment_url);
			$data = array(
				'image'  => esc_url($tsvg_attachment_url),
				'width'  => esc_html($width),
				'height' => esc_html($height),
			);
			if (is_numeric(attachment_url_to_postid($tsvg_attachment_url))) {
				$data['id'] = attachment_url_to_postid($tsvg_attachment_url);
			}
			wp_send_json_success($data);
		} else {
			wp_send_json_error();
		}
	}
	public function tsvg_screen_option(){
		$option = 'per_page';
		$args   = array(
			'label'   => 'Galleries per page',
			'default' => 15,
			'option'  => 'tsvg_records_per_page'
		);
		add_screen_option($option, $args);
		$this->tsvg_admin_manager = new TS_Video_Gallery_List_Table();
	}
	public function tsvg_admin_menu(){
		$hook = add_menu_page(
			$this->plugin_name,
			esc_html('TS Video Gallery'),
			'manage_options',
			'tsvg-admin',
			array($this, 'tsvg_get_admin'),
			esc_url(plugin_dir_url(__FILE__) . 'img/ts-video-gallery-small-logo.png')
		);
		add_action("load-$hook", array($this, 'tsvg_screen_option'));
	}
	public function tsvg_admin_submenu(){
		$hooks = add_submenu_page(
			'tsvg-admin',
			esc_html('TS Video Gallery'),
			esc_html('All Galleries'),
			'manage_options',
			'tsvg-admin',
			array($this, 'tsvg_get_admin')
		);
		add_action("load-$hooks", array($this, 'tsvg_screen_option'));
	}
	public function tsvg_admin_builder_submenu(){
		add_submenu_page(
			'tsvg-admin',
			esc_html('TS Video Gallery Builder'),
			esc_html('Add Gallery'),
			'manage_options',
			'tsvg-builder',
			array($this, 'tsvg_get_builder')
		);
	}
	public function tsvg_admin_pro_submenu() {
		add_submenu_page(
			'tsvg-admin',
			esc_html__( 'TS Video Gallery Pro Features' ),
			esc_html__( 'Pro Features' ),
			'manage_options',
			'tsvg-pro',
			array( $this, 'tsvg_get_pro' )
		);
	}
	public function tsvg_admin_addons_submenu() {
		add_submenu_page(
			'tsvg-admin',
			esc_html__( 'TS Video Gallery Add-Ons' ),
			esc_html__( 'Add-Ons' ),
			'manage_options',
			'tsvg-add-ons',
			array( $this, 'tsvg_get_add_ons' )
		);
	}
	public function tsvg_get_admin(){
		include_once 'tsvg-admin.php';
	}
	public function tsvg_get_builder(){
		include_once 'tsvg-builder.php';
	}
	public function tsvg_get_pro() {
		include_once 'tsvg-pro.php';
	}
	public function tsvg_get_add_ons() {
		include_once 'tsvg-add-ons.php';
	}
}
