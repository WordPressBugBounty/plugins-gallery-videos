<?php
class TS_Video_Gallery_Admin extends TS_Video_Gallery_Function
{
	private $plugin_name;
	private $version;
	public $tsvg_admin_manager;
	public $tsvg_build;
	public $tsvg_build_proporties;
	public $tsvg_build_id;
	private $tsvg_page_slug;
	private $tsvg_themes;
	private $tsvg_themes_links;
	protected $tsvg_function_class;
	public function __construct($plugin_name, $version){
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		if (isset($_GET) && isset($_GET['page'])) {
			if (sanitize_text_field(wp_unslash($_GET['page'])) === 'tsvg-admin' || sanitize_text_field(wp_unslash($_GET['page'])) === 'tsvg-builder') {
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
	}
	public function tsvg_process_requests()
	{
		$this->tsvg_themes = array(
			'grid_video_gallery'     => 'Grid Video Gallery',
			'lightbox_video_gallery' => 'LightBox Video Gallery',
			'thumbnails_video'       => 'Thumbnails Video',
			'content_popup'          => 'Content Popup',
			'elastic_gallery'        => 'Elastic Gallery',
			'fancy_gallery'          => 'Fancy Gallery',
			'parallax_engine'        => 'Parallax Engine',
			'classic_gallery'        => 'Classic Gallery',
			'space_gallery'          => 'Space Gallery',
			'effective_gallery'      => 'Effective Gallery',
			'gallery_album'          => 'Gallery Album'
		);
		if (isset($_POST) && isset($_POST["tsvg_nonce"])) {
			if (sanitize_text_field(wp_unslash($_POST['tsvg_nonce'])) === '' || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tsvg_nonce'])), 'tsvg_builder_nonce_field')) {
				echo ('TS Video Gallery nonce error.');
				die();
			}
			if(isset($_POST["tsvg_id"]) && isset($_POST["tsvg_title"]))
			{
				$tsvg_id = sanitize_text_field(wp_unslash($_POST['tsvg_id']));
				$tsvg_title = sanitize_text_field(wp_unslash($_POST['tsvg_title']));
				if (is_numeric($tsvg_id) || array_key_exists($tsvg_id, $this->tsvg_themes)) {
					global $wpdb;
					$tsvg_db_manager_table = esc_sql($wpdb->prefix . 'ts_galleryv_manager');
					$tsvg_db_videos_table = esc_sql($wpdb->prefix . 'ts_galleryv_videos');
					$tsvg_videos = json_decode(stripslashes(sanitize_text_field(wp_unslash($_POST['tsvg_videos']))), true);
					$tsvg_videos_order = json_decode(stripslashes(sanitize_text_field(wp_unslash($_POST['tsvg_videos_order']))), true);
					$tsvg_styles = json_decode(stripslashes(sanitize_text_field(wp_unslash($_POST['tsvg_styles']))), true);
					$tsvg_options = json_decode(stripslashes(sanitize_text_field(wp_unslash($_POST['tsvg_options']))), true);
					$tsvg_settings = json_decode(stripslashes(sanitize_text_field(wp_unslash($_POST['tsvg_settings']))), true);
					$tsvg_option_styles = json_decode(stripslashes(sanitize_text_field(wp_unslash($_POST['tsvg_option_styles']))), true);
					$tsvg_deleted_videos = sanitize_text_field(wp_unslash(isset($_POST['tsvg_deleted_videos']))) ? json_decode(stripslashes(sanitize_text_field(wp_unslash($_POST['tsvg_deleted_videos']))), true) : "";
					$tsvg_order_array = array();
					foreach ($tsvg_styles as $key => $value) {
						$tsvg_styles[$key] = sanitize_text_field(htmlentities(stripslashes($value), ENT_QUOTES));
					}
					foreach ($tsvg_settings as $key => $value) {
						$tsvg_settings[$key] = sanitize_text_field(htmlentities(stripslashes($value), ENT_QUOTES));
					}
					foreach ($tsvg_option_styles as $key => $value) {
						$tsvg_option_styles[$key] = sanitize_text_field(htmlentities(stripslashes($value), ENT_QUOTES));
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
								'updated_at'         => gmdate( 'd.m.Y h:i:sa' ),
							),
							array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
						);
						$tsvg_insert_id = $wpdb->insert_id;
						foreach ($tsvg_videos_order as $key => $value) {
							$tsvg_arr_key = sanitize_text_field($value);
							$tsvg_video_title = htmlentities(sanitize_text_field(stripslashes($tsvg_videos[$tsvg_arr_key]['TS_VG_SetName'])), ENT_QUOTES);
							foreach ($tsvg_videos[$tsvg_arr_key]['TS_VG_Options'] as $tsvg_param_key => $tsvg_param_value) {
								if ($tsvg_param_key == 'TotalSoftVGallery_Vid_Vd' || $tsvg_param_key == 'TotalSoftVGallery_Vid_Im' || $tsvg_param_key == 'TotalSoftVGallery_Vid_link') {
									$tsvg_videos[$tsvg_arr_key]['TS_VG_Options'][$tsvg_param_key] = sanitize_url($tsvg_param_value);
								} elseif ($tsvg_param_key == 'TotalSoftVGallery_Vid_desc') {
									$tsvg_videos[$tsvg_arr_key]['TS_VG_Options'][$tsvg_param_key] = sanitize_text_field(htmlentities(stripslashes($tsvg_param_value)));
								} else {
									$tsvg_videos[$tsvg_arr_key]['TS_VG_Options'][$tsvg_param_key] = sanitize_text_field($tsvg_param_value);
								}
							}
							$wpdb->insert(
								$tsvg_db_videos_table,
								array(
									'id'            => '',
									'TS_VG_SetType' => (int) $tsvg_insert_id,
									'TS_VG_SetName' => $tsvg_video_title,
									'TS_VG_Options' => json_encode($tsvg_videos[$tsvg_arr_key]['TS_VG_Options']),
								),
								array('%d', '%d', '%s', '%s')
							);
							$tsvg_order_array[] = $wpdb->insert_id;
						}
						$wpdb->update($tsvg_db_manager_table, array('TS_VG_Sort' => implode(',', $tsvg_order_array)), array('id' => (int) $tsvg_insert_id), array('%s'), array('%d'));
						if (wp_safe_redirect(add_query_arg('tsvg-id', $tsvg_insert_id, admin_url('admin.php?page=tsvg-builder'))))
							exit();
					} else {
						foreach ($tsvg_videos_order as $key => $value) {
							$tsvg_arr_key       = sanitize_text_field($value);
							$tsvg_video_title = sanitize_text_field(htmlentities(stripslashes($tsvg_videos[$tsvg_arr_key]['TS_VG_SetName']), ENT_QUOTES));
							foreach ($tsvg_videos[$tsvg_arr_key]['TS_VG_Options'] as $tsvg_param_key => $tsvg_param_value) {
								if ($tsvg_param_key == 'TotalSoftVGallery_Vid_Im' || $tsvg_param_key == 'TotalSoftVGallery_Vid_Vd' || $tsvg_param_key == 'TotalSoftVGallery_Vid_link') {
									$tsvg_videos[$tsvg_arr_key]['TS_VG_Options'][$tsvg_param_key] = sanitize_url($tsvg_param_value);
								} elseif ($tsvg_param_key == 'TotalSoftVGallery_Vid_desc') {
									$tsvg_videos[$tsvg_arr_key]['TS_VG_Options'][$tsvg_param_key] = sanitize_text_field(htmlentities(stripslashes($tsvg_param_value)));
								} else {
									$tsvg_videos[$tsvg_arr_key]['TS_VG_Options'][$tsvg_param_key] = sanitize_text_field($tsvg_param_value);
								}
							}
							if (strpos($value, 'new') !== false) {
								$wpdb->insert(
									$tsvg_db_videos_table,
									array(
										'id'            => '',
										'TS_VG_SetType' => (int) $tsvg_id,
										'TS_VG_SetName' => $tsvg_video_title,
										'TS_VG_Options' => json_encode($tsvg_videos[$tsvg_arr_key]['TS_VG_Options']),
									),
									array('%d', '%d', '%s', '%s')
								);
								$tsvg_order_array[] = $wpdb->insert_id;
							} else {
								$wpdb->update(
									$tsvg_db_videos_table,
									array(
										'TS_VG_SetName' => $tsvg_video_title,
										'TS_VG_Options' => json_encode($tsvg_videos[$tsvg_arr_key]['TS_VG_Options']),
									),
									array('id' => (int) $tsvg_arr_key),
									array('%s', '%s'),
									array('%d')
								);
								$tsvg_order_array[] = (int) $tsvg_arr_key;
							}
						}
						if (is_array($tsvg_deleted_videos) && count($tsvg_deleted_videos) != 0) {
							foreach ($tsvg_deleted_videos as $key => $value) {
								if (strpos(sanitize_text_field($value), 'new') === false) {
									$wpdb->delete(
										$tsvg_db_videos_table,
										array('id' => (int) sanitize_text_field($value)),
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
						if (wp_safe_redirect(add_query_arg('tsvg-id', $tsvg_id, admin_url('admin.php?page=tsvg-builder'))))
							exit();
					}
				} else {
					echo 'TS Video Gallery - unexpected error.';
					die();
				}
			}else {
				echo 'TS Video Gallery - unexpected error.';
				die();
			}

		}
		if (isset($_GET['tsvg-id']) || isset($_GET['tsvg-theme'])) {
			$this->tsvg_function_class = new TS_Video_Gallery_Function();
			if (wp_unslash(isset($_GET['tsvg-id'])) && is_numeric(sanitize_text_field(wp_unslash($_GET['tsvg-id']))) && is_int((int) sanitize_text_field(wp_unslash($_GET['tsvg-id']))) && (int) sanitize_text_field(wp_unslash($_GET['tsvg-id'])) > 0) {
				global $wpdb;
				$tsvg_get_record = false;
				$tsvg_db_manager_table = esc_sql($wpdb->prefix . 'ts_galleryv_manager');
				$tsvg_db_videos_table = esc_sql($wpdb->prefix . 'ts_galleryv_videos');
				$tsvg_get_record = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tsvg_db_manager_table} WHERE id = %d ", sanitize_text_field(wp_unslash($_GET['tsvg-id']))), ARRAY_A);
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
					$tsvg_get_video_records = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$tsvg_db_videos_table} WHERE TS_VG_SetType = %d ", (int) sanitize_text_field(wp_unslash($_GET['tsvg-id']))) , ARRAY_A);
					foreach ($tsvg_get_video_records as $key => $value) {
						$tsvg_get_video_records[$key]['TS_VG_SetName'] = html_entity_decode(htmlspecialchars_decode($value['TS_VG_SetName']), ENT_QUOTES);
					}
					$tsvg_get_record['tsvg_video_records'] = $tsvg_get_video_records;
					$tsvg_get_record['TS_VG_Style'] = json_decode($tsvg_get_record['TS_VG_Style'], true);
					$this->tsvg_build = 'edit';
					$this->tsvg_build_proporties = $tsvg_get_record;
					$this->tsvg_build_id = sanitize_text_field(wp_unslash($_GET['tsvg-id']));
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
					'tsvg_video_records' => $tsvg_default_data['Videos'],
				);
			} else {
				$this->tsvg_build = '404';
			}
		} else {
			$this->tsvg_build = 'new';
			$this->tsvg_themes_links = array(
				'grid_video_gallery'     => 'wp-video-gallery-grid/',
				'lightbox_video_gallery' => 'wp-video-gallery-lightbox/',
				'thumbnails_video'       => 'wp-video-gallery-thumbnails/',
				'content_popup'          => 'wp-video-gallery-content-popup/',
				'elastic_gallery'        => 'wp-video-gallery-elastic/',
				'fancy_gallery'          => 'wp-video-gallery-fancy/',
				'parallax_engine'        => 'wp-video-gallery-parallax/',
				'classic_gallery'        => 'wp-video-gallery-classic/',
				'space_gallery'          => 'wp-video-gallery-space/',
				'effective_gallery'		 => 'wp-video-gallery-effective/',
				'gallery_album'			 => 'wp-video-gallery-album/'
			);
		}
	}
	public function tsvg_add_action_link($links)
	{
		$links['tsvgallery_support'] = sprintf('<a href="%1$s" style="color: #8bc34a;font-weight: bold;" target="_blank">Support</a>', esc_url('https://wordpress.org/support/plugin/gallery-videos/'));
		$links['tsvgallery_go_pro']  = sprintf('<a href="%1$s" style="color: #ff0000;font-weight: bold;" target="_blank">Go Pro</a>', esc_url('https://total-soft.com/wp-video-gallery/'));
		return $links;
	}
	public function enqueue_styles()
	{
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
	}
	public function enqueue_scripts()
	{
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
					$this->tsvg_build_proporties['tsvg_video_records'][$key]['TS_VG_Options']->TotalSoftVGallery_Vid_desc = wp_unslash(html_entity_decode($this->tsvg_build_proporties['tsvg_video_records'][$key]['TS_VG_Options']->TotalSoftVGallery_Vid_desc));
				endforeach;
				wp_localize_script(
					'tsvg-builder',
					'tsvg_builder_object',
					array(
						'ajaxurl'         => admin_url('admin-ajax.php'),
						'tsvg_nonce'      => wp_create_nonce('tsvg_builder_nonce_field'),
						'tsvg_proporties' => $this->tsvg_build_proporties,
						'tsvg_id'         => $this->tsvg_build_id,
						'tsvg_creation'   => wp_unslash(isset($_GET['tsvg-theme'])) ? 'save' : 'update',
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
	}
	public static function tsvg_set_screen($status, $option, $value)
	{
		return $value;
	}
	function tsvg_get_attachment_callback()
	{
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
	function tsvg_get_attachment_id_callback()
	{
		if (! wp_unslash(isset($_POST['tsvg_nonce'])) || sanitize_text_field(wp_unslash($_POST['tsvg_nonce'])) === '' || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tsvg_nonce'])), 'tsvg_builder_nonce_field')) {
			wp_send_json_error();
		}
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
	public function tsvg_screen_option()
	{
		$option = 'per_page';
		$args   = array(
			'label'   => 'Galleries per page',
			'default' => 15,
			'option'  => 'tsvg_records_per_page'
		);
		add_screen_option($option, $args);
		$this->tsvg_admin_manager = new TS_Video_Gallery_List_Table();
	}
	public function tsvg_admin_menu()
	{
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
	public function tsvg_admin_submenu()
	{
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
	public function tsvg_admin_builder_submenu()
	{
		add_submenu_page(
			'tsvg-admin',
			esc_html('TS Video Gallery Builder'),
			esc_html('Add Gallery'),
			'manage_options',
			'tsvg-builder',
			array($this, 'tsvg_get_builder')
		);
	}
	public function tsvg_get_admin()
	{
		include_once 'tsvg-admin.php';
	}
	public function tsvg_get_builder()
	{
		include_once 'tsvg-builder.php';
	}
}
