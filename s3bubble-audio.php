<?php
/*
Plugin Name: S3Bubble Amazon S3 Video And Audio Streaming With Analytics
Plugin URI: https://s3bubble.com/
Description: S3Bubble offers secure, Media Streaming from Amazon S3 to WordPress. 
Version: 2.3
Author: S3Bubble
Author URI: https://s3bubble.com
License: GPL2
*/ 
 
/*  Copyright YEAR  Samuel East  (email : mail@samueleast.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/ 


if (!class_exists("s3bubble_audio")) {
	class s3bubble_audio {

		/*
		 * Class properties
		 * @author sameast
		 * @params noen
		 */ 
        public  $s3audible_username = '';
		public  $s3audible_email = '';
		public  $bucket          = '';
		public  $folder          = '';
		public  $colour          = '#1abc98';
		public  $width           = '100%';
		public  $autoplay        = 'yes';
		public  $jtoggle		 = 'true';
		public  $loggedin        = 'false';
		public  $s3bubble_force_download = 'false';
		public  $search          = 'false';
		public  $responsive      = 'responsive';
		public  $theme           = 's3bubble_clean';
		public  $stream          = 'm4v';
		public  $version         =  46;
		public  $s3bubble_video_all_bar_colours = '#adadad';
		public  $s3bubble_video_all_bar_seeks   = '#53bbb4';
		public  $s3bubble_video_all_controls_bg = '#384049';
		public  $s3bubble_video_all_icons       = '#FFFFFF';
		private $endpoint       = 'https://s3api.com/v1/';
		
		/*
		 * Constructor method to intiat the class
		 * @author sameast
		 * @params none
		 */ 
		function s3bubble_audio(){
			
			/*
			 * Add default option to database
			 * @author sameast
			 * @params none
			 */ 
			add_option("s3-s3audible_username", $this->s3audible_username);
			add_option("s3-s3audible_email", $this->s3audible_email);
			add_option("s3-bucket", $this->bucket);
			add_option("s3-folder", $this->folder); 
			add_option("s3-colour", $this->colour);
			add_option("s3-width", $this->width);
			add_option("s3-autoplay", $this->autoplay);
			add_option("s3-jtoggle", $this->jtoggle);
			add_option("s3-loggedin", $this->loggedin);
			add_option("s3bubble_force_download", $this->s3bubble_force_download);
			add_option("s3-search", $this->search);
			add_option("s3-responsive", $this->responsive);
			add_option("s3-theme", $this->theme);
			add_option("s3-stream", $this->stream);
			add_option("s3bubble_video_all_bar_colours", $this->s3bubble_video_all_bar_colours);
			add_option("s3bubble_video_all_bar_seeks", $this->s3bubble_video_all_bar_seeks);
			add_option("s3bubble_video_all_controls_bg", $this->s3bubble_video_all_controls_bg);
			add_option("s3bubble_video_all_icons", $this->s3bubble_video_all_icons);

			/*
			 * Run the add admin menu class
			 * @author sameast
			 * @params none
			 */ 
			add_action( 'admin_menu', array( $this, 's3bubble_audio_admin_menu' ));

			/*
			 * Add some extras to run after theme support add image sizes etc...
			 * @author sameast
			 * @params none
			 */ 
			add_action( 'after_setup_theme', array( $this, 's3bubble_wordpress_theme_setup' ) );
			
			/*
			 * Add css to the header of the document
			 * @author sameast
			 * @params none
			 */ 
			add_action( 'wp_enqueue_scripts', array( $this, 's3bubble_audio_css' ), 12 );
			add_action( 'wp_enqueue_scripts', array( $this, 's3bubble_audio_javascript' ), 12 );
			
			/*
			 * Add javascript to the frontend footer connects to wp_footer
			 * @author sameast
			 * @params none
			 */ 
			add_action( 'admin_enqueue_scripts', array( $this, 's3bubble_audio_admin_scripts' ) );
			
			/*
			 * Setup shortcodes for the plugin
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3bubbleAudio', array( $this, 's3bubble_audio_player' ) );
			add_shortcode( 's3bubbleAudioSingle', array( $this, 's3bubble_audio_single_player' ) );
			add_shortcode( 's3bubbleVideo', array( $this, 's3bubble_video_player' ) );
			add_shortcode( 's3bubbleVideoSingle', array( $this, 's3bubble_video_single_player' ) );

			/*
			 * Video JS for the plugin
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3bubbleVideoSingleJs', array( $this, 's3bubble_video_single_player_videojs' ) );
			
			/*
			 * Media Element shortcodes for the plugin
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3bubbleMediaElementVideo', array( $this, 's3bubble_media_element_video' ) );
			add_shortcode( 's3bubbleMediaElementAudio', array( $this, 's3bubble_media_element_audio' ) );

			/*
			 * HLS shortcodes for the plugin
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3bubbleHlsVideo', array( $this, 's3bubble_hls_video' ) );
			add_shortcode( 's3bubbleHlsAudio', array( $this, 's3bubble_hls_audio' ) );
			add_shortcode( 's3bubbleHlsVideoJs', array( $this, 's3bubble_hls_video_js' ) );

			/*
			 * Popup shortcodes for the plugin
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3bubbleLightboxVideoSingle', array( $this, 's3bubble_lightbox_video' ) );

			/*
			 * Live Stream Video JS shortcodes for the plugin
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3bubbleLiveStream', array( $this, 's3bubble_live_stream_video' ) );
			add_shortcode( 's3bubbleLiveAudio', array( $this, 's3bubble_live_stream_audio' ) );
			add_shortcode( 's3bubbleLiveStreamMedia', array( $this, 's3bubble_live_stream_media_element_video' ) );


			/*
			 * Rtmp shortcodes for the plugin
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3bubbleRtmpVideo', array( $this, 's3bubble_rtmp_video' ) );
			add_shortcode( 's3bubbleRtmpAudio', array( $this, 's3bubble_rtmp_audio' ) );
			add_shortcode( 's3bubbleRtmpVideoJs', array( $this, 's3bubble_rtmp_video_js' ) );
			add_shortcode( 's3bubbleRtmpAudioJs', array( $this, 's3bubble_rtmp_audio_js' ) );
			add_shortcode( 's3bubbleRtmpVideoDefault', array( $this, 's3bubble_rtmp_video_default' ) );
			add_shortcode( 's3bubbleRtmpAudioDefault', array( $this, 's3bubble_rtmp_audio_default' ) );

			
			/*
			 * Legacy shortcodes for the plugin
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3audible', array( $this, 's3bubble_audio_player' ) );
			add_shortcode( 's3audibleSingle', array( $this, 's3bubble_audio_single_player' ) );	
			add_shortcode( 's3video', array( $this, 's3bubble_video_player' ) );	
			add_shortcode( 's3videoSingle', array( $this, 's3bubble_video_single_player' ) );	

			/*
			 * Iframe codes for the plugin
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3bubbleVideoSingleIframe', array( $this, 's3bubble_video_single_player_iframe' ) );
			add_shortcode( 's3bubbleAudioSingleIframe', array( $this, 's3bubble_audio_single_player_iframe' ) );
			
			/*
			 * Outputs the s3bubble analytics
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3bubbleAnalytics', array( $this, 's3bubble_output_analytics' ) );

			
			/*
			 * Tiny mce button for the plugin
			 * @author sameast
			 * @params none
			 */
			add_action( 'init', array( $this, 's3bubble_buttons' ) );
			add_action( 'wp_ajax_s3bubble_audio_playlist_ajax', array( $this, 's3bubble_audio_playlist_ajax' ) );
			add_action( 'wp_ajax_s3bubble_video_playlist_ajax', array( $this, 's3bubble_video_playlist_ajax' ) );
			add_action( 'wp_ajax_s3bubble_audio_single_ajax', array( $this, 's3bubble_audio_single_ajax' ) );
			add_action( 'wp_ajax_s3bubble_video_single_ajax', array( $this, 's3bubble_video_single_ajax' ) ); 
			add_action( 'wp_ajax_s3bubble_live_stream_ajax', array( $this, 's3bubble_live_stream_ajax' ) );
		
            /*
			 * Internal Ajax
			 */
			add_action( 'wp_ajax_s3bubble_video_single_internal_ajax', array( $this, 's3bubble_video_single_internal_ajax' ) );
			add_action( 'wp_ajax_nopriv_s3bubble_video_single_internal_ajax', array( $this, 's3bubble_video_single_internal_ajax' ) ); 
		    
		    add_action( 'wp_ajax_s3bubble_video_playlist_internal_ajax', array( $this, 's3bubble_video_playlist_internal_ajax' ) );
			add_action( 'wp_ajax_nopriv_s3bubble_video_playlist_internal_ajax', array( $this, 's3bubble_video_playlist_internal_ajax' ) ); 
			
			add_action( 'wp_ajax_s3bubble_audio_single_internal_ajax', array( $this, 's3bubble_audio_single_internal_ajax' ) );
			add_action( 'wp_ajax_nopriv_s3bubble_audio_single_internal_ajax', array( $this, 's3bubble_audio_single_internal_ajax' ) ); 
            
			add_action( 'wp_ajax_s3bubble_audio_playlist_internal_ajax', array( $this, 's3bubble_audio_playlist_internal_ajax' ) );
			add_action( 'wp_ajax_nopriv_s3bubble_audio_playlist_internal_ajax', array( $this, 's3bubble_audio_playlist_internal_ajax' ) ); 

			add_action( 'wp_ajax_s3bubble_video_rtmp_internal_ajax', array( $this, 's3bubble_video_rtmp_internal_ajax' ) );
			add_action( 'wp_ajax_nopriv_s3bubble_video_rtmp_internal_ajax', array( $this, 's3bubble_video_rtmp_internal_ajax' ) ); 
			
			add_action( 'wp_ajax_s3bubble_audio_rtmp_internal_ajax', array( $this, 's3bubble_audio_rtmp_internal_ajax' ) );
			add_action( 'wp_ajax_nopriv_s3bubble_audio_rtmp_internal_ajax', array( $this, 's3bubble_audio_rtmp_internal_ajax' ) ); 

			/*
			 * Internal Ajax
			 */
			add_action( 'wp_ajax_s3bubble_analytics_internal_ajax', array( $this, 's3bubble_analytics_internal_ajax' ) );
			add_action( 'wp_ajax_s3bubble_wpremotepost_internal_ajax', array( $this, 's3bubble_wpremotepost_internal_ajax' ) );
			add_action( 'wp_ajax_s3bubble_traceroute_internal_ajax', array( $this, 's3bubble_traceroute_internal_ajax' ) );

			/*
			 * Admin dismiss message
			 */
			add_action('admin_notices', array( $this, 's3bubble_admin_notice' ) );
			add_action('admin_init', array( $this, 's3bubble_nag_ignore' ) );

			/*
			 * Heartbeat fix
			 */
			add_action( 'init', array( $this, 's3bubble_stop_heartbeat' ), 1 );
		}

		/*
		* Fix for poor hosts
		* @author sameast
		* @none
		*/ 
		function s3bubble_stop_heartbeat() {
		  	global $pagenow;
		  	if ( $pagenow != 'edit.php' )
		  	wp_deregister_script('heartbeat');
		}

		/*
		* Run after theme support image sizes etc...
		* @author sameast
		* @none
		*/ 
		function s3bubble_wordpress_theme_setup() {
		  	/* Configure WP 2.9+ Thumbnails ---------------------------------------------*/
    		add_theme_support('post-thumbnails');
        	add_image_size( 'single-video-poster', 960, 540, true ); // (cropped)
		}

		/*
		* Sets up a admin alert notice
		* @author sameast
		* @none
		*/ 
		function s3bubble_admin_notice() {
			global $current_user ;
		    $user_id = $current_user->ID;
		    $params = array_merge($_GET, array("s3bubble_nag_ignore" => 0));
			$new_query_string = http_build_query($params); 
		    /* Check that the user hasn't already clicked to ignore the message */
			if ( ! get_user_meta($user_id, 's3bubble_nag_ignore') ) {
		        echo '<div class="updated"><p>'; 
		        echo 'Thankyou for upgrading your S3Bubble media streaming plugin. Any issues please contact us at <a href="mailto:support@s3bubble.com">support@s3bubble.com</a> if you are stuck you can always roll back within the S3Bubble WP admin download and re-install the old plugin. Want to see the great new features please watch this video <a href="https://s3bubble.com/video_tutorials/s3bubble-plugin-upgrade/" target="_blank">Watch Video</a>. | <a href="' . $_SERVER['PHP_SELF'] . "?" . $new_query_string . '" class="pull-right">Hide Notice</a>';
		        echo "</p></div>";
			}
		}

		/*
		* Allows users to ignore the message
		* @author sameast
		* @none
		*/ 
		function s3bubble_nag_ignore() {
			global $current_user;
		        $user_id = $current_user->ID;
		        /* If user clicks to ignore the notice, add that to their user meta */
		        if ( isset($_GET['s3bubble_nag_ignore']) && '0' == $_GET['s3bubble_nag_ignore'] ) {
		             add_user_meta($user_id, 's3bubble_nag_ignore', 'true', true);
			}
		}

		/*
		* Adds the menu item to the wordpress admin
		* @author sameast
		* @none
		*/ 
        function s3bubble_audio_admin_menu(){	

			add_menu_page( 's3bubble_audio', 'S3Bubble Media', 'manage_options', 's3bubble_audio', array($this, 's3bubble_audio_admin'), plugins_url('assets/images/s3bubblelogo.png',__FILE__ ) );
			add_submenu_page( 's3bubble_audio', 'Analytics', 'Analytics', 'manage_options', 's3bubble-analytics-page', array($this, 's3bubble_analytics_page_callback') );
			add_submenu_page( 's3bubble_audio', 'Desktop App', 'Desktop App', 'manage_options', 's3bubble-desktop-app-page', array($this, 's3bubble_desktop_app_page_callback') );
			add_submenu_page( 's3bubble_audio', 'Live Streaming', 'Live Streaming', 'manage_options', 's3bubble-live-streaming-page', array($this, 's3bubble_live_streaming_page_callback') );
			//add_submenu_page( 's3bubble_audio', 'Debug', 'Debug', 'manage_options', 's3bubble-debug-page', array($this, 's3bubble_debug_page_callback') );

    	}

    	/*
		* Add javascript to the footer connect to wp_footer()
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_admin(){	

			if ( isset($_POST['submit']) ) {
				$nonce = $_REQUEST['_wpnonce'];
				if (! wp_verify_nonce($nonce, 's3bubble-media') ) die('Security check failed'); 
				if (!current_user_can('manage_options')) die(__('You cannot edit the s3bubble media options.'));
				check_admin_referer('s3bubble-media');	
				// Get our new option values
				$s3audible_username	     = $this->s3bubble_clean_options($_POST['s3audible_username']);
				$s3audible_email	     = $this->s3bubble_clean_options($_POST['s3audible_email']);
				$loggedin			     = $this->s3bubble_clean_options($_POST['loggedin']);
				$s3bubble_force_download = $this->s3bubble_clean_options($_POST['s3bubble_force_download']);

				// new
				$s3bubble_video_all_bar_colours	= $this->s3bubble_clean_options($_POST['s3bubble_video_all_bar_colours']);
				$s3bubble_video_all_bar_seeks	= $this->s3bubble_clean_options($_POST['s3bubble_video_all_bar_seeks']);
				$s3bubble_video_all_controls_bg	= $this->s3bubble_clean_options($_POST['s3bubble_video_all_controls_bg']);
				$s3bubble_video_all_icons	    = $this->s3bubble_clean_options($_POST['s3bubble_video_all_icons']);

			    // Update the DB with the new option values
				update_option("s3-s3audible_username", $s3audible_username);
				update_option("s3-s3audible_email", $s3audible_email);
				update_option("s3-loggedin", $loggedin);
				update_option("s3bubble_force_download", $s3bubble_force_download);
				
				// new
				update_option("s3bubble_video_all_bar_colours", $s3bubble_video_all_bar_colours);
				update_option("s3bubble_video_all_bar_seeks", $s3bubble_video_all_bar_seeks);
				update_option("s3bubble_video_all_controls_bg", $s3bubble_video_all_controls_bg);
				update_option("s3bubble_video_all_icons", $s3bubble_video_all_icons);

				//set POST variables
				$alert = '';
				$url = $this->endpoint . 'main_plugin/auth';
				$response = wp_remote_post( $url, array(
					'method' => 'POST',
					'sslverify' => false,
					'timeout' => 10,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => array(),
					'body' => array(
						'AccessKey' => $s3audible_username
					),
					'cookies' => array()
				    )
				);

				if ( is_wp_error( $response ) ) {

				   $error_message = $response->get_error_message();
				   $alert = '<div class="error"><p>' . $error_message . '</p></div>';

				} else {

					$data = json_decode($response['body']);
					if($data->error){
						$alert = '<div class="error"><p>' . $data->message . '</p></div>';
					}else{
						$alert = '<div class="updated"><p>' . $data->message . '</p></div>';
					}
				}

			}
			
			$s3audible_username	     = get_option("s3-s3audible_username");
			$s3audible_email	     = get_option("s3-s3audible_email");
			$loggedin			     = get_option("s3-loggedin");
			$s3bubble_force_download = get_option("s3bubble_force_download");			

			// new
			$s3bubble_video_all_bar_colours	= get_option("s3bubble_video_all_bar_colours");
			$s3bubble_video_all_bar_seeks	= get_option("s3bubble_video_all_bar_seeks");
			$s3bubble_video_all_controls_bg	= get_option("s3bubble_video_all_controls_bg");
			$s3bubble_video_all_icons	    = get_option("s3bubble_video_all_icons");

		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2>S3Bubble Amazon S3 Media Cloud Media Streaming</h2>
			<div id="message" class="updated fade"><p>Please sign up for a S3Bubble account at <a href="https://s3bubble.com" target="_blank">https://s3bubble.com</a></p></div>
			<?php echo $alert; ?>
			<div class="metabox-holder has-right-sidebar">
				<div class="inner-sidebar" style="width:40%">
					<div class="postbox">
						<h3 class="hndle">PLEASE USE WYSIWYG EDITOR BUTTONS</h3>
						<div class="inside">
							<img style="width: 100%;" src="<?php echo plugins_url('/assets/images/wp_editor.png',__FILE__); ?>" />
						</div> 
					</div>
					<div class="postbox">
						<h3 class="hndle">S3Bubble Support</h3>
						<div class="inside">
							<ul class="s3bubble-adverts">
								<li>
									<img src="<?php echo plugins_url('/assets/images/support.png',__FILE__); ?>" alt="S3Bubble iPhone" /> 
									<h3>
										Are you stuck and need help please read through our S3Bubble documentation.
									</h3>
									<a class="button button-s3bubble" href="https://s3bubble.com/documentation/" target="_blank">GO TO S3BUBBLE DOCUMENTATION</a>
								</li>
							</ul>        
						</div> 
					</div>
					<div class="postbox">
						<h3 class="hndle">S3Bubble Apps - Monitor Analytics</h3>
						<div class="inside">
							<ul class="s3bubble-adverts">
								<li>
									<img src="<?php echo plugins_url('/assets/images/plugin-mobile-icon.png',__FILE__); ?>" alt="S3Bubble Android" /> 
									<h3>
										Desktop App
									</h3>
									<p>Record Manage Watch Download Share. Manage all your video and audio analytics.</p>
									<a class="button button-s3bubble" href="https://s3bubble.com/s3bubble-desktop-app-beta/" target="_blank">DOWNLOAD</a>
								</li>
								<li>
									<img src="<?php echo plugins_url('/assets/images/plugin-mobile-icon.png',__FILE__); ?>" alt="S3Bubble iPhone" /> 
									<h3>
										iPhone Mobile App
									</h3>
									<p>Upload large file directly from your desktop. Manage all your video and audio.</p>
									<a class="button button-s3bubble" href="https://itunes.apple.com/us/app/s3bubble/id720256052?ls=1&mt=8" target="_blank">GET THE APP</a>
								</li>
							</ul>        
						</div> 
					</div>
				</div>
				<div id="post-body">
					<div id="post-body-content" style="margin-right: 41%;">
						<div class="postbox">
							<h3 class="hndle">Fill in details below if stuck please <a class="button button-s3bubble" style="float: right;margin: -5px -10px;" href="https://s3bubble.com/documentation/" target="_blank">Documentation</a></h3>
							<div class="inside">
								<form action="" method="post" class="s3bubble-video-popup-form" autocomplete="off">
								    <table class="form-table">
								      <?php if (function_exists('wp_nonce_field')) { wp_nonce_field('s3bubble-media'); } ?>
								       <tr style="position: relative;">
								        <th scope="row" valign="top"><label for="S3Bubble_username">App Access Key:</label></th>
								        <td><input type="text" name="s3audible_username" id="s3audible_username" class="regular-text" value="<?php echo empty($s3audible_username) ? 'Enter App Key' : $s3audible_username; ?>"/>
								        	<br />
								       <span class="description">App Access Key can be found at S3Bubble.com <a href="https://s3bubble.com/video_tutorials/s3bubble-lets-get-you-up-and-running-tutorial/" target="_blank">Watch Video</a></span>	
								        </td>
								      </tr> 
								       <tr>
								        <th scope="row" valign="top"><label for="s3audible_email">App Secret Key:</label></th>
								        <td><input type="password" name="s3audible_email" id="s3audible_email" class="regular-text" value="<?php echo empty($s3audible_email) ? 'Enter App Secret Key' : $s3audible_email; ?>"/>
								        	<br />
								        	<span class="description">App Secret Key can be found at S3Bubble.com <a href="https://s3bubble.com/video_tutorials/s3bubble-lets-get-you-up-and-running-tutorial/" target="_blank">Watch Video</a></span>
								        </td>
								      </tr> 
								       <tr>
								        <th scope="row" valign="top"><label for="loggedin">Download option logged in:</label></th>
								        <td><select name="loggedin" id="loggedin">
								            <option value="<?php echo $loggedin; ?>"><?php echo $loggedin; ?></option>
								            <option value="true">true</option>
								            <option value="false">false</option>
								          </select>
								          <br />
								          <span class="description">Only allow download link for logged in users.</p></td>
								      </tr>
								      <tr>
								        <th scope="row" valign="top"><label for="s3bubble_force_download">Force download option for all players:</label></th>
								        <td><select name="s3bubble_force_download" id="s3bubble_force_download">
								            <option value="<?php echo $s3bubble_force_download; ?>"><?php echo $s3bubble_force_download; ?></option>
								            <option value="true">true</option>
								            <option value="false">false</option>
								          </select>
								          <br />
								          <span class="description">!important this will force the download to show on (All) players.</p></td>
								      </tr>
								      <!-- new -->
								      <tr>
								        <th scope="row" valign="top"><label for="s3bubble_video_all_bar_colours">Player Bar Colours:</label></th>
								        <td> <input type="text" name="s3bubble_video_all_bar_colours" id="s3bubble_video_all_bar_colours" value="<?php echo $s3bubble_video_all_bar_colours; ?>" class="cpa-color-picker" >
								        	<br />
								        	<span class="description">Change the progress bar and volume bar colour</span>
								        </td>
								      </tr>
								      <tr>
								        <th scope="row" valign="top"><label for="s3bubble_video_all_bar_seeks">Seek Bar Colours:</label></th>
								        <td> <input type="text" name="s3bubble_video_all_bar_seeks" id="s3bubble_video_all_bar_seeks" value="<?php echo $s3bubble_video_all_bar_seeks; ?>" class="cpa-color-picker" >
								        	<br />
								        	<span class="description">Change the progress bar and volume bar seek bar colours</span>
								        </td>
								      </tr>
								      <tr>
								        <th scope="row" valign="top"><label for="s3bubble_video_all_controls_bg">Player Controls Colour:</label></th>
								        <td> <input type="text" name="s3bubble_video_all_controls_bg" id="s3bubble_video_all_controls_bg" value="<?php echo $s3bubble_video_all_controls_bg; ?>" class="cpa-color-picker" >
								        	<br />
								        	<span class="description">Change the controls background colour</span>
								        </td>
								      </tr> 
								      <tr>
								        <th scope="row" valign="top"><label for="s3bubble_video_all_icons">Player Icon Colours:</label></th>
								        <td> <input type="text" name="s3bubble_video_all_icons" id="s3bubble_video_all_icons" value="<?php echo $s3bubble_video_all_icons; ?>" class="cpa-color-picker" >
								        	<br />
								        	<span class="description">Change the player icons colours</span>
								        </td>
								      </tr>  
								      <!-- end new -->
								    </table>
								    <br/>
								    <span class="submit" style="border: 0;">
								    	<input type="submit" name="submit" class="button button-s3bubble button-hero" value="Save Settings" />
								    </span>
								  </form>
							</div><!-- .inside -->
						</div>
					</div> <!-- #post-body-content -->
				</div> <!-- #post-body -->
			</div> <!-- .metabox-holder -->
		</div> <!-- .wrap -->
		<?php	
       }

        /*
		* S3Bubble desktop page run route
		* @author sameast
		* @none
		*/ 
    	function s3bubble_desktop_app_page_callback() {
    		//Run a S3Bubble security check
			$ajax_nonce = wp_create_nonce( "s3bubble-nonce-security" );
    		?>
			<div class="wrap"><div id="icon-tools" class="icon32"></div>
				<h2>Did you know we have a desktop app?</h2>
				<div class="s3bubble-video-main-form-alerts"><p>The S3Bubble desktop app is in development and we are looking for testers, we are hoping to keep developing this and make it as awesome as we can.</p></div>
				<div class="metabox-holder has-right-sidebar">
				<div class="inner-sidebar" style="width:40%">
					<div class="postbox">
						<h3 class="hndle">Important heads up</h3>
						<div class="inside">
							Heads up! If the dmg does not open please make sure you have the latest version of java jdk Mac OS X x64 link here. <a href="http://www.oracle.com/technetwork/java/javase/downloads/jdk8-downloads-2133151.html" target="_blank">http://www.oracle.com/technetwork/java/javase/downloads/jdk8-downloads-2133151.html</a>
						</div> 
					</div>
					<div class="postbox">
						<h3 class="hndle">Desktop app downloads</h3>
						<div class="inside">
							<ul>
								<li>
									<a class="button button-s3bubble button-hero" href="https://s3.amazonaws.com/s3bubble.business.template/s3bubble-sharing-functionality.dmg.zip?Signature=oMHH0q1A4I3%2FbgsVODqTjV3eC5w%3D&AWSAccessKeyId=AKIAIDVCDVR32H7DCSNA&Expires=1696263845" target="_blank">DOWNLOAD IOS APP</a>
									<a class="button button-s3bubble button-hero" href="https://s3.amazonaws.com/s3bubble.assets/desktop/S3Bubble-Setup.zip" target="_blank">DOWNLOAD WINDOWS APP</a>
									<p>If you have any suggestions we would love to here them please drop us an email at <a href="mailto:support@s3bubble.com">support@s3bubble.com</a></p>
								</li>
							</ul>        
						</div> 
					</div>
				</div>
				<div id="post-body">
					<div id="post-body-content" style="margin-right: 41%;">
						<div class="postbox">
							<h3 class="hndle">Watch this quick overview video</h3>
							<div class="inside">
								<iframe style="width:100%;min-height:300px;" onload="this.height=(this.offsetWidth/16)*9;" src="//media.s3bubble.com/video/ECPfysyCB" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" allowtransparency="true" webkitAllowFullScreen="true" mozallowfullscreen="true" allowFullScreen="true" ></iframe>
							</div><!-- .inside -->
						</div>
					</div> <!-- #post-body-content -->
				</div> <!-- #post-body -->
			</div> <!-- .metabox-holder -->
		</div> <!-- .wrap -->
			<?php

		}

		/*
		* S3Bubble generates a randow string for stream
		* @author sameast
		* @none
		*/ 
		function s3BubbleGenerateRandomString($length = 3) {
		    $characters = 'abcdefghijklmnopqrstuvwxyz';
		    $charactersLength = strlen($characters);
		    $randomString = '';
		    for ($i = 0; $i < $length; $i++) {
		        $randomString .= $characters[rand(0, $charactersLength - 1)];
		    }
		    return $randomString;
		}

    	/*
		* S3Bubble desktop page run route
		* @author sameast
		* @none
		*/ 
    	function s3bubble_live_streaming_page_callback() {
    		//Run a S3Bubble security check
			$ajax_nonce = wp_create_nonce( "s3bubble-nonce-security" );
			$stream = $this->s3BubbleGenerateRandomString();
    		?>
			<div class="wrap"><div id="icon-tools" class="icon32"></div>
				<h2>Live Streaming coming soon, interested?</h2>

				
				<div class="s3bubble-video-main-form-alerts"><p>If you are insterested in being one of our live streaming beta testers or want your own personal live stream url please contact us. <a href="mailto:support@s3bubble.com">support@s3bubble.com</a></p></div>
				<div class="metabox-holder has-right-sidebar">
				<div class="inner-sidebar" style="width:40%">
					<div class="postbox">
						<h3 class="hndle">It really simple urls below.</h3>
						<div class="inside">
							<p>Rtmp Url: <strong>rtmp://54.152.190.21/live</strong></p>
							<p>Mobile Url: <strong>http://54.152.190.21/hls/<?php echo $stream; ?>.m3u8</strong> (Email Someone your stream)</p>
							<p>S3Bubble Player Url: <strong>rtmp://54.152.190.21/live/<?php echo $stream; ?></strong> (Paste into the S3Bubble player)</p>
						</div> 
					</div>
					<div class="postbox">
						<h3 class="hndle">Broadcasting app downloads</h3>
						<div class="inside">
							<ul>
								<li>
									<a class="button button-s3bubble button-hero" href="https://itunes.apple.com/us/app/os-broadcaster/id632458541?mt=8" target="_blank">DOWNLOAD IOS APP</a>
									<a class="button button-s3bubble button-hero" href="https://play.google.com/store/apps/details?id=air.OS.Broadcaster&hl=en" target="_blank">DOWNLOAD ANDRIOD APP</a>
									<p>If you are insterested in a Live Streaming setup please drop us an email at <a href="mailto:support@s3bubble.com">support@s3bubble.com</a></p>
								</li>
							</ul>        
						</div> 
					</div>
					<div class="postbox">
						<h3 class="hndle">Free Flash Media Live Encoder download</h3>
						<div class="inside">
							<a href="<?php echo plugins_url('/assets/images/flash.png',__FILE__); ?>" target="_blank"><img style="width:100%" src="<?php echo plugins_url('/assets/images/flash.png',__FILE__); ?>"></a>
							<ul>
								<li>
									<a class="button button-s3bubble button-hero" href="http://offers.adobe.com/en/na/leap/offers/fmle3.html?faas_unique_submission_id=%7bA9666D59-341F-8683-8F08-E49687CFD3B0%7d&s_cid=null" target="_blank">DOWNLOAD</a>
									<p>If you are insterested in a Live Streaming setup please drop us an email at <a href="mailto:support@s3bubble.com">support@s3bubble.com</a></p>
								</li>
							</ul>        
						</div> 
					</div>
				</div>
				<div id="post-body">
					<div id="post-body-content" style="margin-right: 41%;">
						<div class="postbox">
							<h3 class="hndle">Setting up a live webcam/streaming service - watch video below</h3>
							<div class="inside">
								<iframe width="100%" height="315" onload="this.height=(this.offsetWidth/16)*9;" src="https://www.youtube.com/embed/L7TWlGkJUUM" frameborder="0" allowfullscreen></iframe>
							</div><!-- .inside -->
						</div>
					</div> <!-- #post-body-content -->
				</div> <!-- #post-body -->
			</div> <!-- .metabox-holder -->
		</div> <!-- .wrap -->
			<?php

		}

    	/*
		* S3Bubble debug page run route
		* @author sameast
		* @none
		*/ 
    	function s3bubble_analytics_page_callback() {
    		//Run a S3Bubble security check
			$ajax_nonce = wp_create_nonce( "s3bubble-nonce-security" );
    		?>
			<div class="wrap"><div id="icon-tools" class="icon32"></div>
				<h2>S3Bubble video analytics - <span class="s3bubble-analytics-loading"><small>loading...</small></span></h2>
				<div class="s3bubble-video-main-form-alerts"><p>All user Analytics can be monitored directly through the S3Bubble app with push notifications. <a href="https://itunes.apple.com/gb/app/s3bubble-sharing-storing-streaming/id720256052?mt=8" target="_blank">Download App</a></p></div>
				<table id="s3AnalyticsTable" class="widefat tablesorter"> 
					<thead>
						<tr>
							<th scope="col">Source</th>
							<th>Key</th>
							<th>Country</th>
							<th>City/Location</th>
							<th>Ip Address</th>
							<th>Browser</th>
							<th>Type</th>
							<th>Watched</th>
							<th>Stats</th>
							<th>Created</th>
							<th>Map</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th scope="col">Source</th>
							<th>Key</th>
							<th>Country</th>
							<th>City/Location</th>
							<th>Ip Address</th>
							<th>Browser</th>
							<th>Type</th>
							<th>Watched</th>
							<th>Stats</th>
							<th>Created</th>
							<th>Map</th>
						</tr>
					</tfoot>
				</table>
			</div>
			<script type="text/javascript">
				function timeNow(UNIX_timestamp){
					 var a = new Date(UNIX_timestamp * 1000);
					  var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
					  var year = a.getFullYear();
					  var month = months[a.getMonth()];
					  var date = a.getDate();
					  var hour = a.getHours();
					  var min = a.getMinutes();
					  var sec = a.getSeconds();
					  var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min;
					  return time;
				}
				function baseName(str){
				    var base = new String(str).substring(str.lastIndexOf('/') + 1); 
				    if(base.lastIndexOf(".") != -1)       
				        base = base.substring(0, base.lastIndexOf("."));
				    return decodeURIComponent(base.replace(/\+/gi, " "));
				}
				function isNumeric(n) {
				  	return !isNaN(parseFloat(n)) && isFinite(n);
				}
				String.prototype.toHHMMSS = function () {
				    var sec_num = parseInt(this, 10); // don't forget the second param
				    var hours   = Math.floor(sec_num / 3600);
				    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
				    var seconds = sec_num - (hours * 3600) - (minutes * 60);

				    if (hours   < 10) {hours   = "0"+hours;}
				    if (minutes < 10) {minutes = "0"+minutes;}
				    if (seconds < 10) {seconds = "0"+seconds;}
				    var time    = hours+':'+minutes+':'+seconds;
				    return time;
				}
				jQuery(document).ready(function($) {
					var sendData = {
						action: 's3bubble_analytics_internal_ajax',
						security: '<?php echo $ajax_nonce; ?>'
					}
					$.post("<?php echo admin_url('admin-ajax.php'); ?>", sendData, function(response) {
						if(response.error){
							$(".s3bubble-analytics-loading").html("<small>" + response.message + "</small>");
						}else{
							var html = "";
							$.each( response.data, function( key, value ) {
								var country = 'zz';
								if(value.user_country){
									country = value.user_country.toLowerCase();
								}
								var overall = value.overall_watched;
								var watched = value.time_watched;
								var total = Math.round(watched/overall*100);
								total = (isNumeric(total)) ? total : '';
								html += '<tr>' +
											'<td align="center"><a href="' + value.location_href + '" target="_blank">Open</a></td>' +
							                '<td align="left"><div class="s3bubble-key-ellipse">' + baseName(value.key) + '</div></td>' +
							                '<td align="center"><img src="https://s3-eu-west-1.amazonaws.com/isdcloud/flags/' + country + '.png"></td>' +
							                '<td align="center">' + value.user_city + '</td>' +
							                '<td align="center">' + value.user_ip + '</td>' +
							                '<td align="center"><div class="s3bubble-key-ellipse">' + value.browser.split(" ")[0] + '</div></td>' +
							                '<td align="center">' + value.type + '</td>' +
							                '<td align="center">' + watched.toHHMMSS() + '</td>' +
							                '<td align="center"><div class="s3bubble-percentage"><span class="s3bubble-seekbar" style="width:' + total + '%;"></span><span class="s3bubble-total">' + total + '%</div></div></td>' +
							                '<td align="center">' + timeNow(value.created) + '</td>' +
							                '<td align="center"><a href="http://maps.google.com/maps?z=12&t=m&q=loc:' + value.user_loc_lat.replace(',', '+') + '" target="_blank">Open</a></td>' +
							            '</tr>';
							});
							$("#s3AnalyticsTable").append(html);
							$(".s3bubble-analytics-loading").html("<small>" + response.message + "</small>");
							$.tablesorter.addParser({
							    id: "customDate",
							    is: function(s) {
							        return /\d{1,4}-\d{1,2}-\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}\.\d+/.test(s);
							    },
							    format: function(s) {
							        s = s.replace(/\-/g," ");
							        s = s.replace(/:/g," ");
							        s = s.replace(/\./g," ");
							        s = s.split(" ");
							        return $.tablesorter.formatFloat(new Date(s[0], s[1]-1, s[2], s[3], s[4], s[5]).getTime()+parseInt(s[6]));
							    },
							    type: "numeric"
							});
							$("#s3AnalyticsTable").tablesorter({
								headers : {
									9 : {
										sorter : "customDate"
									}
								}
							});
						}
					},'json');
				});
			</script>
			<?php

		}

    	/*
		* S3Bubble debug page run route
		* @author sameast
		* @none
		*/ 
    	function s3bubble_debug_page_callback() {
			
			//Run a S3Bubble security check
			$ajax_nonce = wp_create_nonce( "s3bubble-nonce-security" );
			$url = $this->endpoint . 'main_plugin/debug';
			$ip = gethostbyname($this->get_domain(get_site_url()));
			
			?>
			<div class="wrap"><div id="icon-tools" class="icon32"></div>
				<h2>Running Debug - Checking the route to the S3Bubble api</h2>
				<div class="metabox-holder has-right-sidebar">
					<div id="post-body">
						<div id="post-body-content">
							<div class="postbox">
								<h3 class="hndle">Run some debugging tests - Current IP:<?php echo $ip; ?></h3>
								<div class="inside">
									<pre class='s3bubble-debug'></pre>
									<form action="" method="post" id="debug_remote_form" class="s3bubble-video-popup-form" autocomplete="off">
									    <table class="form-table">
									    	<tr style="position: relative;">
										        <th scope="row" valign="top"><label for="debug_url">Url:</label></th>
										        <td><input type="text" name="debug_url" id="debug_url" class="regular-text" value="<?php echo $url; ?>"/>
										        </td>
										    </tr>
									    	<tr style="position: relative;">
										        <th scope="row" valign="top"><label for="debug_method">Method:</label></th>
										        <td><input type="text" name="debug_method" id="debug_method" class="regular-text" value="POST"/>
										        </td>
										    </tr>
									    	<tr style="position: relative;">
										        <th scope="row" valign="top"><label for="debug_sslverify">Sslverify:</label></th>
										        <td><input type="text" name="debug_sslverify" id="debug_sslverify" class="regular-text" value="false"/>
										        </td>
										    </tr>
										    <tr style="position: relative;">
										        <th scope="row" valign="top"><label for="debug_timeout">Timeout:</label></th>
										        <td><input type="text" name="debug_timeout" id="debug_timeout" class="regular-text" value="10"/>
										        </td>
										    </tr>
										    <tr style="position: relative;">
										        <th scope="row" valign="top"><label for="debug_redirection">Redirection:</label></th>
										        <td><input type="text" name="debug_redirection" id="debug_redirection" class="regular-text" value="5"/>
										        </td>
										    </tr>
										    <tr style="position: relative;">
										        <th scope="row" valign="top"><label for="debug_httpversion">Httpversion:</label></th>
										        <td><input type="text" name="debug_httpversion" id="debug_httpversion" class="regular-text" value="1.0"/>
										        </td>
										    </tr>   
									      	<tr style="position: relative;">
										        <th scope="row" valign="top"><label for="debug_blocking">Blocking:</label></th>
										        <td><input type="text" name="debug_blocking" id="debug_blocking" class="regular-text" value="true"/>
										        </td>
										    </tr> 
									    <!-- end new -->
									    </table>
									    <br/>
									    <span class="submit" style="border: 0;">
									    	<input type="submit" name="submit" class="button button-s3bubble button-hero" value="Run Remote Post" />
									    </span>
									</form>
									<form action="" method="post" id="debug_traceroute_form" class="s3bubble-video-popup-form" autocomplete="off">
									    <span class="submit" style="border: 0;">
									    	<input type="submit" name="submit" class="button button-s3bubble button-hero" value="Run Traceroute" />
									    </span>
									</form>
								</div> 
							</div>
						</div> 
					</div> 
				</div>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function($) {

					// Run debug form
					$( "#debug_remote_form" ).submit(function( event ) {
						$(".s3bubble-debug").html("Running...");
						var sendData = {
							action: 's3bubble_wpremotepost_internal_ajax',
							security: '<?php echo $ajax_nonce; ?>',
							url: $("#debug_url").val(),
							method: $("#debug_method").val(),
							sslverify: $("#debug_sslverify").val(),
							timeout: parseInt($("#debug_timeout").val()),
							redirection: parseInt($("#debug_redirection").val()),
							httpversion: $("#debug_httpversion").val(),
							blocking: $("#debug_blocking").val()
						}	
						$.post("<?php echo admin_url('admin-ajax.php'); ?>", sendData, function(response) {
							$(".s3bubble-debug").html(JSON.stringify(response));
						},'json');
					  	event.preventDefault();
					});

					// Run trace route
					$( "#debug_traceroute_form" ).submit(function( event ) {
						$(".s3bubble-debug").html("Running traceroute please wait...");
						var sendData = {
							action: 's3bubble_traceroute_internal_ajax',
							security: '<?php echo $ajax_nonce; ?>'
						}		
						$.post("<?php echo admin_url('admin-ajax.php'); ?>", sendData, function(response) {
							$(".s3bubble-debug").html(response);
						});
					  	event.preventDefault();
					});

				});
			</script>
			<?php

		}
        
		/*
		* Add css to wordpress admin to run colourpicker
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_admin_scripts(){
			
			// Css
			wp_register_style( 's3bubble.video.all.admin', plugins_url('assets/css/s3bubble.video.all.admin.min.css', __FILE__), array(), $this->version );
			wp_register_style( 's3bubble.video.all.plugin', plugins_url('assets/css/s3bubble.video.all.plugin.min.css', __FILE__), array(), $this->version );
			
			wp_enqueue_style('s3bubble.video.all.admin');
			wp_enqueue_style('s3bubble.video.all.plugin');
			
			// Javascript
			//wp_enqueue_script( 's3bubble.video.all.tinymce', plugins_url( 'assets/js/s3bubble.video.all.tinymce.js', __FILE__ ), array( ), false, true ); 
			wp_enqueue_style( 'wp-color-picker' );
			// Javascript
			wp_enqueue_script( 's3bubble.video.all.colour', plugins_url( 'assets/js/s3bubble.video.all.colour.min.js', __FILE__ ), array( 'wp-color-picker' ), false, true ); 
			// Include the table script
			wp_register_script( 's3bubble-backup-stupidtable-js', plugins_url('assets/js/stupidtable.min.js', __FILE__) );
			wp_enqueue_script('s3bubble-backup-stupidtable-js');

		}
		
		/*
		* Add css ties into wp_head() function
		* @author sameast
		* @params none
        */ 
		function s3bubble_audio_css(){
			
			$progress	= get_option("s3bubble_video_all_bar_colours");
			$background	= get_option("s3bubble_video_all_controls_bg");
			$seek	    = get_option("s3bubble_video_all_bar_seeks");
			$icons	    = get_option("s3bubble_video_all_icons");
			
			wp_register_style( 'font-s3bubble.min', plugins_url('assets/css/font-awesome.min.css', __FILE__), array(), $this->version );
			wp_register_style( 'font-s3bubble-icon.min', plugins_url('assets/css/font-awesome-s3bubble.min.css', __FILE__), array(), $this->version );
			wp_register_style( 's3bubble.video.all.main', plugins_url('assets/css/s3bubble.video.all.main.min.css', __FILE__), array(), $this->version );
			
			wp_enqueue_style('font-s3bubble.min');
			wp_enqueue_style('font-s3bubble-icon.min');
			wp_enqueue_style('s3bubble.video.all.main');

			// Depreciated
			wp_enqueue_style('wp-mediaelement');
			wp_register_style('s3bubble.video.all.media.element.min', plugins_url('assets/css/s3bubble.video.all.media.element.min.css', __FILE__), array(), $this->version  );
			wp_enqueue_style('s3bubble.video.all.media.element.min');
			
			// Video js
			wp_register_style('s3bubble.video.js.css.include', plugins_url('assets/videojs/video-js.min.css', __FILE__), array(), $this->version  );
			wp_enqueue_style('s3bubble.video.js.css.include');

			// Popup styles
			wp_register_style('magnific-popup.min', plugins_url('assets/css/magnific-popup.min.css', __FILE__), array(), $this->version  );
			wp_enqueue_style('magnific-popup.min');

			// Important CDN fixes
			wp_register_style('s3bubble.helpers', ("//s3.amazonaws.com/s3bubble.assets/plugin.css/style.css"), array(),  $this->version );
			wp_enqueue_style('s3bubble.helpers');

			echo '<style type="text/css">
					.s3bubble-media-main-progress, .s3bubble-media-main-volume-bar {background-color: '.stripcslashes($progress).' !important;}
					.s3bubble-media-main-play-bar, .s3bubble-media-main-volume-bar-value {background-color: '.stripcslashes($seek).' !important;}
					.s3bubble-media-main-interface, .s3bubble-media-main-video-play, .s3bubble-media-main-video-skip, .s3bubble-media-main-preview-over {background-color: '.stripcslashes($background).' !important;color: '.stripcslashes($icons).' !important;}
					.s3bubble-media-main-video-loading {color: '.stripcslashes($icons).' !important;}
					.s3bubble-media-main-interface  > * a, .s3bubble-media-main-interface  > * a:hover, .s3bubble-media-main-interface  > * i, .s3bubble-media-main-interface  > * i:hover, .s3bubble-media-main-current-time, .s3bubble-media-main-duration, .time-sep, .s3icon-cloud-download {color: '.stripcslashes($icons).' !important;text-decoration: none !important;font-style: normal !important;}
					.s3bubble-media-main-video-skip h2, .s3bubble-media-main-preview-over-container h2 {color: '.stripcslashes($icons).' !important;}
					.s3bubble-media-main-playlist-current {color: '.stripcslashes($seek).' !important;}
					.mejs-controls {background-color: '.stripcslashes($background).' !important;}
					.mejs-overlay-button {background: '.stripcslashes($background).' url(' . plugins_url('assets/images/play48.png', __FILE__) . ')center no-repeat !important;}
					.mejs-time-current, .mejs-horizontal-volume-current {background-color: '.stripcslashes($seek).' !important;}
					.vjs-control-bar {background-color: '.stripcslashes($background).' !important;}
			</style>'; 

		}
		
		/*
		* Add javascript to the footer connect to wp_footer()
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_javascript(){
			
			if (!is_admin()) {

				wp_register_script( 's3player.all.s3bubble', plugins_url('assets/js/s3player.video.all.player.min.js',__FILE__ ), array('jquery'), $this->version, true  );
				wp_localize_script('s3player.all.s3bubble', 's3bubble_all_object', array(
					's3appid' => get_option("s3-s3audible_username"),
					'serveraddress' => $_SERVER['REMOTE_ADDR'],
					'ajax_url' => admin_url( 'admin-ajax.php' )
				));
				wp_register_script( 's3bubble.mobile.browser.check', plugins_url('assets/js/mobile.browser.check.min.js',__FILE__ ), array('jquery'),  $this->version, true );
				wp_register_script( 's3bubble.analytics.min', plugins_url('assets/js/s3analytics.min.js',__FILE__ ), array('jquery'),  $this->version, true );

				//Video js
				wp_register_script( 's3bubble.video.js.include', plugins_url('assets/videojs/video.min.js',__FILE__ ), array('jquery'),  $this->version, true );
				wp_register_script( 'tweetAction', plugins_url('assets/js/jquery.tweetAction.min.js',__FILE__ ), array('jquery'),  $this->version, true );
				wp_register_script( 'singleVideoDeploy', plugins_url('assets/js/s3bubble.single.video.deploy.min.js',__FILE__ ), array('jquery'),  $this->version, true );
				wp_register_script( 'VideoPlaylistDeploy', plugins_url('assets/js/s3bubble.video.playlist.deploy.min.js',__FILE__ ), array('jquery'),  $this->version, true );
				wp_register_script( 'singleAudioDeploy', plugins_url('assets/js/s3bubble.single.audio.deploy.min.js',__FILE__ ), array('jquery'),  $this->version, true );
				wp_register_script( 'AudioPlaylistDeploy', plugins_url('assets/js/s3bubble.audio.playlist.deploy.min.js',__FILE__ ), array('jquery'),  $this->version, true );
				wp_register_script( 'jquery.magnific-popup', plugins_url('assets/js/jquery.magnific-popup.min.js',__FILE__ ), array('jquery'),  $this->version, true );

				wp_enqueue_script('jquery');
				wp_enqueue_script('jquery-migrate');
				wp_enqueue_script('wp-mediaelement');
				wp_enqueue_script('s3bubble.video.js.include');
				wp_enqueue_script('s3player.all.s3bubble');
				wp_enqueue_script('s3bubble.mobile.browser.check');
				wp_enqueue_script('s3bubble.analytics.min');
				wp_enqueue_script('tweetAction');
				wp_enqueue_script('singleVideoDeploy');
				wp_enqueue_script('VideoPlaylistDeploy');
				wp_enqueue_script('singleAudioDeploy');
				wp_enqueue_script('AudioPlaylistDeploy');
				wp_enqueue_script('jquery.magnific-popup');

				// remove jplayer if exists
				wp_deregister_script( 'jplayer' );
				
            }
		}

		/*
		* Outputs some memory information
		* @author sameast
		* @none
		*/ 
		function s3bubble_convert_memory(){

		    $mem_usage = memory_get_usage(true); 
	        $unit=array('b','kb','mb','gb','tb','pb');
	        $mem_out = @round($mem_usage/pow(1024,($i=floor(log($mem_usage,1024)))),2).' '.$unit[$i];
            return "Memory usage: " . $mem_out . ". Max execution time: " . ini_get('max_execution_time');

		}

		/*
		* S3Bubble trace route debug tests
		* @author sameast
		* @none
		*/ 
		function s3bubble_traceroute_internal_ajax(){
			
			// Run security check
			check_ajax_referer( 's3bubble-nonce-security', 'security' );

			function isEnabled($func) {
			    return is_callable($func) && false === stripos(ini_get('disable_functions'), $func);
			}

			if (isEnabled('shell_exec')) {
				$output = shell_exec('traceroute api.s3bubble.com');
				echo $output;
			}else{
				echo "Trying to run a traceroute this may not work on certain hosts.";
			} 

			wp_die();	
			
		}

		/*
		* S3Bubble wp remote debug tests
		* @author sameast
		* @none
		*/ 
		function s3bubble_wpremotepost_internal_ajax(){
			
			// Run security check
			check_ajax_referer( 's3bubble-nonce-security', 'security' );

			//set POST variables
			$response = wp_remote_post( (($_POST['url'] == '') ? $this->endpoint . 'main_plugin/debug' : $_POST['url']), array(
				'method' => (($_POST['method'] == 'POST') ? 'POST' : 'GET'),
				'sslverify' => (($_POST['sslverify'] == 'true') ? true : false),
				'timeout' => $_POST['timeout'],
				'redirection' => $_POST['redirection'],
				'httpversion' => $_POST['httpversion'],
				'blocking' => (($_POST['blocking'] == 'true') ? true : false),
				'headers' => array(),
				'body' => array(
					'Test' => "Hello World"
				),
				'cookies' => array()
			    )
			);

			if ( is_wp_error( $response ) ) {

			   $error_message = $response->get_error_message();
			   echo json_encode(
			   					array(
			   						"error" => true, 
			   						"message" => $error_message . ". Stats " . $this->s3bubble_convert_memory() .". Please contact support@s3bubble.com this error is normally related to a hosting issue."
			   						)
			   					);
			} else {

			   echo $response['body'];

			}

			wp_die();	
			
		}

		/*
		* Analytics get all
		* @author sameast
		* @none
		*/ 
		function s3bubble_analytics_internal_ajax(){
			
			// Run security check
			check_ajax_referer( 's3bubble-nonce-security', 'security' );

			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");

			//set POST variables
			$url = $this->endpoint . 'analytics/all';
			$response = wp_remote_post( $url, array(
				'method' => 'POST',
				'sslverify' => false,
				'timeout' => 10,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'body' => array(
					'AccessKey' => $s3bubble_access_key
				),
				'cookies' => array()
			    )
			);

			if ( is_wp_error( $response ) ) {

			   $error_message = $response->get_error_message();
			   echo json_encode(
			   					array(
			   						"error" => true, 
			   						"message" => $error_message . ". Stats " . $this->s3bubble_convert_memory() .". Please contact support@s3bubble.com this error is normally related to a hosting issue."
			   						)
			   					);
			} else {

			   echo $response['body'];

			}

			wp_die();	
			
		}

		/*
		* Video playlist internal ajax call
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_rtmp_internal_ajax(){
			
			// Run security check
			check_ajax_referer( 's3bubble-nonce-security', 'security' );

			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");

			//set POST variables
			$url = $this->endpoint . 'main_plugin/single_video_rtmp_live';
			$response = wp_remote_post( $url, array(
				'method' => 'POST',
				'sslverify' => false,
				'timeout' => 10,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'body' => array(
					'AccessKey' => $s3bubble_access_key,
				    'SecretKey' => $s3bubble_secret_key,
				    'Timezone' => 'America/New_York',
				    'Bucket' => $_POST['Bucket'],
				    'Key' => $_POST['Key'],
				    'Cloudfront' => $_POST['Cloudfront'],
				    'Server' => $_POST['Server'],
				    'IsMobile' => $_POST['IsMobile']
				),
				'cookies' => array()
			    )
			);

			if ( is_wp_error( $response ) ) {

			   $error_message = $response->get_error_message();
			   echo json_encode(
			   					array(
			   						"error" => true, 
			   						"message" => $error_message . ". Stats " . $this->s3bubble_convert_memory() .". Please contact support@s3bubble.com this error is normally related to a hosting issue."
			   						)
			   					);
			} else {

			   echo $response['body'];

			}

			wp_die();	
			
		}

		/*
		* Audio playlist internal ajax call
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_rtmp_internal_ajax(){
			
			// Run security check
			check_ajax_referer( 's3bubble-nonce-security', 'security' );

			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");

			//set POST variables
			$url = $this->endpoint . 'rtmp/audio';
			$response = wp_remote_post( $url, array(
				'method' => 'POST',
				'sslverify' => false,
				'timeout' => 10,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'body' => array(
					'AccessKey' => $s3bubble_access_key,
				    'SecretKey' => $s3bubble_secret_key,
				    'Timezone' => 'America/New_York',
				    'Bucket' => $_POST['Bucket'],
				    'Key' => $_POST['Key'],
				    'Cloudfront' => $_POST['Cloudfront'],
				    'IsMobile' => $_POST['IsMobile']
				),
				'cookies' => array()
			    )
			);

			if ( is_wp_error( $response ) ) {

			   $error_message = $response->get_error_message();
			   echo json_encode(
			   					array(
			   						"error" => true, 
			   						"message" => $error_message . ". Stats " . $this->s3bubble_convert_memory() .". Please contact support@s3bubble.com this error is normally related to a hosting issue."
			   						)
			   					);
			} else {

			   echo $response['body'];

			}

			wp_die();	
			
		}

		/*
		* Video single internal ajax call
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_single_internal_ajax(){

			// Run security check
			check_ajax_referer( 's3bubble-nonce-security', 'security' );

			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");

			//set POST variables
			$url = $this->endpoint . 'main_plugin/single_video_object';
			$response = wp_remote_post( $url, array(
				'method' => 'POST',
				'sslverify' => false,
				'timeout' => 10,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'body' => array(
					'AccessKey' => $s3bubble_access_key,
				    'SecretKey' => $s3bubble_secret_key,
				    'Timezone' => 'America/New_York',
				    'Bucket' => $_POST['Bucket'],
				    'Key' => $_POST['Key'],
				    'Cloudfront' => $_POST['Cloudfront'],
				    'Server' => $_POST['Server']
				),
				'cookies' => array()
			    )
			);

			if ( is_wp_error( $response ) ) {

			   $error_message = $response->get_error_message();
			   echo json_encode(
			   					array(
			   						"error" => true, 
			   						"message" => $error_message . ". Stats " . $this->s3bubble_convert_memory() .". Please contact support@s3bubble.com this error is normally related to a hosting issue."
			   						)
			   					);
			} else {

			   echo $response['body'];

			}
			
			wp_die();	

		}

		/*
		* Video playlist internal ajax call
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_playlist_internal_ajax(){
			
			// Run security check
			check_ajax_referer( 's3bubble-nonce-security', 'security' );

			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");

			//set POST variables
			$url = $this->endpoint . 'main_plugin/playlist_video_objects';
			$response = wp_remote_post( $url, array(
				'method' => 'POST',
				'sslverify' => false,
				'timeout' => 60,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'body' => array(
					'AccessKey' => $s3bubble_access_key,
				    'SecretKey' => $s3bubble_secret_key,
				    'Timezone' => 'America/New_York',
				    'Bucket' => $_POST['Bucket'],
				    'Folder' => $_POST['Folder'],
				    'Cloudfront' => $_POST['Cloudfront'],
				    'IsMobile' => $_POST['IsMobile'],
				    'Server' => $_POST['Server']
				),
				'cookies' => array()
			    )
			);

			if ( is_wp_error( $response ) ) {

			   $error_message = $response->get_error_message();
			   echo json_encode(
			   					array(
			   						"error" => true, 
			   						"message" => $error_message . ". Stats " . $this->s3bubble_convert_memory() .". Please contact support@s3bubble.com this error is normally related to a hosting issue."
			   						)
			   					);
			} else {

			   echo $response['body'];

			}
			
			wp_die();	

		}

		/*
		* Audio single internal ajax call
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_single_internal_ajax(){
			
			// Run security check
			check_ajax_referer( 's3bubble-nonce-security', 'security' );

			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");

			//set POST variables
			$url = $this->endpoint . 'main_plugin/single_audio_object';
			$response = wp_remote_post( $url, array(
				'method' => 'POST',
				'sslverify' => false,
				'timeout' => 10,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'body' => array(
					'AccessKey' => $s3bubble_access_key,
				    'SecretKey' => $s3bubble_secret_key,
				    'Timezone' => 'America/New_York',
				    'Bucket' => $_POST['Bucket'],
				    'Key' => $_POST['Key'],
				    'Cloudfront' => $_POST['Cloudfront'],
				    'Server' => $_POST['Server']
				),
				'cookies' => array()
			    )
			);

			if ( is_wp_error( $response ) ) {

			   $error_message = $response->get_error_message();
			   echo json_encode(
			   					array(
			   						"error" => true, 
			   						"message" => $error_message . ". Stats " . $this->s3bubble_convert_memory() .". Please contact support@s3bubble.com this error is normally related to a hosting issue."
			   						)
			   					);
			} else {

			   echo $response['body'];

			}
			
			wp_die();	

		}

        /*
		* Audio playlist internal ajax call
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_playlist_internal_ajax(){
			
			// Run security check
			check_ajax_referer( 's3bubble-nonce-security', 'security' );

			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");

			//set POST variables
			$url = $this->endpoint . 'main_plugin/playlist_audio_objects';
			$response = wp_remote_post( $url, array(
				'method' => 'POST',
				'sslverify' => false,
				'timeout' => 30,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'body' => array(
					'AccessKey' => $s3bubble_access_key,
				    'SecretKey' => $s3bubble_secret_key,
				    'Timezone' => 'America/New_York',
				    'Bucket' => $_POST['Bucket'],
				    'Folder' => $_POST['Folder']
				),
				'cookies' => array()
			    )
			);

			if ( is_wp_error( $response ) ) {

			   $error_message = $response->get_error_message();
			   echo json_encode(
			   					array(
			   						"error" => true, 
			   						"message" => $error_message . ". Stats " . $this->s3bubble_convert_memory() .". Please contact support@s3bubble.com this error is normally related to a hosting issue."
			   						)
			   					);
			} else {

			   echo $response['body'];

			}
			
			wp_die();	

		}
        
        /*
		* Audio playlist button callback
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_playlist_ajax(){
		    // echo the form
		    $s3bubble_access_key = get_option("s3-s3audible_username");
		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        	$('#TB_ajaxContent').css({
                    	'width' : 'auto',
                    	'height' : 'auto',
                    	'padding' : '0'
                    });
			        var sendData = {
						AccessKey: '<?php echo $s3bubble_access_key; ?>'
					};
					$.post("<?php echo $this->endpoint; ?>main_plugin/live_buckets/", sendData, function(response) {
						if(response.error){
							$(".s3bubble-video-main-form-alerts").html("<p>Oh Snap! " + response.message + ". If you do not understand this error please contact support@s3bubble.com</p>");
						}else{
							$(".s3bubble-video-main-form-alerts").html("<p>Awesome! " + response.message + ".</p>");
							var isSingle = response.data.Single;
							var html = '<select class="form-control input-lg" tabindex="1" name="s3bucket" id="s3bucket"><option value="">Choose bucket</option>';
						    $.each(response.data.Buckets, function (i, item) {
						    	var bucket = item.Name;
						    	if(isSingle === true){
						    		html += '<option value="s3bubble.users">' + bucket + '</option>';
						    	}else{
						    		html += '<option value="' + bucket + '">' + bucket + '</option>';	
						    	}
							});
							html += '</select>';
							$('#s3bubble-buckets-shortcode').html(html);
						}
						$( "#s3bucket" ).change(function() {
						   $('#s3bubble-folders-shortcode').html('<img src="<?php echo plugins_url('/assets/js/ajax-loader.gif',__FILE__); ?>"/> loading folders');
						   var bucket = $(this).val();
						   if(isSingle === true){
						   		bucket = $("#s3bucket option:selected").text();
						   }			   
						   var data = {
								AccessKey: '<?php echo $s3bubble_access_key; ?>',
								Bucket: bucket
							};
							$.post("<?php echo $this->endpoint; ?>main_plugin/folders/", data, function(response) {
								var html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder"><option value="">Choose folder</option><option value="">Root</option>';
								if(isSingle === true){
							   		html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder">';
							    }	
							    $.each(response, function (i, item) {
							    	var folder = item;
							    	if(isSingle === true){
										html += '<option value="' + folder + '">' + ((i === 0) ? 'root' : folder.split('/').reverse()[0]) + '</option>';
									}else{
										html += '<option value="' + folder + '">' + folder + '</option>';
									}
								});
								html += '</select>';
								$('#s3bubble-folders-shortcode').html(html);
						   },'json');
						});				
					},'json');
					setTimeout(function(){
						$(".s3bubble-lightbox-wrap").height($("#TB_window").height());
					},500);
			        $('#s3bubble-mce-submit').click(function(){
			        	var bucket     = $('#s3bucket').val();
			        	var folder     = $('#s3folder').val();
			        	var cloudfront = $('#s3cloudfront').val();
			        	var height     = $('#s3height').val();
			        	if($("#s3autoplay").is(':checked')){
						    var autoplay = true;
						}else{
						    var autoplay = false;
						}
						if($("#s3playlist").is(':checked')){
						    var playlist = 'hidden';
						}else{
						    var playlist = 'show';
						}
			        	var order      = $('#s3order').val();
			        	if($("#s3order").is(':checked')){
						    var order = 'order="desc"';
						}
						if($("#s3download").is(':checked')){
						    var download = true;
						}else{
						    var download = false;
						}
						if($("#s3preload").is(':checked')){
						    var preload = 'none';
						}else{
						    var preload = 'auto';
						}
	        	        var shortcode = '[s3bubbleAudio bucket="' + bucket + '" folder="' + folder + '"  height="' + height + '"  autoplay="' + autoplay + '" playlist="' + playlist + '" ' + order + ' download="' + download + '"  preload="' + preload + '"/]';
	                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
	                    tb_remove();
			        });
		        })
		    </script>
		    <div class="s3bubble-lightbox-wrap">
			    <form class="s3bubble-form-general">
			    	<div class="s3bubble-video-main-form-alerts"></div>
			    	<span>
				    	<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="fname">Your S3Bubble Buckets/Folders:</label>
				    		<span id="s3bubble-buckets-shortcode">loading buckets...</span>
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="fname">Your S3Bubble Files:</label>
				    		<span id="s3bubble-folders-shortcode">Select bucket/folder...</span>
				    	</div>
					</span>
					<span>
				    	<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="fname">Set A Playlist Height: <i>(Do Not Add PX)</i></label>
				    		<input type="text" class="s3bubble-form-input" name="height" id="s3height">
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<input type="hidden" class="s3bubble-form-input" name="cloudfront" id="s3cloudfront">
				    	</div>
					</span>
					<blockquote class="bs-callout-s3bubble"><strong>Extra options</strong> please just select any extra options from the list below and S3Bubble will automatically add it to the shortcode.</blockquote><br />
					<span>
						<input type="checkbox" name="autoplay" id="s3autoplay">Autoplay <i>(Start Audio On Page Load)</i><br />
						<input type="checkbox" name="playlist" id="s3playlist" value="hidden">Hide Playlist <i>(Hide Playlist On Page Load)</i><br />
						<input type="checkbox" name="order" id="s3order" value="desc">Reverse Order <i>(Reverse The Playlist Order)</i><br />
						<input class="s3bubble-checkbox" type="checkbox" name="s3preload" id="s3preload" value="true">Preload Off <i>(Prevent Tracks From Preloading)</i><br />
						<input type="checkbox" name="download" id="s3download" value="true">Show Download Links <i>(Adds A Download Button To The Tracks)</i>
					</span>
					<span>
						<a href="#"  id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
					</span>
				</form>
			</div>
        	<?php
        	wp_die();
		}
		
		/*
		* Video playlist button callback
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_playlist_ajax(){
			// echo the form
		    $s3bubble_access_key = get_option("s3-s3audible_username");
		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        	$('#TB_ajaxContent').css({
                    	'width' : 'auto',
                    	'height' : 'auto',
                    	'padding' : '0'
                    });
			        var sendData = {
						AccessKey: '<?php echo $s3bubble_access_key; ?>'
					};
					$.post("<?php echo $this->endpoint; ?>main_plugin/live_buckets/", sendData, function(response) {	
						if(response.error){
							$(".s3bubble-video-main-form-alerts").html("<p>Oh Snap! " + response.message + ". If you do not understand this error please contact support@s3bubble.com</p>");
						}else{
							$(".s3bubble-video-main-form-alerts").html("<p>Awesome! " + response.message + ".</p>");
							var isSingle = response.data.Single;
							var html = '<select class="form-control input-lg" tabindex="1" name="s3bucket" id="s3bucket"><option value="">Choose bucket</option>';
						    $.each(response.data.Buckets, function (i, item) {
						    	var bucket = item.Name;
						    	if(isSingle === true){
						    		html += '<option value="s3bubble.users">' + bucket + '</option>';
						    	}else{
						    		html += '<option value="' + bucket + '">' + bucket + '</option>';	
						    	}
							});
							html += '</select>';
							$('#s3bubble-buckets-shortcode').html(html);
						}
						// Get Cloudfront ids if they are present
						var data = {
							AccessKey: '<?php echo $s3bubble_access_key; ?>'
						};
						$.post("<?php echo $this->endpoint; ?>main_plugin/list_cloudfront_distributions/", data, function(response) {
							var html = '<select class="form-control input-lg" tabindex="1" name="s3bubble-cloudfrontid" id="s3bubble-cloudfrontid">';	
							if(response.error){
								html += '<option value="">-- No Cloudfront Distributions --</option>';
							}else{
								if(response.data.Items){
									html += '<option value="">-- Cloudfront ID --</option>';
								    $.each(response.data.Items, function (i, item) {
								    	var Cloudfront = item;
								    	console.log(Cloudfront);
								    	html += '<option value="' + Cloudfront.Id + '">' + Cloudfront.Id + ' - ' + Cloudfront.S3Origin.DomainName + ' - Enabled: ' + Cloudfront.Enabled + '</option>';
									});
								}else{
									html += '<option value="">-- No Cloudfront Distributions --</option>';
								}
							}
							html += '</select>';
							$('#s3bubble-cloudfrontid-container').html(html);
					   	},'json');
						$( "#s3bucket" ).change(function() {
						   $('#s3bubble-folders-shortcode').html('<img src="<?php echo plugins_url('/assets/js/ajax-loader.gif',__FILE__); ?>"/> loading folders');
						   var bucket = $(this).val();
						   if(isSingle === true){
						   		bucket = $("#s3bucket option:selected").text();
						   }	
						   var data = {
								AccessKey: '<?php echo $s3bubble_access_key; ?>',
								Bucket: bucket
							};
							$.post("<?php echo $this->endpoint; ?>main_plugin/folders/", data, function(response) {
							    var html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder"><option value="">Choose folder</option><option value="">Root</option>';
								if(isSingle === true){
							   		html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder">';
							    }	
							    $.each(response, function (i, item) {
							    	var folder = item;
							    	if(isSingle === true){
										html += '<option value="' + folder + '">' + ((i === 0) ? 'root' : folder.split('/').reverse()[0]) + '</option>';
									}else{
										html += '<option value="' + folder + '">' + folder + '</option>';
									}
								});
								html += '</select>';
								$('#s3bubble-folders-shortcode').html(html);
						   },'json');
						});				
					},'json');
					setTimeout(function(){
						$(".s3bubble-lightbox-wrap").height($("#TB_window").height());
					},500);
			        $('#s3bubble-mce-submit').click(function(){
			        	var bucket       = $('#s3bucket').val();
			        	var folder       = $('#s3folder').val();
			        	var height       = $('#s3height').val();
			        	var cloudfrontid = $('#s3bubble-cloudfrontid').val();
			        	if($("#s3autoplay").is(':checked')){
						    var autoplay = true;
						}else{
						    var autoplay = false;
						}
						if($("#s3playlist").is(':checked')){
						    var playlist = 'hidden';
						}else{
						    var playlist = 'show';
						}
			        	var order      = $('#s3order').val();
			        	if($("#s3order").is(':checked')){
						    var order = 'order="desc"';
						}
						if($("#s3download").is(':checked')){
						    var download = true;
						}else{
						    var download = false;
						}
						var aspect = '16:9';
						if($('#s3aspect').val() != ''){
						    aspect = $('#s3aspect').val();
						}
	        	        var shortcode = '[s3bubbleVideo bucket="' + bucket + '" folder="' + folder + '" aspect="' + aspect + '"  height="' + height + '"  autoplay="' + autoplay + '" playlist="' + playlist + '" ' + order + ' download="' + download + '" cloudfront="' + cloudfrontid + '"/]';
	                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
	                    tb_remove();
			        });
		        })
		    </script>
		    <div class="s3bubble-lightbox-wrap">
			    <form class="s3bubble-form-general">
			    	<div class="s3bubble-video-main-form-alerts"></div>
			    	<span>
				    	<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="fname">Your S3Bubble Buckets/Folders:</label>
				    		<span id="s3bubble-buckets-shortcode">loading buckets...</span>
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="fname">Your S3Bubble Files:</label>
				    		<span id="s3bubble-folders-shortcode">Select bucket/folder...</span>
				    	</div>
					</span>
					<span>
				    	<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="fname">Aspect Ratio: (Example: 16:9 / 4:3 Default: 16:9)</label>
				    		<input type="text" class="s3bubble-form-input" name="aspect" id="s3aspect">
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="fname">Set A Playlist Height:</label>
				    		<input type="text" class="s3bubble-form-input" name="height" id="s3height">
				    	</div>
					</span>
					<span>
				    	<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="fname">Set Cloudfront Distribution Id:</label>
				    		<span id="s3bubble-cloudfrontid-container">Select Cloudfront...</span>
				    	</div>
					</span>
					<blockquote class="bs-callout-s3bubble"><strong>Extra options</strong> please just select any extra options from the list below and S3Bubble will automatically add it to the shortcode.</blockquote>
					<span>
						<input type="checkbox" name="autoplay" id="s3autoplay">Autoplay <i>(Start Video On Page Load)</i><br />
						<input type="checkbox" name="playlist" id="s3playlist" value="hidden">Hide Playlist <i>(Hide Playlist On Page Load)</i><br />
						<input type="checkbox" name="order" id="s3order" value="desc">Reverse Order <i>(Reverse The Playlist Order)</i><br />
						<input type="checkbox" name="download" id="s3download" value="true">Show Download Links <i>(Adds A Download Button To The Videos)</i>
					</span>
				    <span>
						<a href="#"  id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
					</span>
				</form>
			</div>
        	<?php
        	wp_die();
		}
        
		/*
		* Single video button callback
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_single_ajax(){
		    // echo the form
		    $s3bubble_access_key = get_option("s3-s3audible_username");
		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {

		        	// Setup vars
		        	var StreamingType  = 'progressive';
		        	var FileExtension  = 'mp4';

		        	$('#TB_ajaxContent').css({
                    	'width' : 'auto',
                    	'height' : 'auto',
                    	'padding' : '0'
                    });
			        var sendData = {
						AccessKey: '<?php echo $s3bubble_access_key; ?>'
					};
					$.post("<?php echo $this->endpoint; ?>main_plugin/live_buckets/", sendData, function(response) {
						if(response.error){
							$(".s3bubble-video-main-form-alerts").html("<p>Oh Snap! " + response.message + ". If you do not understand this error please contact support@s3bubble.com</p>");
						}else{
							$(".s3bubble-video-main-form-alerts").html("<p>Awesome! " + response.message + ".</p>");
							var isSingle = response.data.Single;
							var html = '<select class="form-control input-lg" tabindex="1" name="s3bucket" id="s3bucket"><option value="">Choose bucket</option>';
						    $.each(response.data.Buckets, function (i, item) {
						    	var bucket = item.Name;
						    	if(isSingle === true){
						    		html += '<option value="s3bubble.users">' + bucket + '</option>';
						    	}else{
						    		html += '<option value="' + bucket + '">' + bucket + '</option>';	
						    	}
							});
							html += '</select>';
							$('#s3bubble-buckets-shortcode').html(html);
						}
						// Get Cloudfront ids if they are present
						var data = {
							AccessKey: '<?php echo $s3bubble_access_key; ?>'
						};
						$.post("<?php echo $this->endpoint; ?>main_plugin/list_cloudfront_distributions/", data, function(response) {
							var html = '<select class="form-control input-lg" tabindex="1" name="s3bubble-cloudfrontid" id="s3bubble-cloudfrontid">';	
							if(response.error){
								html += '<option value="">-- No Cloudfront Distributions --</option>';
							}else{
								html += '<option value="">-- Cloudfront ID --</option>';
								if(response.data.Items){
								    $.each(response.data.Items, function (i, item) {
								    	var Cloudfront = item;
								    	console.log(Cloudfront);
								    	html += '<option value="' + Cloudfront.Id + '">' + Cloudfront.Id + ' - ' + Cloudfront.S3Origin.DomainName + ' - Enabled: ' + Cloudfront.Enabled + '</option>';
									});
								}else{
									html += '<option value="">-- No Cloudfront Distributions --</option>';
								}
							}
							html += '</select>';
							$('#s3bubble-cloudfrontid-container').html(html);
					   	},'json');

						// Runs when a bucket is selected
						$( "#s3bucket" ).change(function() {
						   $('#s3bubble-folders-shortcode').html('<img src="<?php echo plugins_url('/assets/js/ajax-loader.gif',__FILE__); ?>"/> loading videos files');
						   var bucket = $(this).val();
						   if(isSingle === true){
						   		bucket = $("#s3bucket option:selected").text();
						   }
							var data = {
								AccessKey: '<?php echo $s3bubble_access_key; ?>',
								Bucket: bucket
							};
							$.post("<?php echo $this->endpoint; ?>main_plugin/video_files/", data, function(response) {
								var html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder"><option value="">Choose video</option>';
							    $.each(response, function (i, item) {
							    	if(isSingle === true){
										html += '<option value="' + item + '">' + item + '</option>';
									}else{
								    	var folder = item.Key;
								    	var ext    = folder.split('.').pop();
								    	if(ext == 'mp4' || ext === 'm4v' || ext === 'm3u8'){
								    		html += '<option value="' + folder + '" data-ext="' + ext + '">' + folder + '</option>';
								    	}
								    }
								});
								html += '</select>';
								$('#s3bubble-folders-shortcode').html(html);
						   },'json');
						});				
					},'json');
					$( "#s3bubble-streaming-type" ).change(function() {
						StreamingType = $(this).val();
					});
					setTimeout(function(){
						$(".s3bubble-lightbox-wrap").height($("#TB_window").height());
					},500);

					$('#s3bubble-mce-submit-iframe').click(function(){

						// Setup vars
			        	var bucket       = $('#s3bucket').val();
			        	var folder       = $('#s3folder').val();
			        	var cloudfrontid = $('#s3bubble-cloudfrontid').val();
			        	var extension    = $('#s3folder').find(':selected').data('ext');
			        	//Set extra options
			        	if($("#s3autoplay").is(':checked')){
						    var autoplay = true;
						}else{
						    var autoplay = false;
						}
						if($("#s3download").is(':checked')){
						    var download = true;
						}else{
						    var download = false;
						}
						var aspect = '16:9';
						if($('#s3aspect').val() != ''){
						    aspect = $('#s3aspect').val();
						}

						var data = {
							AccessKey: '<?php echo $s3bubble_access_key; ?>',
							bucket: bucket,
							key: folder,
							Distribution : cloudfrontid,
							StreamingType : StreamingType
						};

						$.post("<?php echo $this->endpoint; ?>iframe/get_code/", data, function(response) {

							if(response.error){

								alert(response.message);

							}else{
								
								var code = response.data;
						   		shortcode = '[s3bubbleVideoSingleIframe code="' + code + '" supplied="' + StreamingType + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '"/]';
								tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
		                    	tb_remove();
		                    }

                    	},'json');

					});

			        $('#s3bubble-mce-submit').click(function(){

			        	// Setup vars
			        	var bucket       = $('#s3bucket').val();
			        	var folder       = $('#s3folder').val();
			        	var cloudfrontid = $('#s3bubble-cloudfrontid').val();
			        	var extension    = $('#s3folder').find(':selected').data('ext');
			        	var lightboxtext = $("#lightbox-text").val();

			        	if(bucket === '' || folder === ''){

			        		alert("You must set a bucket and video to insert shortcode.");
			        	
			        	}else{

				        	//Set extra options
				        	if($("#s3autoplay").is(':checked')){
							    var autoplay = true;
							}else{
							    var autoplay = false;
							}
							if($("#s3download").is(':checked')){
							    var download = true;
							}else{
							    var download = false;
							}
							var aspect = '16:9';
							if($('#s3aspect').val() != ''){
							    aspect = $('#s3aspect').val();
							}

							var start = false;
							if($('#s3bubble-preview-starttime').val() != ''){
							    start = $('#s3bubble-preview-starttime').val();
							}

							var finish = false;
							if($('#s3bubble-preview-finishtime').val() != ''){
							    finish = $('#s3bubble-preview-finishtime').val();
							}

							var data = {
								AccessKey: '<?php echo $s3bubble_access_key; ?>',
								bucket: bucket,
								key: folder,
								Distribution : cloudfrontid
							};
							console.log(data);

							var shortcode = '';
							if(StreamingType === 'progressive'){
								shortcode = '[s3bubbleVideoSingle bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '" start="' + start + '" finish="' + finish + '" /]';
								if($("#s3mediaelement").is(':checked')){
								    shortcode = '[s3bubbleMediaElementVideo bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '"/]';
								}else if($("#s3videojs").is(':checked')){
									shortcode = '[s3bubbleVideoSingleJs  bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '"/]';
								}else if(lightboxtext !== ""){
									shortcode = '[s3bubbleLightboxVideoSingle text="' + lightboxtext + '" bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '"/]';
								}
								tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
		                    	tb_remove();
							}
							if(StreamingType === 'hls'){
								if(extension !== 'm3u8'){
									alert('To use HLS streaming your file extension needs to be .m3u8');
								}else{
									shortcode = '[s3bubbleHlsVideo bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '"/]';
									if($("#s3videojs").is(':checked')){
										shortcode = '[s3bubbleHlsVideoJs bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '"/]';
									}
									tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
		                    		tb_remove();
								}
							}
							if(StreamingType === 'rtmp'){
								if(cloudfrontid === ''){
									alert('To use RTMP streaming you need to specify a Cloudfront Distribution ID');
								}else{
									shortcode = '[s3bubbleRtmpVideoDefault bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '" start="' + start + '" finish="' + finish + '" /]';
									if($("#s3mediaelement").is(':checked')){
										shortcode = '[s3bubbleRtmpVideo bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '"/]';
									}else if($("#s3videojs").is(':checked')){
										shortcode = '[s3bubbleRtmpVideoJs bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '"/]';
									}
									tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			                   	 	tb_remove();
			                   	}
							}
						}
			        });
		        })
		    </script>
		    <div class="s3bubble-lightbox-wrap">
			    <form class="s3bubble-form-general">
	                <div class="s3bubble-video-main-form-alerts"></div>
			    	<span>
				    	<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="fname">Your S3Bubble Buckets/Folders:</label>
				    		<span id="s3bubble-buckets-shortcode">loading buckets...</span>
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="fname">Your S3Bubble Files:</label>
				    		<span id="s3bubble-folders-shortcode">Select bucket/folder...</span>
				    	</div>
					</span>
					<span>
						<div class="s3bubble-pull-left s3bubble-width-left">
							<label for="fname">Select Streaming Type:</label>
				    		<select class="form-control input-lg" tabindex="1" name="s3bubble-streaming-type" id="s3bubble-streaming-type">
				    			<option value="progressive">Progressive</option>
				    			<option value="rtmp">Rtmp</option>
				    			<option value="hls">HLS</option>
				    		</select>
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="fname">Set Cloudfront Distribution Id:</label>
				    		<span id="s3bubble-cloudfrontid-container">Select Cloudfront...</span>
				    	</div>
					</span>
					<span>
						<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="aspect">Aspect Ratio: (Example: 16:9 / 4:3 Default: 16:9)</label>
				    		<input type="text" class="s3bubble-form-input" name="aspect" id="s3aspect">
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="lightbox-text">Lightbox link text: <a class="s3bubble-pull-right" href="https://s3bubble.com/s3bubble-video-lightbox/" target="_blank">Watch Video</a></label>
				    		<input type="text" class="s3bubble-form-input" name="lightbox-text" id="lightbox-text">
				    	</div>
					</span>
					<span>
						<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="s3bubble-preview-starttime">Start time percent for preview: (leave blank to ignore)</label>
				    		<input type="text" class="s3bubble-form-input" name="s3bubble-preview-starttime" id="s3bubble-preview-starttime">
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="s3bubble-preview-finishtime">End time percent for preview: (leave blank to ignore)<a class="s3bubble-pull-right" href="https://s3bubble.com/s3bubble-video-preview-example/" target="_blank">Watch Video</a></label>
				    		<input type="text" class="s3bubble-form-input" name="s3bubble-preview-finishtime" id="s3bubble-preview-finishtime">
				    	</div>
					</span>
					<span>
						<label for="fname">Player Selection: (Only the default player supports adverts)</label>
					</span> 
					<span>
						<!--<input type="checkbox" name="s3videojs" id="s3videojs" value="true">Use Video JS <i>(Changes the player from default to video js player - best option for HLS streaming)</i><br />-->
						<input type="checkbox" name="mediaelement" id="s3mediaelement" value="true">Use Media Elements JS <i>(Changes the player from default to media element js player)</i>
					</span>
	                <blockquote class="bs-callout-s3bubble"><strong>Extra options:</strong> please just select any extra options from the list below, and S3Bubble will automatically add it to the shortcode.</blockquote>
					<span>
						<input type="checkbox" name="autoplay" id="s3autoplay">Autoplay: <i>(Start Video On Page Load)</i><br />
						<input type="checkbox" name="download" id="s3download" value="true">Show Download Links <i>(Add A Download Button To The Video) - Not available for VideoJS or Media Element Players</i><br />
					</span>
					<span>
						<a href="#" id="s3bubble-mce-submit-iframe" class="s3bubble-pull-left button media-button button-primary button-large media-button-gallery">Insert Iframe</a>
						<a href="#" id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
					</span>
				</form>
		    </div>
        	<?php
        	wp_die();
		}

        /*
		* Single audio button callback
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_single_ajax(){
		    // echo the form
		    $s3bubble_access_key = get_option("s3-s3audible_username");
		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        	
		        	// Setup vars
		        	var StreamingType  = 'progressive';

                    $('#TB_ajaxContent').css({
                    	'width' : 'auto',
                    	'height' : 'auto',
                    	'padding' : '0'
                    }); 
			        var sendData = {
						AccessKey: '<?php echo $s3bubble_access_key; ?>'
					};
					$.post("<?php echo $this->endpoint; ?>main_plugin/live_buckets/", sendData, function(response) {
						if(response.error){
							$(".s3bubble-video-main-form-alerts").html("<p>Oh Snap! " + response.message + ". If you do not understand this error please contact support@s3bubble.com</p>");
						}else{
							$(".s3bubble-video-main-form-alerts").html("<p>Awesome! " + response.message + ".</p>");
							var isSingle = response.data.Single;
							var html = '<select class="form-control input-lg" tabindex="1" name="s3bucket" id="s3bucket"><option value="">Choose bucket</option>';
						    $.each(response.data.Buckets, function (i, item) {
						    	var bucket = item.Name;
						    	if(isSingle === true){
						    		html += '<option value="s3bubble.users">' + bucket + '</option>';
						    	}else{
						    		html += '<option value="' + bucket + '">' + bucket + '</option>';	
						    	}
							});
							html += '</select>';
							$('#s3bubble-buckets-shortcode').html(html);
						}
						$( "#s3bucket" ).change(function() {
						   $('#s3bubble-folders-shortcode').html('<img src="<?php echo plugins_url('/assets/js/ajax-loader.gif',__FILE__); ?>"/> loading audio files');
						   var bucket = $(this).val();
						   if(isSingle === true){
						   		bucket = $("#s3bucket option:selected").text();
						   }
						   var data = {
								AccessKey: '<?php echo $s3bubble_access_key; ?>',
								Bucket: bucket
							};
							$.post("<?php echo $this->endpoint; ?>main_plugin/audio_files/", data, function(response) {
								var html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder"><option value="">Choose audio</option>';
							    $.each(response, function (i, item) {
							    	if(isSingle === true){
										html += '<option value="' + item + '">' + item + '</option>';
									}else{
										var folder = item.Key;
								    	var ext    = folder.split('.').pop();
								    	if(ext == 'mp3' || ext === 'm4a'){
								    		html += '<option value="' + folder + '">' + folder + '</option>';
								    	}
									}
								});
								html += '</select>';
								$('#s3bubble-folders-shortcode').html(html);
						   },'json');
						});				
					},'json');
					$( "#s3bubble-streaming-type" ).change(function() {
						StreamingType = $(this).val();
					});	
					setTimeout(function(){
						$(".s3bubble-lightbox-wrap").height($("#TB_window").height());
					},500);
					$('#s3bubble-mce-submit-iframe').click(function(){

						// Setup vars
			        	var bucket       = $('#s3bucket').val();
			        	var folder       = $('#s3folder').val();
			        	var cloudfrontid = $('#s3bubble-cloudfrontid').val();
			        	if(bucket === '' || folder === ''){
			        		alert('Please select a bucket and track');
			        		return false;
			        	}
			        	if($("#s3autoplay").is(':checked')){
						    var autoplay = true;
						}else{
						    var autoplay = false;
						}
						if($("#s3download").is(':checked')){
						    var download = true;
						}else{
						    var download = false;
						}
						if($("#s3style").is(':checked')){
						    var style = 'plain';
						}else{
						    var style = 'bar';
						}
						if($("#s3preload").is(':checked')){
						    var preload = 'none';
						}else{
						    var preload = 'auto';
						}

						var data = {
							AccessKey: '<?php echo $s3bubble_access_key; ?>',
							bucket: bucket,
							key: folder,
							Distribution : cloudfrontid,
							StreamingType : StreamingType
						};

						$.post("<?php echo $this->endpoint; ?>iframe/get_code/", data, function(response) {

							if(response.error){

								alert(response.message);

							}else{
								
								var code = response.data;
						   		shortcode = '[s3bubbleAudioSingleIframe code="' + code + '" supplied="' + StreamingType + '" autoplay="' + autoplay + '" download="' + download + '" style="' + style + '" preload="' + preload + '"/]';
								tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
		                    	tb_remove();
		                    }

                    	},'json');

					});

			        $('#s3bubble-mce-submit').click(function(){
			        	var bucket       = $('#s3bucket').val();
			        	var folder       = $('#s3folder').val();
			        	var cloudfrontid = $('#s3bubble-cloudfrontid').val();
			        	if(bucket === '' || folder === ''){
			        		alert('Please select a bucket and track');
			        		return false;
			        	}
			        	if($("#s3autoplay").is(':checked')){
						    var autoplay = true;
						}else{
						    var autoplay = false;
						}
						if($("#s3download").is(':checked')){
						    var download = true;
						}else{
						    var download = false;
						}
						if($("#s3style").is(':checked')){
						    var style = 'plain';
						}else{
						    var style = 'bar';
						}
						if($("#s3preload").is(':checked')){
						    var preload = 'none';
						}else{
						    var preload = 'auto';
						}
						var shortcode = '[s3bubbleAudioSingle bucket="' + bucket + '" track="' + folder + '" autoplay="' + autoplay + '" download="' + download + '" style="' + style + '" preload="' + preload + '"/]';
						if($("#s3mediaelement").is(':checked')){
						    shortcode = '[s3bubbleMediaElementAudio bucket="' + bucket + '" track="' + folder + '" autoplay="' + autoplay + '" download="' + download + '" style="' + style + '" preload="' + preload + '"/]';
							
						} 
						if(StreamingType === 'rtmp'){
							if(cloudfrontid === ''){
								alert('To use RTMP streaming you need to specify a Cloudfront Distribution ID');
							}else{
								shortcode = '[s3bubbleRtmpAudioDefault bucket="' + bucket + '" track="' + folder + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '"  style="' + style + '" preload="' + preload + '"/]';
		                   	}
						} 
	                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
	                    tb_remove();
			        });
		        })
		    </script>
		    <div class="s3bubble-lightbox-wrap">
			    <form class="s3bubble-form-general">
			    	<div class="s3bubble-video-main-form-alerts"></div>
			    	<span>
				    	<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="fname">Your S3Bubble Buckets/Folders:</label>
				    		<span id="s3bubble-buckets-shortcode">loading buckets...</span>
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="fname">Your S3Bubble Files:</label>
				    		<span id="s3bubble-folders-shortcode">Select bucket/folder...</span>
				    	</div>
					</span>
					<span>
						<div class="s3bubble-pull-left s3bubble-width-left">
							<label for="fname">Select Streaming Type:</label>
				    		<select class="form-control input-lg" tabindex="1" name="s3bubble-streaming-type" id="s3bubble-streaming-type">
				    			<option value="progressive">Progressive</option>
				    			<option value="rtmp">Rtmp</option>
				    		</select>
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="fname">Set Cloudfront Distribution Id:</label>
				    		<input type="text" class="s3bubble-form-input" name="s3bubble-cloudfrontid" id="s3bubble-cloudfrontid">
				    	</div>
					</span>
					<input type="hidden" class="s3bubble-form-input" name="cloudfront" id="s3cloudfront">
					<blockquote class="bs-callout-s3bubble"><strong>Extra options:</strong> please just select any extra options from the list below, and S3Bubble will automatically add it to the shortcode.</blockquote>
					<span>
						<input class="s3bubble-checkbox" type="checkbox" name="autoplay" id="s3autoplay">Autoplay <i>(Start Audio On Page Load)</i><br />
						<input class="s3bubble-checkbox" type="checkbox" name="style" id="s3style" value="true">Remove Bar <i>(Remove The Info Bar Under Player)</i><br />
						<input class="s3bubble-checkbox" type="checkbox" name="preload" id="s3preload" value="true">Preload Off <i>(Prevent Track From Preloading)</i><br />
						<input class="s3bubble-checkbox" type="checkbox" name="download" id="s3download" value="true">Show Download Links <i>(Add A Download Button To The Track)</i><br />
						<input type="checkbox" name="mediaelement" id="s3mediaelement" value="true">Use Media Elements JS <i>(Changes the player from default to media element js player)</i>
					</span>
					<span>
						<a href="#" id="s3bubble-mce-submit-iframe" class="s3bubble-pull-left button media-button button-primary button-large media-button-gallery">Insert Iframe</a>
						<a href="#" id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
					</span>
				</form>
			</div>
        	<?php
        	wp_die();
		}

		/*
		* Single audio button callback
		* @author sameast
		* @none
		*/ 
		function s3bubble_live_stream_ajax(){
		    // echo the form
		    $s3bubble_access_key = get_option("s3-s3audible_username");
		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        		
                    $('#TB_ajaxContent').css({
                    	'width' : 'auto',
                    	'height' : 'auto',
                    	'padding' : '0'
                    });
                    setTimeout(function(){
						$(".s3bubble-lightbox-wrap").height($("#TB_window").height());
					},500); 
			        $('#s3bubble-mce-submit').click(function(){

			        	var stream = $('#s3bubble-live-stream-url').val();
			        	var aspect = '16:9';
						if($('#s3aspect').val() != ''){
						    aspect = $('#s3aspect').val();
						}
						if($("#s3autoplay").is(':checked')){
						    var autoplay = true;
						}else{
						    var autoplay = false;
						}
			        	
						var shortcode = '[s3bubbleLiveStreamMedia stream="' + stream + '" aspect="' + aspect + '" autoplay="' + autoplay + '"/]';
	                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
	                    tb_remove();
			        });
		        })
		    </script>
		    <div class="s3bubble-lightbox-wrap">
			    <form class="s3bubble-form-general">
			    	<span>
				    	<div>
				    		<label for="s3bubble-live-stream-url">Your Live Stream Url: <a class="s3bubble-pull-right" href="https://s3bubble.com/s3bubble-live-broadcasting-app/" target="_blank">Watch Tutorial</a></label>
				    		<input type="text" class="s3bubble-form-input" placeholder="rtmp://52.7.131.192/live/( your s3bubble username )" name="s3bubble-live-stream-url" id="s3bubble-live-stream-url">
				    	</div>
					</span>
					<span>
						<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="aspect">Aspect Ratio: (Example: 16:9 / 4:3 Default: 16:9)</label>
				    		<input type="text" class="s3bubble-form-input" name="aspect" id="s3aspect">
				    	</div>
					</span>
					<span>
						<input type="checkbox" name="autoplay" id="s3autoplay">Autoplay: <i>(Start Stream On Page Load)</i><br />
					</span> 
					<span>
						<div class="s3bubble-video-main-form-alerts">
							<p>
					    		LIVE STREAM DIRECTLY TO THIS POST! For more information on setting up a Live Stream directly from a mobile app to this post please open this link.
					    		<a href="https://s3bubble.com/s3bubble-live-broadcasting-app/" target="_blank">LIVE STREAMING TUTORIAL</a>
					    	</p>
				    	</div>
					</span>
					<span>
						<a href="#" id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
					</span>
				</form>
			</div>
        	<?php
        	wp_die();
		}
        
		/*
		* Sets up tiny mce plugins
		* @author sameast
		* @none
		*/ 
		function s3bubble_buttons() {
			if ( current_user_can( 'manage_options' ) )  {
				add_filter( 'mce_external_plugins', array( $this, 's3bubble_add_buttons' ) ); 
				add_filter( 'mce_buttons', array( $this, 's3bubble_register_buttons' ) );
			} 
		}
		
		/*
		* Adds the menu item to the tiny mce
		* @author sameast
		* @none
		*/ 
		function s3bubble_add_buttons( $plugin_array ) {
		    $plugin_array['s3bubble'] = plugins_url('/assets/js/s3bubble.video.all.tinymce.min.js',__FILE__);
		    return $plugin_array;
		}
		
		/*
		* Registers the amount of buttons
		* @author sameast
		* @none
		*/ 
		function s3bubble_register_buttons( $buttons ) {
		    array_push( $buttons, 's3bubble_live_stream_shortcode', 's3bubble_audio_single_shortcode', 's3bubble_audio_playlist_shortcode', 's3bubble_video_single_shortcode', 's3bubble_video_playlist_shortcode' ); 
		    return $buttons;
		}

		/*
		* Cleans the options removing white space etc...
		* @author sameast
		* @none
		*/ 
		function s3bubble_clean_options( $val ) {
		    return trim(stripslashes(wp_filter_post_kses(addslashes($val))));
		}

		/*
		* Gets the domain name without ip
		* @author sameast
		* @none
		*/ 
		function get_domain($url){

		  $pieces = parse_url($url);
		  $domain = isset($pieces['host']) ? $pieces['host'] : '';
		  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
		    return $regs['domain'];
		  }
		  return false;

		}

        function check_user_agent ( $type = NULL ) {
		        $user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );
		        if ( $type == 'bot' ) {
		                // matches popular bots
		                if ( preg_match ( "/googlebot|adsbot|yahooseeker|yahoobot|msnbot|watchmouse|pingdom\.com|feedfetcher-google/", $user_agent ) ) {
		                        return true;
		                        // watchmouse|pingdom\.com are "uptime services"
		                }
		        } else if ( $type == 'browser' ) {
		                // matches core browser types
		                if ( preg_match ( "/mozilla\/|opera\//", $user_agent ) ) {
		                        return true;
		                }
		        } else if ( $type == 'mobile' ) {
		                // matches popular mobile devices that have small screens and/or touch inputs
		                // mobile devices have regional trends; some of these will have varying popularity in Europe, Asia, and America
		                // detailed demographics are unknown, and South America, the Pacific Islands, and Africa trends might not be represented, here
		                if ( preg_match ( "/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $user_agent ) ) {
		                        // these are the most common
		                        return true;
		                } else if ( preg_match ( "/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $user_agent ) ) {
		                        // these are less common, and might not be worth checking
		                        return true;
		                }
		        }
		        return false;
		}

		/*
		* Outputs the s3bubble analytics
		* @author sameast
		* @none
		*/ 
		function s3bubble_output_analytics($atts){

			extract( shortcode_atts( array(
				'aspect'     => '16:9',
				'autoplay'   => 'false',
				'code'       => '',
				'supplied'   => 'video'
			), $atts, 's3bubbleVideoSingleIframe' ) );

            $autoplay  = ((empty($autoplay) || $autoplay == 'false') ? 'no' : 'autoplay');
			$code      = ((empty($code)) ? false : $code);
			$aspect    = ((empty($aspect)) ? false : $aspect);
			$supplied  = ((empty($supplied) || $supplied == 'progressive') ? 'video' : $supplied);

			return '<div class="s3bubble-output-analytics"></div>';

		}

		// -------------------------- IFRAME PLAYERS SETUPS BELOW ------------------------------ //

		/*
		* Run the s3bubble single player iframe code
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_single_player_iframe($atts){

			extract( shortcode_atts( array(
				'aspect'     => '16:9',
				'autoplay'   => 'false',
				'code'       => '',
				'supplied'   => 'video'
			), $atts, 's3bubbleVideoSingleIframe' ) );

            $autoplay  = ((empty($autoplay) || $autoplay == 'false') ? 'no' : 'autoplay');
			$code      = ((empty($code)) ? false : $code);
			$aspect    = ((empty($aspect)) ? false : $aspect);
			$supplied  = ((empty($supplied) || $supplied == 'progressive') ? 'video' : $supplied);

			return '<iframe style="width:100%;min-height:300px;" onload="this.height=(this.offsetWidth/16)*9;" src="//media.s3bubble.com/' . $supplied . '/' . $code . ':' . $autoplay . '" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" allowtransparency="true" webkitAllowFullScreen="true" mozallowfullscreen="true" allowFullScreen="true"></iframe>';

		}

		/*
		* Run the s3bubble single player iframe code
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_single_player_iframe($atts){

			extract( shortcode_atts( array(
				'aspect'     => '16:9',
				'autoplay'   => 'false',
				'code'       => '',
				'supplied'   => 'audio',
				'style'      => 'bar'
			), $atts, 's3bubbleAudioSingleIframe' ) );

            $autoplay  = ((empty($autoplay) || $autoplay == 'false') ? 'no' : 'autoplay');
			$code      = ((empty($code)) ? false : $code);
			$aspect    = ((empty($aspect)) ? false : $aspect);
			$supplied  = ((empty($supplied) || $supplied == 'progressive') ? 'audio' : $supplied);
			$style     = ((empty($style) || $style == 'bar') ? 75 : 35);

			return '<iframe style="width:100%;height:' . $style . 'px;" src="//media.s3bubble.com/' . $supplied . '/' . $code . ':' . $autoplay . '" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" allowtransparency="true" webkitAllowFullScreen="true" mozallowfullscreen="true" allowFullScreen="true"></iframe>';

		}

		// -------------------------- LIVE STREAM PLAYERS SETUPS BELOW ------------------------------ //

		/*
		* Run the video js supports Live Streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_live_stream_video($atts){
	   	
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");

	        extract( shortcode_atts( array(
				'aspect'     => '16:9',
				'autoplay'   => 'false',
				'stream'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleLiveStream' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			
			$player_id = uniqid();

			if(empty($stream)){
				echo "No live steam url has been set";
			}else{

				return '<div class="video-wrap-' . $player_id . '" style="width:100%;position:relative;">
					<video id="video-' . $player_id . '" class="video-js vjs-default-skin" controls preload="none" width="640" 
					      poster="https://s3.amazonaws.com/s3bubble.assets/video.player/placeholder.png"
					      data-setup=\'{"techOrder": ["flash"]}\'>
					    <source src="' . $stream . '" type="video/mp4" />
					</video>
				</div>
				<script>
					jQuery(document).ready(function($) {
						videojs.options.flash.swf = "https://s3.amazonaws.com/s3bubble.assets/videojs/video-js.swf";
						var Current = -1;
						var video_width = $(".video-wrap-' . $player_id . '").width();
						var aspects  = "' . $aspect . '";
						var aspects = aspects.split(":");
						var aspect = video_width/aspects[0]*aspects[1];
						var video = document.getElementById("video-' . $player_id . '");
						video.height = Math.round(aspect);
						video.width = video_width;
					});
				</script>';

			}

			
			
        }

        /*
		* WORKING HERE LIVE STREAM - Main HLS and RTMP Live Streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_live_stream_media_element_video($atts){
	   	
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");

	        extract( shortcode_atts( array(
				'aspect'     => '16:9',
				'autoplay'   => 'false',
				'comments' => 'false',
				'stream'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleLiveStreamMedia' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$comments = ((empty($comments)) ? 'false' : $comments);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			
			$player_id = uniqid();

			//Split the key
			$path_parts = pathinfo($stream);

			if(isset($path_parts['extension']) && $path_parts['extension'] == 'm3u8'){

				// Setup secure url
				$secret = 'secret'; // To make the hash more difficult to reproduce.
				$path   = '/hls/' . $path_parts['filename'] . '.m3u8'; // This is the file to send to the user.
				$expire = time() + 3600; // At which point in time the file should expire. time() + x; would be the usual usage.
				$md5 = base64_encode(md5($secret .  $path . $expire , true)); // Using binary hashing.
				$md5 = strtr($md5, '+/', '-_'); // + and / are considered special characters in URLs, see the wikipedia page linked in references.
				$md5 = str_replace('=', '', $md5); // When used in query parameters the base64 padding character is considered special.
				$url = $stream . '?st=' . $md5 . '&e=' . time();

			}else{

				$url = $stream;

			}

			$post_thumbnail_id = get_post_thumbnail_id( get_the_ID() );
			$large_image_url = wp_get_attachment_image_src( $post_thumbnail_id, 'single-video-poster' );
			$poster = "https://s3.amazonaws.com/s3bubble.assets/video.player/placeholder.png";
			if(is_array($large_image_url)){
				$poster = $large_image_url[0];
			}
			
			$commentsOutput = '';
			if($comments == 'facebook'){
				$commentsOutput = '<div id="fb-root"></div><script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=803844463017959";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>
							<div class="fb-comments" data-href="' . get_permalink( get_the_ID() ) . '" data-num-posts="5"></div>';
			}

			if(empty($stream)){
				echo "No live steam url has been set";
			}else{

				return '<div class="video-wrap-' . $player_id . '" style="width:100%;overflow:hidden;">
							<video id="video-' . $player_id . '" style="width:100%;" controls="controls" preload="none">
								<source type="application/x-mpegURL" src="' . $url . '" />
							</video>
							' . $commentsOutput . ' 
						</div>
						<script>
							jQuery(document).ready(function($) {
								// Setup Aspect Ratio
								var aspects  = "' . $aspect . '";
								var video_width = $(".video-wrap-' . $player_id . '").width();
								var aspects = aspects.split(":");
								var aspect = video_width/aspects[0]*aspects[1];
								var video = document.getElementById("video-' . $player_id . '");
								video.height = Math.round(aspect);
								video.width = video_width;
								$("#video-' . $player_id . '").mediaelementplayer({
					    			poster: "' . $poster . '",
					    			videoWidth: "100%",
									videoHeight: "100%",
									enableAutosize: true,
									plugins: ["flash"],
									features: ["playpause","volume","fullscreen"],
									pluginPath: "' . plugins_url('assets/mediaelementjs/build/',__FILE__ ) . '",
									flashName: "flashmediaelement.swf",
					    			success: function(mediaElement, node, player) {
					    				$(".video-wrap-' . $player_id . ' .mejs-fullscreen-button").css("float","right");
								        mediaElement.addEventListener("timeupdate", function(e) {
								            if(e.currentTime){
								            	
								            }
								        }, false);
										'. (($autoplay == 'true') ? 'mediaElement.play();' : '') . '
							     	}
				    			});
							});
						</script>';

			}
	
        }

       // -------------------------- HLS PLAYERS SETUPS BELOW ------------------------------ //

       /*
		* Run the media element video supports HLS streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_hls_video($atts){
	   	
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");	

	        extract( shortcode_atts( array(
	        	'aspect'     => '16:9',
	        	'autoplay'   => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleHlsVideo' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
            
            //set POST variables
			$url = $this->endpoint . 'main_plugin/single_video_hls';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $bucket,
			    'Key' => $track,
			    'Cloudfront' => $cloudfront
			);

			if(!function_exists('curl_init')){
    			echo json_encode(array("error" => "<i>Your hosting does not have PHP curl installed. Please install php curl S3Bubble requires PHP curl to work!</i>"));
    			exit();
    		}
			
			//open connection
			$ch = curl_init();
			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			//execute post
		    $result = curl_exec($ch);
			$track = json_decode($result, true);
			curl_close($ch);

			if(!empty($track['error'])){
				echo $track['error'];
			}else{
				$player_id = uniqid();
				if(is_array($track)){
					$source = '<source type="application/x-mpegURL" src="' . $track[0]['m3u8'] . '" />';
					$url    = $track[0]['m3u8'];
					$ismobile = $this->check_user_agent('mobile');
					if($ismobile) {
						$source = '<source type="video/mp4" src="' . $track[0]['m4v'] . '" />';
						$url    = $track[0]['m4v'];
					}
					return '<div class="video-wrap-' . $player_id . '" style="width:100%;overflow:hidden;">
							<video id="video-' . $player_id . '" style="width:100%;" controls="controls" preload="none">
							' . $source . '
							</video>
						</div>
						<script>
							jQuery(document).ready(function($) {
								var Bucket = "' . $bucket . '";
								var Key = "' . $track[0]['key'] . '";
								var Current = -1;
								// Setup Aspect Ratio
								var aspects  = "' . $aspect . '";
								var video_width = $(".video-wrap-' . $player_id . '").width();
								var aspects = aspects.split(":");
								var aspect = video_width/aspects[0]*aspects[1];
								var video = document.getElementById("video-' . $player_id . '");
								video.height = Math.round(aspect);
								video.width = video_width;
								$("#video-' . $player_id . '").mediaelementplayer({
					    			poster: "' . $track[0]['poster'] . '",
					    			videoVolume: "horizontal",
									enableAutosize: true,
									features: ["playpause","current","progress","duration","tracks","volume","fullscreen"],
									plugins: ["flash"],
									pluginPath: "' . plugins_url('assets/mediaelementjs/build/',__FILE__ ) . '",
									flashName: "flashmediaelement.swf",
					    			success: function(mediaElement, node, player) {
					    				'. (($autoplay == 'true') ? 'mediaElement.play();' : '') . '
					    				// add event listener
								        mediaElement.addEventListener("play", function(e) {
								            if(Current < 0){
												addListener({
													app_id: s3bubble_all_object.s3appid,
													server: s3bubble_all_object.serveraddress,
													bucket: Bucket,
													key: Key,
													type: "video",
													advert: false
												});
												Current = 1;
											}
								        }, false);
										$("video").bind("contextmenu", function(e) {
											return false
										});
							     	}
				    			});
							});
						</script>';
				}
			}
			
			
       }

       /*
		* Run the media element video supports HLS streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_hls_video_js($atts){
	   	
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");	
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$stream              = get_option("s3-stream");

	        extract( shortcode_atts( array(
	        	'aspect'     => '16:9',
	        	'autoplay'   => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleHlsVideo' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);

			//set POST variables
			$url = $this->endpoint . 'main_plugin/single_video_hls';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $bucket,
			    'Key' => $track,
			    'Cloudfront' => $cloudfront
			);

			if(!function_exists('curl_init')){
    			echo json_encode(array("error" => "<i>Your hosting does not have PHP curl installed. Please install php curl S3Bubble requires PHP curl to work!</i>"));
    			exit();
    		}
			
			//open connection
			$ch = curl_init();
			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			//execute post
		    $result = curl_exec($ch);
			$track = json_decode($result, true);
			curl_close($ch);

			if(!empty($track['error'])){
				echo $track['error'];
			}else{
				$player_id = uniqid();
				if(is_array($track)){
					$source = '<source type="video/mp4" src="' . $track[0]['m3u8'] . '" />';
					$ismobile = $this->check_user_agent('mobile');
					if($ismobile) {
						$source = '<source type="video/mp4" src="' . $track[0]['m4v'] . '" />';
					}
					return '<div class="video-wrap-' . $player_id . '" style="width:100%;overflow:hidden;">
								<video id="video-' . $player_id . '" class="video-js vjs-default-skin" controls width="640">
								    ' . $source . '
								</video>
							</div>
							<script>
								jQuery(document).ready(function($) {
									videojs.options.flash.swf = "https://s3.amazonaws.com/s3bubble.assets/videojs/video-js.swf";
									var Bucket = "' . $bucket . '";
									var Key = "' . $track[0]['key'] . '";
									var Current = -1;
									var video_width = $(".video-wrap-' . $player_id . '").width();
									var aspects  = "' . $aspect . '";
									var aspects = aspects.split(":");
									var aspect = video_width/aspects[0]*aspects[1];
									var video = document.getElementById("video-' . $player_id . '");
									video.height = Math.round(aspect);
									video.width = video_width;
									videojs("video-' . $player_id . '", {
										"preload" : "auto",
										"techOrder": ["flash"],
										"poster": "' . $track[0]['poster'] . '",
										"autoplay": '. (($autoplay == 'true') ? "true" : "false") . '
									}, function(){
										this.on("play", function() { 
								          	if(Current < 0){
												addListener({
													app_id: s3bubble_all_object.s3appid,
													server: s3bubble_all_object.serveraddress,
													bucket: Bucket,
													key: Key,
													type: "video",
													advert: false
												});
												Current = 1;
											}
								        });
									});  
								});
							</script>';
				}
			}
			
			
       }

       // -------------------------- RTMP SETUPS BELOW ------------------------------ //

       /*
		* Run the jplayer supports RTMP streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_rtmp_video_default($atts){
	   		
	   		//Run a S3Bubble security check
			$ajax_nonce = wp_create_nonce( "s3bubble-nonce-security" );

			// get option from database	
			$responsive          = get_option("s3-responsive");

	        extract( shortcode_atts( array(
	        	'download'   => 'false',
	        	'twitter' => 'false',
	        	'twitter_handler' => '@s3bubble',
	        	'twitter_text' => 'Shared via s3bubble.com media streaming',
				'aspect'     => '16:9',
				'responsive' => $responsive,
				'autoplay'   => 'false',
				'start'      => 'false',
				'finish'     => 'false',
				'disable_skip' => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3bubbleRtmpVideoDefault' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$disable_skip = ((empty($disable_skip)) ? 'false' : $disable_skip);
			$download = ((empty($download)) ? 'false' : $download);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$start = ((empty($start)) ? 'false' : $start);
			$finish = ((empty($finish)) ? 'false' : $finish);

            $player_id = uniqid();

            return '<div class="single-video-' . $player_id . '"></div>
            <script type="text/javascript">
            	window.onbeforeunload = confirmExit;
			    function confirmExit(){
			    	addListener(window.s3bubbleAnalytics);
			    }
				jQuery(document).ready(function($) {
					$(".single-video-' . $player_id . '").singleVideo({
						Ajax:       "' . admin_url('admin-ajax.php') . '",
						ApiCall:    "s3bubble_video_rtmp_internal_ajax",
						Flash:      "https://s3.amazonaws.com/s3bubble.assets/flash/s3bubble.rtmp.swf",
						Pid:		"' . $player_id . '",
						Bucket:		"' . $bucket . '",
						Key:		"' . $track . '",
						Cloudfront:	"' . $cloudfront . '",
						Supplied:   "rtmpv",
						Security:	"' . $ajax_nonce . '",
						AutoPlay:	' . $autoplay . ',
						Download:	' . $download . ',
						Aspect:	    "' . $aspect . '",
						DisableSkip:"' . $disable_skip . '",
						Twitter:    "' . $twitter . '",
						TwitterText:    "' . $twitter_text . '",
						TwitterHandler:	"' . $twitter_handler . '",
						Start:      "' . $start . '",
						Finish:	    "' . $finish . '"
					},function(){
						
					});
				});
			</script>';

       }

       /*
		* Run the jplayer supports RTMP streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_rtmp_audio_default($atts){

	   		//Run a S3Bubble security check
			$ajax_nonce = wp_create_nonce( "s3bubble-nonce-security" );
	
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");

			 extract( shortcode_atts( array(
				'style'      => 'bar',
				'download'   => 'false',
				'autoplay'   => 'false',
				'preload'    => 'auto',
				'bucket'     => '',
				'track'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleRtmpAudioDefault' ) );
			extract( shortcode_atts( array(
				'style'      => 'bar',
				'download'   => 'false',
				'autoplay'   => 'false',
				'preload'    => 'auto',
				'bucket'     => '',
				'track'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleRtmpAudioDefault' ) );

			$style    = ((empty($style)) ? 'bar' : $style);
			$download = ((empty($download)) ? 'false' : $download);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$preload  = ((empty($preload)) ? 'auto' : $preload);
			
			// Check download
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = 1;
				}else{
					if($download == 'true'){
						$download = 1;
					}else{
						$download = 0;
					}
				}
			}
            $player_id = uniqid();

            return '<div id="s3bubble-media-main-container-' . $player_id .  '" class="s3bubble-media-main-audio">
			    <div id="jquery_jplayer_rtmp_audio_' . $player_id .  '" class="s3bubble-media-main-jplayer"></div>
				    <div class="s3bubble-media-main-gui">
				        <div class="s3bubble-media-main-interface s3bubble-media-main-interface-audio-playlist">
				        	<div class="s3bubble-media-main-audio-loading">
						    	<i class="s3icon s3icon-circle-o-notch s3icon-spin"></i>
						    </div>
				            <div class="s3bubble-media-main-controls-holder" style="display:none;">
					            <div class="s3bubble-media-main-left-controls">
									<a href="javascript:;" class="s3bubble-media-main-play" tabindex="1"><i class="s3icon s3icon-play"></i></a>
									<a href="javascript:;" class="s3bubble-media-main-pause" tabindex="1"><i class="s3icon s3icon-pause"></i></a>
								</div>
								<div class="s3bubble-media-main-progress" dir="auto">
								    <div class="s3bubble-media-main-seek-bar" dir="auto">
								        <div class="s3bubble-media-main-play-bar" dir="auto"></div>
								    </div>
								</div>
								<div class="s3bubble-media-main-right-controls">
									<div class="s3bubble-media-main-volume-bar" dir="auto">
									    <div class="s3bubble-media-main-volume-bar-value" dir="auto"></div>
									</div>
									<a href="javascript:;" class="s3bubble-media-main-mute" tabindex="2" title="mute"><i class="s3icon s3icon-volume-up"></i></a>
									<a href="javascript:;" class="s3bubble-media-main-unmute" tabindex="2" title="unmute"><i class="s3icon s3icon-volume-off"></i></a>
									<div class="s3bubble-media-main-time-container">
										<div class="s3bubble-media-main-duration"></div>
									</div>
								</div>
				            </div>
				        </div>
				    </div>
				    <div class="s3bubble-media-main-playlist">
						<ul>
							<li></li>
						</ul>
					</div>
				    <div class="s3bubble-media-main-no-solution" style="display:none;">
				        <span>Update Required</span>
				        Flash player is needed to use this player please download here. <a href="https://get2.adobe.com/flashplayer/" target="_blank">Download</a>
				    </div>
				</div>
				<script type="text/javascript">
				jQuery(document).ready(function( $ ) {
					
					// Set aspect ratio
					var Bucket     = "' . $bucket . '";
					var Key        = "' . $track . '";
					var Cloudfront = "' . $cloudfront . '";
					var Current    = -1;
					var IsMobile   = false;
					if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
						IsMobile = true;
					}

					var s3bubbleAudioRtmpPlaylist = new jPlayerPlaylist({
						jPlayer: "#jquery_jplayer_rtmp_audio_' . $player_id . '",
						cssSelectorAncestor: "#s3bubble-media-main-container-' . $player_id . '"
					}, s3bubbleAudioRtmpPlaylist, {
						playlistOptions: {
		                    displayTime: 0,
		                    playerWidth: $(this).width(),
		                    enableRemoveControls: false
		                },
						ready : function(event) {
							var sendData = {
								action : "s3bubble_audio_rtmp_internal_ajax",
								security : "' . $ajax_nonce . '",
								Timezone :"America/New_York",
							    Bucket : Bucket,
							    Key : Key,
							    Cloudfront : Cloudfront,
							    IsMobile : IsMobile
							}
							$.post("' . admin_url('admin-ajax.php') . '", sendData, function(response) {

								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-audio-loading").fadeOut();
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-controls-holder").fadeIn();

								if(response.error){
									$("#s3bubble-media-main-container-' . $player_id . '").append("<span class=\"s3bubble-alert\"><p>" + response.message + ". <a href=\"https://s3bubble.com/video_tutorials/starter-setting-up-rtmp-streaming/\" target=\"_blank\">Watch Video</a></p></span>");
									console.log(response.message);
								}else{

									s3bubbleAudioRtmpPlaylist.setPlaylist(response.results);
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 200px 0 40px");
									if(IsMobile){
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 60px 0 40px");	
									}
									if(response.user === "s2member_level1" || response.user === "s2member_level2"){
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 240px 0 40px");
										if(IsMobile){
											$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 100px 0 40px");	
										}
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-right-controls").prepend("<a href=\"https://s3bubble.com/?brand=plugin\" class=\"s3bubble-media-main-logo\"><i id=\"icon-S3\" class=\"icon-S3\"></i></a>");
									}
									//Make it plain
									if ("' . $style . '" === "plain") {
										$("#s3bubble-media-main-container-' . $player_id . '").css({
											overflow : "hidden",
											height : "35px"
										})
									}

								}
							},"json");

						},
						loadedmetadata: function (event) {
							
					    },
					    resize: function (event) {

					    },
					    click: function (event) {

					    },
					    error: function (event) {

					    },
					    warning: function (event) {

					    },
					    loadstart: function (event) {

					    },
					    progress: function (event) {

					    },
					    play: function (event) {
							
					    },
					    ended : function(event) {
							console.log("ended");
						},
					    timeupdate : function(event) {

						},
						swfPath: "https://s3.amazonaws.com/s3bubble.assets/flash/s3bubble.audio.rtmp.swf",
				        supplied: ((IsMobile) ? "mp3,wav,m4a" : "rtmpa"),
						useStateClassSkin: true,
						autoBlur: false,
						smoothPlayBar: false,
						keyEnabled: true,
						audioFullScreen: true,
						remainingDuration: true
					});
				});
				</script>';

       }

       /*
		* Run the media element video supports RTMP streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_rtmp_video($atts){
	   	
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");	
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$stream              = get_option("s3-stream");
	        extract( shortcode_atts( array(
	        	'aspect'     => '16:9',
	        	'autoplay'   => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleRtmpVideo' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);

			//set POST variables
			$url = $this->endpoint . 'main_plugin/single_video_rtmp';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $bucket,
			    'Key' => $track,
			    'Cloudfront' => $cloudfront
			);

			if(!function_exists('curl_init')){
    			echo json_encode(array("error" => "<i>Your hosting does not have PHP curl installed. Please install php curl S3Bubble requires PHP curl to work!</i>"));
    			exit();
    		}
			
			//open connection
			$ch = curl_init();
			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			//execute post
		    $result = curl_exec($ch);
			$track = json_decode($result, true);
			curl_close($ch);

			if(!empty($track['error'])){
				echo $track['error'];
			}else{
				$player_id = uniqid();
				if(is_array($track)){
					$end = explode('mp4:', $track[0]['video']);
					$source = '<source type="video/rtmp" src="mp4:' . $end[1] .'" />';
					$ismobile = $this->check_user_agent('mobile');
					if($ismobile) {
						$source = '<source type="video/mp4" src="' . $track[0]['m4v'] . '" />';
					}
					return '<div class="video-wrap-' . $player_id . '" style="width:100%;">
								<video id="video-' . $player_id . '" style="width:100%;" controls="controls"> 
									' . $source . '
								</video>
							</div>
							<script>
								jQuery(document).ready(function($) {
									var Bucket = "' . $bucket . '";
									var Key = "' . $track[0]['key'] . '";
									var Current = -1;
									var video_width = $(".video-wrap-' . $player_id . '").width();
									var aspects  = "' . $aspect . '";
									var aspects = aspects.split(":");
									var aspect = video_width/aspects[0]*aspects[1];
									var video = document.getElementById("video-' . $player_id . '");
									video.height = Math.round(aspect);
									video.width = video_width;

									$("#video-' . $player_id . '").mediaelementplayer({
										flashStreamer:"' . $track[0]['video'] . '",
										plugins: ["flash", "silverlight"],
						    			poster: "' . $track[0]['poster'] . '",
						    			videoVolume: "horizontal",
										enableAutosize: true,
										features: ["playpause","current","progress","duration","tracks","volume","fullscreen"],
										pluginPath: "' . plugins_url('assets/mediaelementjs/build/',__FILE__ ) . '",
										flashName: "flashmediaelement.swf",
						    			success: function(mediaElement, node, player) {
						    				'. (($autoplay == 'true') ? 'mediaElement.play();' : '') . '
						    				// add event listener
									        mediaElement.addEventListener("play", function(e) {
									            if(Current < 0){
													addListener({
														app_id: s3bubble_all_object.s3appid,
														server: s3bubble_all_object.serveraddress,
														bucket: Bucket,
														key: Key,
														type: "video",
														advert: false
													});
													Current = 1;
												}
									        }, false);
											$("video").bind("contextmenu", function(e) {
												return false
											});
								     	}
					    			});
								});
							</script>';
				}
			}
			
			
       }

       /*
		* Run the VIDEO JS video supports RTMP streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_rtmp_video_js($atts){
	   	
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");	
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$stream              = get_option("s3-stream");
	        extract( shortcode_atts( array(
	        	'aspect'     => '16:9',
	        	'autoplay'   => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleRtmpVideoJs' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);

			//set POST variables
			$url = $this->endpoint . 'main_plugin/single_video_rtmp';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $bucket,
			    'Key' => $track,
			    'Cloudfront' => $cloudfront
			);

			if(!function_exists('curl_init')){
    			echo json_encode(array("error" => "<i>Your hosting does not have PHP curl installed. Please install php curl S3Bubble requires PHP curl to work!</i>"));
    			exit();
    		}
			
			//open connection
			$ch = curl_init();
			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			//execute post
		    $result = curl_exec($ch);
			$track = json_decode($result, true);
			curl_close($ch);

			if(!empty($track['error'])){
				echo $track['error'];
			}else{
				$player_id = uniqid();
				if(is_array($track)){
					$end = explode('mp4:', $track[0]['video']);
					$source = '<source src="' . $end[0] . '&mp4:' . $end[1] . '" type="rtmp/mp4" />';
					$ismobile = $this->check_user_agent('mobile');
					if($ismobile) {
						$source = '<source type="video/mp4" src="' . $track[0]['m4v'] . '" />';
					}
					return '<div class="video-wrap-' . $player_id . '" style="width:100%;position:relative;">
								<video id="video-' . $player_id . '" class="video-js vjs-default-skin" controls width="640">
								    ' . $source . '
								</video>
							</div>
							<script>
								jQuery(document).ready(function($) {
									videojs.options.flash.swf = "https://s3.amazonaws.com/s3bubble.assets/videojs/video-rtmp.swf";
									var Current = -1;
									var video_width = $(".video-wrap-' . $player_id . '").width();
									var aspects  = "' . $aspect . '";
									var aspects = aspects.split(":");
									var aspect = video_width/aspects[0]*aspects[1];
									var video = document.getElementById("video-' . $player_id . '");
									video.height = Math.round(aspect);
									video.width = video_width;
									videojs("video-' . $player_id . '", {
										"preload" : "auto",
										"techOrder": ["flash"],
										"poster": "' . $track[0]['poster'] . '",
										"autoplay": '. (($autoplay == 'true') ? "true" : "false") . '
									}, function(){
										this.on("play", function() { 
								          	if(Current < 0){
												addListener({
													app_id: s3bubble_all_object.s3appid,
													server: s3bubble_all_object.serveraddress,
													bucket: Bucket,
													key: Key,
													type: "video",
													advert: false
												});
												Current = 1;
											}
								        });
									}); 
								});
							</script>';
				}
			}
			
			
       }

       // ------------------------------ PROGRESSIVE PLAYERS BELOW --------------------------- //

       /*
		* Run the media element video supports VIDEO JS streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_video_single_player_videojs($atts){
	   	
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");	

	        extract( shortcode_atts( array(
	        	'aspect'     => '16:9',
	        	'autoplay'   => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleVideoSingleJs' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);

			//set POST variables
			$url = $this->endpoint . 'main_plugin/single_video_videojs';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $bucket,
			    'Key' => $track,
			    'Cloudfront' => $cloudfront,
			    'Server' => $_SERVER['REMOTE_ADDR']
			);

			if(!function_exists('curl_init')){
    			echo json_encode(array("error" => "<i>Your hosting does not have PHP curl installed. Please install php curl S3Bubble requires PHP curl to work!</i>"));
    			exit();
    		}
			
			//open connection
			$ch = curl_init();
			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			//execute post
		    $result = curl_exec($ch);
			$track = json_decode($result, true);
			curl_close($ch);

			if(!empty($track['error'])){
				echo $track['error'];
			}else{
				$player_id = uniqid();
				if(is_array($track)){

					//Setup vars
					$video    = $track['results'][0]['m4v'];
					$poster   = $track['results'][0]['poster'];

					return '<div class="video-wrap-' . $player_id . '" style="width:100%;position:relative;">
								<video id="video-' . $player_id . '" class="video-js vjs-default-skin" controls '. (($autoplay == 'true') ? 'autoplay' : '') . ' preload="none" width="640" 
								      poster="' . $poster . '"
								      data-setup="{}">
								    <source type="video/mp4" src="' . $video . '" />
								</video>
							</div>
							<script>
								jQuery(document).ready(function($) {
									var Current = -1;
									var video_width = $(".video-wrap-' . $player_id . '").width();
									var aspects  = "' . $aspect . '";
									var aspects = aspects.split(":");
									var aspect = video_width/aspects[0]*aspects[1];
									var video = document.getElementById("video-' . $player_id . '");
									video.height = Math.round(aspect);
									video.width = video_width;
									$("video").bind("contextmenu", function(e) {
										return false
									});
								});
							</script>';
				}
			}
			
			
       }
	   
	   /*
		* Run the media element video supports RTMP streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_media_element_video($atts){
	   	
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");	

	        extract( shortcode_atts( array(
	        	'aspect'     => '16:9',
	        	'autoplay'   => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleMediaElementVideo' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);

			//set POST variables
			$url = $this->endpoint . 'main_plugin/single_video_media_element';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $bucket,
			    'Key' => $track,
			    'Cloudfront' => $cloudfront,
			    'Server' => $_SERVER['REMOTE_ADDR']
			);

			if(!function_exists('curl_init')){
    			echo json_encode(array("error" => "<i>Your hosting does not have PHP curl installed. Please install php curl S3Bubble requires PHP curl to work!</i>"));
    			exit();
    		}
			
			//open connection
			$ch = curl_init();
			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			//execute post
		    $result = curl_exec($ch);
			$track = json_decode($result, true);
			curl_close($ch);

			if(!empty($track['error'])){
				echo $track['error'];
			}else{
				
				// Setup vars
				$player_id = uniqid();
				$video     = $track[0]['m4v'];
				$key       = $track[0]['key'];
				$poster    = $track[0]['poster'];
				
				if(is_array($track)){
					return '<div class="video-wrap-' . $player_id . '" style="width:100%;overflow:hidden;">
								<video id="video-' . $player_id . '" controls="controls" style="width:100%;">
									<source type="video/mp4" src="' . $video . '" />
								</video>
							</div>
						<script>
							jQuery(document).ready(function($) {
								var Bucket = "' . $bucket . '";
								var Key = "' . $key . '";
								var Current = -1;
								var video_width = $(".video-wrap-' . $player_id . '").width();
								var aspects  = "' . $aspect . '";
								var aspects = aspects.split(":");
								var aspect = video_width/aspects[0]*aspects[1];
								var video = document.getElementById("video-' . $player_id . '");
								video.height = Math.round(aspect);
								video.width = video_width;
								$("#video-' . $player_id . '").mediaelementplayer({
					    			poster: "' . $poster . '",
					    			videoVolume: "horizontal",
					    			enableAutosize: true,
					    			features: ["playpause","current","progress","duration","tracks","volume","fullscreen"],
					    			plugins: ["flash"], 
									pluginPath: "' . plugins_url('assets/mediaelementjs/build/',__FILE__ ) . '",
									flashName: "flashmediaelement.swf",
					    			success: function(mediaElement, node, player) {
					    				'. (($autoplay == 'true') ? 'mediaElement.play();' : '') . '
					    				// add event listener
								        mediaElement.addEventListener("play", function(e) {
								            if(Current < 0){
												addListener({
													app_id: s3bubble_all_object.s3appid,
													server: s3bubble_all_object.serveraddress,
													bucket: Bucket,
													key: Key,
													type: "video",
													advert: false
												});
												Current = 1;
											}
								        }, false);
										$("video").bind("contextmenu", function(e) {
											return false
										});
							     	}
				    			});
							});
						</script>';
				}
			}
			
			
       }
       
	   /*
		* Run the media element audio does not currently supports RTMP streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_media_element_audio($atts){
	   	
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");		
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$stream              = get_option("s3-stream");
	        extract( shortcode_atts( array(
	        	'autoplay'   => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleMediaElementAudio' ) );

			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);

			//set POST variables
			$url = $this->endpoint . 'main_plugin/single_audio_media_element';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $bucket,
			    'Key' => $track
			);

			if(!function_exists('curl_init')){
    			echo json_encode(array("error" => "<i>Your hosting does not have PHP curl installed. Please install php curl S3Bubble requires PHP curl to work!</i>"));
    			exit();
    		}
			
			//open connection
			$ch = curl_init();
			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			//execute post
		    $result = curl_exec($ch);
			$track = json_decode($result, true);
			curl_close($ch);

			if(!empty($track['error'])){
				echo $track['error'];
			}else{
				$player_id = uniqid();
				if(is_array($track)){
					if($cloudfront != ''){
				    	return '<p>rtmp only currently support for video</p>';
				    }else{
						return '<audio width="100%" src="' . $track[0]['mp3'] . '" id="audio-' . $player_id . '"></audio>
								<script>
									jQuery(document).ready(function($) {
										var Bucket = "' . $bucket . '";
										var Key = "' . $track[0]['key'] . '";
										var Current = -1;
										$("#audio-' . $player_id . '").mediaelementplayer({
											videoVolume: "horizontal",
							    			features: ["playpause","current","progress","duration","tracks","volume","fullscreen"],
							    			plugins: ["flash"],
											pluginPath: "' . plugins_url('assets/mediaelementjs/build/',__FILE__ ) . '",
											flashName: "flashmediaelement.swf",
							    			success: function(mediaElement, node, player) {
							    				'. (($autoplay == 'true') ? 'mediaElement.play();' : '') . '
							    				// add event listener
										        mediaElement.addEventListener("play", function(e) {
										            if(Current < 0){
														addListener({
															app_id: s3bubble_all_object.s3appid,
															server: s3bubble_all_object.serveraddress,
															bucket: Bucket,
															key: Key,
															type: "audio",
															advert: false
														});
														Current = 1;
													}
										        }, false);
									     	}
						    			});
									});
								</script>';
					}
				}
			}
			
       }
	   
	    /*
		* Run the s3bubble jplayer playlist function
		* @author sameast
		* @none
		*/ 
	   function s3bubble_audio_player($atts){
	   	  	
	   	  	//Run a S3Bubble security check
			$ajax_nonce = wp_create_nonce( "s3bubble-nonce-security" );

			/*
			 * player options
			 */ 		
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$s3bubble_force_download = get_option("s3bubble_force_download");

	        extract( shortcode_atts( array(
				'playlist'   => 'show',
				'order'      => 'asc',
				'download'   => 'false',
				'search'     => $search,
				'autoplay'   => 'false',
				'preload'   => 'auto',
				'height'     => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3bubbleAudio' ) );
			extract( shortcode_atts( array(
				'playlist'   => 'show',
				'order'      => 'asc',
				'download'   => 'false',
				'search'     => $search,
				'autoplay'   => 'false',
				'preload'   => 'auto',
				'height'     => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3audible' ) );

			$playlist = ((empty($playlist)) ? 'show' : $playlist);
			$download = ((empty($download)) ? 'false' : $download);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$preload  = ((empty($preload)) ? 'auto' : $preload);
			
			// Check download
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = 1;
				}else{
					if($download == 'true'){
						$download = 1;
					}else{
						$download = 0;
					}
				}
			}

			// Force download
			if($s3bubble_force_download == 'true'){
				$download = 1;
			}

            $player_id = uniqid();

			return '<div class="audio-playlist-' . $player_id . '"></div>
            <script type="text/javascript">
				jQuery(document).ready(function($) {
					$(".audio-playlist-' . $player_id . '").audioPlaylist({
						Ajax:       "' . admin_url('admin-ajax.php') . '",
						Pid:		"' . $player_id . '",
						Bucket:		"' . $bucket . '",
						Folder:		"' . $folder . '",
						Cloudfront:	"' . $cloudfront . '",
						Security:	"' . $ajax_nonce . '",
						AutoPlay:	' . $autoplay . ',
						Download:	' . $download . ',
						Preload:	"' . $preload . '",
						Height:    "' . $height . '",
						Playlist:  "' . (($playlist == 'hidden') ? 'none' : 'block' ) . '"
					},function(){
						
					});
				});
			</script>';

        }

        /*
		* Run the s3bubble jplayer single audio function
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_single_player($atts){

			//Run a S3Bubble security check
			$ajax_nonce = wp_create_nonce( "s3bubble-nonce-security" );

			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");		
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$s3bubble_force_download = get_option("s3bubble_force_download");

			extract( shortcode_atts( array(
				'style'      => 'bar',
				'download'   => 'false',
				'autoplay'   => 'false',
				'start'      => 'false',
				'finish'     => 'false',
				'preload'    => 'auto',
				'bucket'     => '',
				'track'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleAudioSingle' ) );
			extract( shortcode_atts( array(
				'style'      => 'bar',
				'download'   => 'false',
				'autoplay'   => 'false',
				'start'      => 'false',
				'finish'     => 'false',
				'preload'    => 'auto',
				'bucket'     => '',
				'track'      => '',
				'cloudfront' => ''
			), $atts, 's3audibleSingle' ) );

			$style    = ((empty($style)) ? 'bar' : $style);
			$download = ((empty($download)) ? 'false' : $download);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$preload  = ((empty($preload)) ? 'auto' : $preload);
			$start = ((empty($start)) ? 'false' : $start);
			$finish = ((empty($finish)) ? 'false' : $finish);
			
			// Check download
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = 1;
				}else{
					if($download == 'true'){
						$download = 1;
					}else{
						$download = 0;
					}
				}
			}

			// Force download
			if($s3bubble_force_download == 'true'){
				$download = 1;
			}

            $player_id = uniqid();

            return '<div class="single-audio-' . $player_id . '"></div>
            <script type="text/javascript">
				jQuery(document).ready(function($) {
					$(".single-audio-' . $player_id . '").singleAudio({
						Ajax:       "' . admin_url('admin-ajax.php') . '",
						Pid:		"' . $player_id . '",
						Bucket:		"' . $bucket . '",
						Key:		"' . $track . '",
						Cloudfront:	"' . $cloudfront . '",
						Security:	"' . $ajax_nonce . '",
						AutoPlay:	' . $autoplay . ',
						Download:	' . $download . ',
						Styles:      "' . $style . '",
						Start:      "' . $start . '",
						Finish:	    "' . $finish . '"
					},function(){
						
					});
				});
				jQuery( window ).on("beforeunload",function() {
					addListener(window.s3bubbleAnalytics);
				});
			</script>';

		}
        
		/*
		* Run the s3bubble jplayer video playlist function
		* @author sameast
		* @none
		*/ 
        function s3bubble_video_player($atts){
	        
	        //Run a S3Bubble security check
			$ajax_nonce = wp_create_nonce( "s3bubble-nonce-security" );

			/*
			 * Player options
			 */ 
			$loggedin           = get_option("s3-loggedin");
			$search             = get_option("s3-search");
			$responsive         = get_option("s3-responsive");
			$stream             = get_option("s3-stream");
			$s3bubble_force_download = get_option("s3bubble_force_download");

        	extract( shortcode_atts( array(
				'playlist'   => 'show',
				'download'   => 'false',
				'aspect'     => '16:9',
				'search'     => $search,
				'responsive' => $responsive,
				'autoplay'   => 'false',
				'order'      => 'asc',
				'height'     => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3bubbleVideo' ) );
			extract( shortcode_atts( array(
				'playlist'   => 'show',
				'download'   => 'false',
				'aspect'     => '16:9',
				'search'     => $search,
				'responsive' => $responsive,
				'autoplay'   => 'false',
				'order'      => 'asc',
				'height'     => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3video' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$playlist = ((empty($playlist)) ? 'show' : $playlist);
			$download = ((empty($download)) ? 'false' : $download);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			
			// Check download
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = 1;
				}else{
					if($download == 'true'){
						$download = 1;
					}else{
						$download = 0;
					}
				}
			}

			// Force download
			if($s3bubble_force_download == 'true'){
				$download = 1;
			}

            $player_id = uniqid();

            return '<div class="video-playlist-' . $player_id . '"></div>
            <script type="text/javascript">
				jQuery(document).ready(function($) {
					$(".video-playlist-' . $player_id . '").videoPlaylist({
						Ajax:       "' . admin_url('admin-ajax.php') . '",
						Pid:		"' . $player_id . '",
						Bucket:		"' . $bucket . '",
						Folder:		"' . $folder . '",
						Cloudfront:	"' . $cloudfront . '",
						Security:	"' . $ajax_nonce . '",
						AutoPlay:	' . $autoplay . ',
						Download:	' . $download . ',
						Aspect:	    "' . $aspect . '",
						Height:    "' . $height . '",
						Playlist:  "' . (($playlist == 'show') ? "" : "display:none;" ) . '"
					},function(){
						
					});
				});
			</script>';

		}
		
		/*
		* Run the s3bubble jplayer single video function
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_single_player($atts){
			
			//Run a S3Bubble security check
			$ajax_nonce = wp_create_nonce( "s3bubble-nonce-security" );

			// get option from database	
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$responsive          = get_option("s3-responsive");
			$stream              = get_option("s3-stream");
			$s3bubble_force_download = get_option("s3bubble_force_download");

	        extract( shortcode_atts( array(
	        	'download'   => 'false',
	        	'twitter' => 'false',
	        	'twitter_handler' => '@s3bubble',
	        	'twitter_text' => 'Shared via s3bubble.com media streaming',
				'aspect'     => '16:9',
				'responsive' => $responsive,
				'autoplay'   => 'false',
				'start'      => 'false',
				'finish'     => 'false',
				'disable_skip'     => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3bubbleVideoSingle' ) );
			extract( shortcode_atts( array(
				'download'   => 'false',
				'twitter' => 'false',
				'twitter_handler' => 's3bubble',
	        	'twitter_text' => 'Shared via s3bubble.com media streaming',
				'aspect'     => '16:9',
				'responsive' => $responsive,
				'autoplay'   => 'false',
				'start'      => 'false',
				'finish'     => 'false',
				'disable_skip'     => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3videoSingle' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$twitter   = ((empty($twitter)) ? 'false' : $twitter);
			$disable_skip = ((empty($disable_skip)) ? 'false' : $disable_skip);
			$download = ((empty($download)) ? 'false' : $download);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$start = ((empty($start)) ? 'false' : $start);
			$finish = ((empty($finish)) ? 'false' : $finish);
			
			// Check download
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = 1;
				}else{
					if($download == 'true'){
						$download = 1;
					}else{
						$download = 0;
					}
				}
			}

			// Force download
			if($s3bubble_force_download == 'true'){
				$download = 1;
			}
			
            $player_id = uniqid();

            return '<div class="single-video-' . $player_id . '"></div>
            <script type="text/javascript">
				jQuery(document).ready(function($) {
					$(".single-video-' . $player_id . '").singleVideo({
						Ajax:       "' . admin_url('admin-ajax.php') . '",
						ApiCall:    "s3bubble_video_single_internal_ajax",
						Flash:      "https://s3.amazonaws.com/s3bubble.assets/flash/s3bubble.rtmp.swf",
						Supplied:   "m4v",
						Pid:		"' . $player_id . '",
						Bucket:		"' . $bucket . '",
						Key:		"' . $track . '",
						Cloudfront:	"' . $cloudfront . '",
						Security:	"' . $ajax_nonce . '",
						AutoPlay:	' . $autoplay . ',
						Download:	' . $download . ',
						Aspect:	    "' . $aspect . '",
						DisableSkip:"' . $disable_skip . '",
						Twitter:    "' . $twitter . '",
						TwitterText:    "' . $twitter_text . '",
						TwitterHandler:	"' . $twitter_handler . '",
						Start:      "' . $start . '",
						Finish:	    "' . $finish . '"
					},function(){
						
					});
				});
				jQuery( window ).on("beforeunload",function() {
					addListener(window.s3bubbleAnalytics);
				});
			</script>';

		}

		/*
		* Run the s3bubble jplayer single video function
		* @author sameast
		* @none
		*/ 
		function s3bubble_lightbox_video($atts){
				
				//Run a S3Bubble security check
				$ajax_nonce = wp_create_nonce( "s3bubble-nonce-security" );

				// get option from database	
				$loggedin            = get_option("s3-loggedin");
				$search              = get_option("s3-search");
				$responsive          = get_option("s3-responsive");
				$stream              = get_option("s3-stream");
				$s3bubble_force_download = get_option("s3bubble_force_download");

		        extract( shortcode_atts( array(
		        	'download'   => 'false',
		        	'twitter' => 'false',
		        	'twitter_handler' => '@s3bubble',
		        	'twitter_text' => 'Shared via s3bubble.com media streaming',
					'aspect'     => '16:9',
					'text'     => 'S3Bubble Video',
					'responsive' => $responsive,
					'autoplay'   => 'false',
					'start'      => 'false',
					'finish'     => 'false',
					'disable_skip'     => 'false',
					'playlist'   => '',
					'height'     => '',
					'track'      => '',
					'bucket'     => '',
					'folder'     => '',
					'cloudfront' => ''
				), $atts, 's3bubbleVideoSingle' ) );
				extract( shortcode_atts( array(
					'download'   => 'false',
					'twitter' => 'false',
					'twitter_handler' => 's3bubble',
		        	'twitter_text' => 'Shared via s3bubble.com media streaming',
					'aspect'     => '16:9',
					'text'     => 'S3Bubble Video',
					'responsive' => $responsive,
					'autoplay'   => 'false',
					'start'      => 'false',
					'finish'     => 'false',
					'disable_skip'     => 'false',
					'playlist'   => '',
					'height'     => '',
					'track'      => '',
					'bucket'     => '',
					'folder'     => '',
					'cloudfront' => ''
				), $atts, 's3videoSingle' ) );

				$aspect       = ((empty($aspect)) ? '16:9' : $aspect);
				$twitter   = ((empty($twitter)) ? 'false' : $twitter);
				$disable_skip = ((empty($disable_skip)) ? 'false' : $disable_skip);
				$link_text    = ((empty($text)) ? 'S3Bubble Video' : $text);
				$download     = ((empty($download)) ? 'false' : $download);
				$autoplay     = ((empty($autoplay)) ? 'false' : $autoplay);
				$start        = ((empty($start)) ? 'false' : $start);
				$finish       = ((empty($finish)) ? 'false' : $finish);
				
				// Check download
				if($loggedin == 'true'){
					if ( is_user_logged_in() ) {
						$download = 1;
					}else{
						if($download == 'true'){
							$download = 1;
						}else{
							$download = 0;
						}
					}
				}

				// Force download
				if($s3bubble_force_download == 'true'){
					$download = 1;
				}
				
	            $player_id = uniqid();
			
	            return '<a class="s3bubble-popup-link-' . $player_id . '" href="#s3bubble-popup-' . $player_id . '">' . $link_text . '</a>
	            <script type="text/javascript">
					jQuery(document).ready(function($) {
						var fireOnce = true;
						$("body").append("<div id=\"s3bubble-popup-' . $player_id . '\" class=\"s3bubble-popup-styles\"><div class=\"single-video-' . $player_id . '\"></div></div>");
						$(".s3bubble-popup-link-' . $player_id . '").magnificPopup({
						  type:"inline",
						  callbacks: {
					            elementParse: function(item){
						            if(fireOnce){
						                $(".single-video-' . $player_id . '").singleVideo({
											Ajax:       "' . admin_url('admin-ajax.php') . '",
											ApiCall:    "s3bubble_video_single_internal_ajax",
											Flash:      "https://s3.amazonaws.com/s3bubble.assets/flash/s3bubble.rtmp.swf",
											Supplied:   "m4v",
											Pid:		"' . $player_id . '",
											Bucket:		"' . $bucket . '",
											Key:		"' . $track . '",
											Cloudfront:	"' . $cloudfront . '",
											Security:	"' . $ajax_nonce . '",
											AutoPlay:	' . $autoplay . ',
											Download:	' . $download . ',
											Aspect:	    "' . $aspect . '",
											DisableSkip:"' . $disable_skip . '",
											Twitter:    "' . $twitter . '",
											TwitterText:    "' . $twitter_text . '",
											TwitterHandler:	"' . $twitter_handler . '",
											Start:      "' . $start . '",
											Finish:	    "' . $finish . '"
										},function(){
											
										});
										fireOnce = false;
									}
					            }
					        }
						});
					});
					jQuery( window ).on("beforeunload",function() {
						addListener(window.s3bubbleAnalytics);
					});
				</script>';

			}


		}
	/*
	* Initiate the class
	* @author sameast
	* @none
	*/ 
	$s3bubble_audio = new s3bubble_audio();
	
} //End Class S3Bubble