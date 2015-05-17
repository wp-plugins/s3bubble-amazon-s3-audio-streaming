<?php
/*
Plugin Name: S3Bubble Amazon S3 Video And Audio Streaming With Analytics
Plugin URI: https://s3bubble.com/
Description: S3Bubble offers simple, media streaming from Amazon S3 to WordPress. In just 4 simple steps. 
Version: 2.0
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
		public  $jtoggle		    = 'true';
		public  $loggedin        = 'false';
		public  $search          = 'false';
		public  $responsive      = 'responsive';
		public  $theme           = 's3bubble_clean';
		public  $stream          = 'm4v';
		public  $version         =  37;
		public  $s3bubble_video_all_bar_colours = '#adadad';
		public  $s3bubble_video_all_bar_seeks   = '#dd0000';
		public  $s3bubble_video_all_controls_bg = '#010101';
		public  $s3bubble_video_all_icons       = '#FFFFFF';
		private $endpoint       = 'https://api.s3bubble.com/v1/';
		
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
			add_action('admin_menu', array( $this, 's3bubble_audio_admin_menu' ));
			
			/*
			 * Add css to the header of the document
			 * @author sameast
			 * @params none
			 */ 
			add_action( 'wp_head', array( $this, 's3bubble_audio_css' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 's3bubble_audio_javascript' ), 11 );
			
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
			add_action('wp_ajax_nopriv_s3bubble_video_single_internal_ajax', array( $this, 's3bubble_video_single_internal_ajax' ) ); 
		    
		    add_action( 'wp_ajax_s3bubble_video_playlist_internal_ajax', array( $this, 's3bubble_video_playlist_internal_ajax' ) );
			add_action('wp_ajax_nopriv_s3bubble_video_playlist_internal_ajax', array( $this, 's3bubble_video_playlist_internal_ajax' ) ); 
			
			add_action( 'wp_ajax_s3bubble_audio_single_internal_ajax', array( $this, 's3bubble_audio_single_internal_ajax' ) );
			add_action('wp_ajax_nopriv_s3bubble_audio_single_internal_ajax', array( $this, 's3bubble_audio_single_internal_ajax' ) ); 
            
			add_action( 'wp_ajax_s3bubble_audio_playlist_internal_ajax', array( $this, 's3bubble_audio_playlist_internal_ajax' ) );
			add_action('wp_ajax_nopriv_s3bubble_audio_playlist_internal_ajax', array( $this, 's3bubble_audio_playlist_internal_ajax' ) ); 

			add_action( 'wp_ajax_s3bubble_video_rtmp_internal_ajax', array( $this, 's3bubble_video_rtmp_internal_ajax' ) );
			add_action('wp_ajax_nopriv_s3bubble_video_rtmp_internal_ajax', array( $this, 's3bubble_video_rtmp_internal_ajax' ) ); 
			
			/*
			 * Admin dismiss message
			 */
			add_action('admin_notices', array( $this, 's3bubble_admin_notice' ) );
			add_action('admin_init', array( $this, 's3bubble_nag_ignore' ) );

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

			// Important CDN fixes
			wp_register_style('s3bubble.helpers', ("//s3.amazonaws.com/s3bubble.assets/plugin.css/style.css"), array(),  $this->version );
			wp_enqueue_style('s3bubble.helpers');

			echo '<style type="text/css">
					.s3bubble-media-main-progress, .s3bubble-media-main-volume-bar {background-color: '.stripcslashes($progress).' !important;}
					.s3bubble-media-main-play-bar, .s3bubble-media-main-volume-bar-value {background-color: '.stripcslashes($seek).' !important;}
					.s3bubble-media-main-interface, .s3bubble-media-main-video-play {background-color: '.stripcslashes($background).' !important;color: '.stripcslashes($icons).' !important;}
					.s3bubble-media-main-video-loading {color: '.stripcslashes($icons).' !important;}
					.s3bubble-media-main-interface  > * a, .s3bubble-media-main-interface  > * a:hover, .s3bubble-media-main-interface  > * i, .s3bubble-media-main-interface  > * i:hover, .s3bubble-media-main-current-time, .s3bubble-media-main-duration, .time-sep, .s3icon-cloud-download {color: '.stripcslashes($icons).' !important;text-decoration: none !important;font-style: normal !important;}
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
					'serveraddress' => $_SERVER['REMOTE_ADDR']
				));
				wp_register_script( 's3bubble.mobile.browser.check', plugins_url('assets/js/mobile.browser.check.min.js',__FILE__ ), array('jquery'),  $this->version, true );
				wp_register_script( 's3bubble.analytics.min', plugins_url('assets/js/s3analytics.min.js',__FILE__ ), array('jquery'),  $this->version, true );

				//Video js
				wp_register_script( 's3bubble.video.js.include', plugins_url('assets/videojs/video.min.js',__FILE__ ), array('jquery'),  $this->version, true );

				wp_enqueue_script('jquery');
				wp_enqueue_script('jquery-migrate');
				wp_enqueue_script('wp-mediaelement');
				wp_enqueue_script('s3bubble.video.js.include');
				wp_enqueue_script('s3player.all.s3bubble');
				wp_enqueue_script('s3bubble.mobile.browser.check');
				wp_enqueue_script('s3bubble.analytics.min');
				
            }
		}

		/*
		* Video playlist internal ajax call
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_rtmp_internal_ajax(){
			
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");

			//set POST variables
			$url = $this->endpoint . 'rtmp/video';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $_POST['Bucket'],
			    'Key' => $_POST['Key'],
			    'Cloudfront' => $_POST['Cloudfront'],
			    'IsMobile' => $_POST['IsMobile']
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
			echo $result;
			curl_close($ch);

			die();	
			
		}

		/*
		* Video single internal ajax call
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_single_internal_ajax(){
			
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");

			//set POST variables
			$url = $this->endpoint . 'main_plugin/single_video_object';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $_POST['Bucket'],
			    'Key' => $_POST['Key'],
			    'Cloudfront' => $_POST['Cloudfront'],
			    'Server' => $_POST['Server']
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
			echo $result;
			curl_close($ch);
			
			die();	
		}

		/*
		* Video playlist internal ajax call
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_playlist_internal_ajax(){
			
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");

			//set POST variables
			$url = $this->endpoint . 'main_plugin/playlist_video_objects';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $_POST['Bucket'],
			    'Folder' => $_POST['Folder']
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
			echo $result;
			curl_close($ch);
			
			die();	
		}

		/*
		* Audio single internal ajax call
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_single_internal_ajax(){
			
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");

			//set POST variables
			$url = $this->endpoint . 'main_plugin/single_audio_object';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $_POST['Bucket'],
			    'Key' => $_POST['Key'],
			    'Cloudfront' => $_POST['Cloudfront'],
			    'Server' => $_POST['Server']
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
			echo $result;
			curl_close($ch);
			
			die();	
		}

        /*
		* Audio playlist internal ajax call
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_playlist_internal_ajax(){
			
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");

			//set POST variables
			$url = $this->endpoint . 'main_plugin/playlist_audio_objects';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $_POST['Bucket'],
			    'Folder' => $_POST['Folder']
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
			echo $result;
			curl_close($ch);
			
			die();	
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
        	die();
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
			        $('#s3bubble-mce-submit').click(function(){
			        	var bucket     = $('#s3bucket').val();
			        	var folder     = $('#s3folder').val();
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
						var aspect = '16:9';
						if($('#s3aspect').val() != ''){
						    aspect = $('#s3aspect').val();
						}
	        	        var shortcode = '[s3bubbleVideo bucket="' + bucket + '" folder="' + folder + '" aspect="' + aspect + '"  height="' + height + '"  autoplay="' + autoplay + '" playlist="' + playlist + '" ' + order + ' download="' + download + '"/]';
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
        	die();
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
			        $('#s3bubble-mce-submit').click(function(){

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

						var shortcode = '';
						if(StreamingType === 'progressive'){
							shortcode = '[s3bubbleVideoSingle bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '"/]';
							if($("#s3mediaelement").is(':checked')){
							    shortcode = '[s3bubbleMediaElementVideo bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '"/]';
							}else if($("#s3videojs").is(':checked')){
								shortcode = '[s3bubbleVideoSingleJs  bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '"/]';
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
								shortcode = '[s3bubbleRtmpVideoDefault bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '"/]';
								if($("#s3mediaelement").is(':checked')){
									shortcode = '[s3bubbleRtmpVideo bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '"/]';
								}else if($("#s3videojs").is(':checked')){
									shortcode = '[s3bubbleRtmpVideoJs bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '"/]';
								}
								tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
		                   	 	tb_remove();
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
				    		<input type="text" class="s3bubble-form-input" name="s3bubble-cloudfrontid" id="s3bubble-cloudfrontid">
				    	</div>
					</span>
					<span>
						<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="fname">Aspect Ratio: (Example: 16:9 / 4:3 Default: 16:9)</label>
				    		<input type="text" class="s3bubble-form-input" name="aspect" id="s3aspect">
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
						<a href="#"  id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
					</span>
				</form>
		    </div>
        	<?php
        	die();
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
			        $('#s3bubble-mce-submit').click(function(){
			        	var bucket     = $('#s3bucket').val();
			        	var folder     = $('#s3folder').val();
			        	var cloudfront = $('#s3cloudfront').val();
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
						<a href="#"  id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
					</span>
				</form>
			</div>
        	<?php
        	die();
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
			        $('#s3bubble-mce-submit').click(function(){

			        	var stream = $('#s3bubble-live-stream-url').val();
			        	var aspect = '16:9';
						if($('#s3aspect').val() != ''){
						    aspect = $('#s3aspect').val();
						}
			        	
						var shortcode = '[s3bubbleLiveStream stream="' + stream + '" aspect="' + aspect + '"/]';
	                    if($("#s3mediaelement").is(':checked')){
	                    	shortcode = '[s3bubbleLiveStreamMedia stream="' + stream + '" aspect="' + aspect + '"/]';
						}
	                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
	                    tb_remove();
			        });
		        })
		    </script>
		    <div class="s3bubble-lightbox-wrap">
			    <form class="s3bubble-form-general">
			    	<span>
				    	<div>
				    		<label for="s3bubble-live-stream-url">Your Live Stream Url:</label>
				    		<input type="text" class="s3bubble-form-input" name="s3bubble-live-stream-url" id="s3bubble-live-stream-url">
				    	</div>
					</span>
					<span>
						<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="aspect">Aspect Ratio: (Example: 16:9 / 4:3 Default: 16:9)</label>
				    		<input type="text" class="s3bubble-form-input" name="aspect" id="s3aspect">
				    	</div>
					</span> 
					<span>
						<input type="checkbox" name="mediaelement" id="s3mediaelement" value="true">Use Media Elements JS <i>(Changes the player from default to media element js player)</i>
					</span>
					<span>
						<a href="#"  id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
					</span>
				</form>
			</div>
        	<?php
        	die();
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
				$s3audible_username	= $_POST['s3audible_username'];
				$s3audible_email	= $_POST['s3audible_email'];
				$loggedin			= addslashes($_POST['loggedin']);

				// new
				$s3bubble_video_all_bar_colours	= $_POST['s3bubble_video_all_bar_colours'];
				$s3bubble_video_all_bar_seeks	= $_POST['s3bubble_video_all_bar_seeks'];
				$s3bubble_video_all_controls_bg	= $_POST['s3bubble_video_all_controls_bg'];
				$s3bubble_video_all_icons	    = $_POST['s3bubble_video_all_icons'];

			    // Update the DB with the new option values
				update_option("s3-s3audible_username", $s3audible_username);
				update_option("s3-s3audible_email", $s3audible_email);
				update_option("s3-loggedin", $loggedin);
				
				// new
				update_option("s3bubble_video_all_bar_colours", $s3bubble_video_all_bar_colours);
				update_option("s3bubble_video_all_bar_seeks", $s3bubble_video_all_bar_seeks);
				update_option("s3bubble_video_all_controls_bg", $s3bubble_video_all_controls_bg);
				update_option("s3bubble_video_all_icons", $s3bubble_video_all_icons);

			}
			
			$s3audible_username	= get_option("s3-s3audible_username");
			$s3audible_email	= get_option("s3-s3audible_email");
			$loggedin			= get_option("s3-loggedin");			
			
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
			<div class="metabox-holder has-right-sidebar">
				<div class="inner-sidebar" style="width:40%">
					<div class="postbox">
						<h3 class="hndle">PLEASE USE WYSIWYG EDITOR BUTTONS</h3>
						<div class="inside">
							<img style="width: 100%;" src="https://isdcloud.s3.amazonaws.com/wp_editor.png" />
						</div> 
					</div>
					<div class="postbox">
						<h3 class="hndle">Track Analytics</h3>
						<div class="inside">
							<ul class="s3bubble-adverts">
								<li>
									<img src="<?php echo plugins_url('/assets/images/analytics.png',__FILE__); ?>" alt="S3Bubble wordpress plugin" /> 
									<p>S3Bubble is excited to present to you its first consumer analytics page. All your videos that display on your WordPress site will now link to our management system. Find out where your target audience is so you can start strategically promoting your site and grow a global audience.</p>
									<a href="https://s3bubble.com/" target="_blank">Visit s3bubble.com</a>
								</li>
							</ul>        
						</div> 
					</div>
					<div class="postbox">
						<h3 class="hndle">S3Bubble Support</h3>
						<div class="inside">
							<ul class="s3bubble-adverts">
								<li>
									<img src="<?php echo plugins_url('/assets/images/support.png',__FILE__); ?>" alt="S3Bubble iPhone" /> 
									<h3>
										Are you stuck upgraded and not happy?
									</h3>
									<p>If you are stuck at any point or preferred the old version please just click the download below and delete this version and re upload the plugin.</p>
									<a class="button button-s3bubble" href="https://s3.amazonaws.com/s3bubble.assets/main.plugin/s3bubble-amazon-s3-audio-streaming.zip" target="_blank">DOWNLOAD OLD VERISON</a>
								</li>
							</ul>        
						</div> 
					</div>
					<div class="postbox">
						<h3 class="hndle">S3Bubble Mobile Apps - Monitor Analytics</h3>
						<div class="inside">
							<ul class="s3bubble-adverts">
								<li>
									<img src="<?php echo plugins_url('/assets/images/plugin-mobile-icon.png',__FILE__); ?>" alt="S3Bubble iPhone" /> 
									<h3>
										iPhone Mobile App
									</h3>
									<p>Record Manage Watch Download Share. Manage all your video and audio analytics.</p>
									<a class="button button-s3bubble" href="https://itunes.apple.com/us/app/s3bubble/id720256052?ls=1&mt=8" target="_blank">GET THE APP</a>
								</li>
								<li>
									<img src="<?php echo plugins_url('/assets/images/plugin-mobile-icon.png',__FILE__); ?>" alt="S3Bubble Android" /> 
									<h3>
										Android Mobile App
									</h3>
									<p>Record Manage Watch Download Share. Manage all your video and audio analytics.</p>
									<a class="button button-s3bubble" href="https://play.google.com/store/apps/details?id=com.s3bubble" target="_blank">GET THE APP</a>
								</li>
							</ul>        
						</div> 
					</div>
				</div>
				<div id="post-body">
					<div id="post-body-content" style="margin-right: 41%;">
						<div class="postbox">
							<h3 class="hndle">Fill in details below if stuck please <a class="button button-s3bubble" style="float: right;margin: -5px -10px;" href="https://www.youtube.com/watch?v=VFG3-nvV6F0" target="_blank">Watch Video</a></h3>
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

			//close connection
			curl_close($ch);
        }

        /*
		* Run the video js supports Live Streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_live_stream_media_element_video($atts){
	   	
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

				return '<div class="video-wrap-' . $player_id . '" style="width:100%;overflow:hidden;">
							<video id="video-' . $player_id . '" style="width:100%;" controls="controls" preload="none">
								<source type="application/x-mpegURL" src="' . $stream . '" />
							</video>
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
					    			poster: "https://s3.amazonaws.com/s3bubble.assets/video.player/placeholder.png",
					    			videoWidth: "100%",
									videoHeight: "100%",
									enableAutosize: true,
									plugins: ["flash"],
									features: ["playpause","volume","fullscreen"],
									pluginPath: "' . plugins_url('assets/mediaelementjs/build/',__FILE__ ) . '",
									flashName: "flashmediaelement-live.swf",
					    			success: function(mediaElement, node, player) {
					    				$(".mejs-time-rail, .mejs-time").hide();
					    				$(".mejs-fullscreen-button").css("float","right");
					    				$(".mejs-controls").append("<span class=\'s3bubble-live-media-element\'>LOADING...</span>");
					    				// add event listener
								        mediaElement.addEventListener("timeupdate", function(e) {
								            if(e.currentTime){
								            	$(".s3bubble-live-media-element").text("LIVE");
								            }
								        }, false);
							     	}
				    			});
							});
						</script>';

			}

			//close connection
			curl_close($ch);
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
    			return "<i>Your hosting does not have PHP curl installed. Please install php curl S3Bubble requires PHP curl to work!</i>";
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

		    $result = curl_exec($ch);
			$track = json_decode($result, true);
			//print_r($track);
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
			//close connection
			curl_close($ch);
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
    			return "<i>Your hosting does not have PHP curl installed. Please install php curl S3Bubble requires PHP curl to work!</i>";
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

		    $result = curl_exec($ch);
			$track = json_decode($result, true);
			//print_r($track);
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
			//close connection
			curl_close($ch);
       }

       // -------------------------- RTMP SETUPS BELOW ------------------------------ //

       /*
		* Run the jplayer supports RTMP streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_rtmp_video_default($atts){
	   	
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");	
			
        	extract( shortcode_atts( array(
        		'skip'       => 'false',
				'autoplay'   => 'false',
				'aspect'     => '16:9',
				'bucket'     => '',
				'track'      => '',
				'time'       => '',
				'cloudfront' => '',
				'advert'     => ''
			), $atts, 's3bubbleRtmpVideoDefault' ) );

        	$skip     = ((empty($skip)) ? 'false' : $skip);
			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);

            $player_id = uniqid();

            return '<div id="s3bubble-media-main-container-' . $player_id . '" class="s3bubble-media-main-video">
				    <div id="jquery_jplayer_RTMP_' . $player_id . '" class="s3bubble-media-main-jplayer"></div>
				    <div class="s3bubble-media-main-video-skip">
						<h2>Skip Ad</h2>
						<i class="s3icon s3icon-step-forward"></i>
						<img id="s3bubble-media-main-skip-tumbnail" src=""/>
					</div>
				    <div class="s3bubble-media-main-video-loading">
				    	<i class="s3icon s3icon-circle-o-notch s3icon-spin"></i>
				    </div>
				    <div class="s3bubble-media-main-video-play">
						<i class="s3icon s3icon-play"></i>
					</div>
				    <div class="s3bubble-media-main-gui" style="visibility: hidden;">
				        <div class="s3bubble-media-main-interface">
				            <div class="s3bubble-media-main-controls-holder">
					            <span class="s3bubble-media-main-left-controls">
									<a href="javascript:;" class="s3bubble-media-main-play" tabindex="1"><i class="s3icon s3icon-play"></i></a>
									<a href="javascript:;" class="s3bubble-media-main-pause" tabindex="1"><i class="s3icon s3icon-pause"></i></a>
								</span>
								<div class="s3bubble-media-main-progress">
								    <div class="s3bubble-media-main-seek-bar">
								        <div class="s3bubble-media-main-play-bar"></div>
								    </div>
								</div>
								<span class="s3bubble-media-main-right-controls">
									<a href="javascript:;" class="s3bubble-media-main-full-screen" tabindex="3" title="full screen"><i class="s3icon s3icon-arrows-alt"></i></a>
									<a href="javascript:;" class="s3bubble-media-main-restore-screen" tabindex="3" title="restore screen"><i class="s3icon s3icon-arrows-alt"></i></a>
									<div class="s3bubble-media-main-volume-bar">
									    <div class="s3bubble-media-main-volume-bar-value"></div>
									</div>
									<a href="javascript:;" class="s3bubble-media-main-mute" tabindex="2" title="mute"><i class="s3icon s3icon-volume-up"></i></a>
									<a href="javascript:;" class="s3bubble-media-main-unmute" tabindex="2" title="unmute"><i class="s3icon s3icon-volume-off"></i></a>
									<div class="s3bubble-media-main-time-container">
										<div class="s3bubble-media-main-duration"></div>
									</div>
								</span>
				            </div>
				        </div>
				    </div>
				    <div class="s3bubble-media-main-playlist" style="display:none;">
						<ul>
							<li></li>
						</ul>
					</div>
				    <div class="s3bubble-media-main-no-solution">
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
					var aspect     = $("#s3bubble-media-main-container-' . $player_id . '").width()/16*9;
					var IsMobile   = false;
					if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
						IsMobile = true;
					}
					var s3bubbleRtmpPlaylist = new jPlayerPlaylist({
						jPlayer: "#jquery_jplayer_RTMP_' . $player_id . '",
						cssSelectorAncestor: "#s3bubble-media-main-container-' . $player_id . '"
					}, s3bubbleRtmpPlaylist, {
						playlistOptions: {
							enableRemoveControls: true
						},
						ready : function(event) {
							var sendData = {
								"action": "s3bubble_video_rtmp_internal_ajax",
								"Timezone":"America/New_York",
							    "Bucket" : Bucket,
							    "Key" : Key,
							    "Cloudfront" : Cloudfront,
							    "IsMobile" : IsMobile
							} 
							$.post("' . admin_url('admin-ajax.php') . '", sendData, function(response) {
								if(response.error){
									console.log(response.message);
								}else{
									
									$("#s3bubble-media-main-skip-tumbnail").attr("src", response.results[0].poster);
									s3bubbleRtmpPlaylist.setPlaylist(response.results);

									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 240px 0 40px");	
									if(IsMobile){
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 60px 0 40px");	
									}
									if(response.user === "s2member_level1" || response.user === "s2member_level2"){
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 280px 0 40px");
										if(IsMobile){
											$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 100px 0 40px");	
										}
										$(".s3bubble-media-main-right-controls").prepend("<a href=\"https://s3bubble.com\" class=\"s3bubble-media-main-logo\"><i id=\"icon-S3\" class=\"icon-S3\"></i></a>");
									}
									setTimeout(function(){
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut();
										$(".s3bubble-media-main-gui").css("visibility", "visible");
									},2000);
								}
							},"json");
							// Remove right click
							$("video").bind("contextmenu",function(){
					        	return false;
					        });
							$(".s3bubble-media-main-video-skip").on( "click", function() {
								$(".s3bubble-media-main-video-loading").fadeIn();
								$(".s3bubble-media-main-video-skip").animate({
								    left: "-230"
								}, 50, function() {
								    s3bubbleRtmpPlaylist.next();
								});
								
							});
						},
						loadedmetadata: function (event) {
							
					    },
					    resize: function (event) {

					    },
					    click: function (event) {

					    },
					    error: function (event) {
					    	console.log(event.jPlayer.error);
        					console.log(event.jPlayer.error.type);
					    },
					    warning: function (event) {

					    },
					    loadstart: function (event) {

					    },
					    progress: function (event) {

					    },
					    play: function (event) {
					    	$(".s3bubble-media-main-video-loading").fadeIn();
							var CurrentState = s3bubbleRtmpPlaylist.current;
							var PlaylistKey  = s3bubbleRtmpPlaylist.playlist[CurrentState];
							if(IsMobile === false){
								if(PlaylistKey.advert){
									$(".s3bubble-media-main-video-skip").animate({
									    left: "0"
									}, 50, function() {
									    // Animation complete.
									});
								}else{
									$(".s3bubble-media-main-video-skip").animate({
									    left: "-230"
									}, 50, function() {
									    s3bubbleRtmpPlaylist.next();
									});
								}
							}
							if(Current !== CurrentState && PlaylistKey.advert !== true){
								addListener({
									app_id: s3bubble_all_object.s3appid,
									server: s3bubble_all_object.serveraddress,
									bucket: Bucket,
									key: Key,
									type: "video",
									advert: false
								});
								Current = CurrentState;
							}
							
					    },
					    ended : function(event) {
							console.log("ended");
						},
					    timeupdate : function(event) {
							if (event.jPlayer.status.currentTime > 1) {
								$(".s3bubble-media-main-video-loading").fadeOut()
							}
							if (event.jPlayer.status.currentPercentAbsolute > 99) {
                                s3bubbleRtmpPlaylist.next();
                            }
						},
						swfPath: "https://s3.amazonaws.com/s3bubble.assets/flash/s3bubble.rtmp.swf",
				        supplied: ((IsMobile) ? "m4v" : "rtmpv"),
				        preload: "metadata",
						useStateClassSkin: true,
						autoBlur: false,
						smoothPlayBar: false,
						keyEnabled: true,
						audioFullScreen: true,
						remainingDuration: true,
						size: {
				            width: "100%",
				            height: aspect
				        },
				        autohide : {
							full : true,
							restored : true,
							hold : 3000
						}
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
    			return "<i>Your hosting does not have PHP curl installed. Please install php curl S3Bubble requires PHP curl to work!</i>";
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
			//print_r($track);
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
			//close connection
			curl_close($ch);
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
    			return "<i>Your hosting does not have PHP curl installed. Please install php curl S3Bubble requires PHP curl to work!</i>";
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

		    $result = curl_exec($ch);
			$track = json_decode($result, true);
			//print_r($track);
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
			//close connection
			curl_close($ch);
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
    			return "<i>Your hosting does not have PHP curl installed. Please install php curl S3Bubble requires PHP curl to work!</i>";
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

		    $result = curl_exec($ch);
			$track = json_decode($result, true);

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
			//close connection
			curl_close($ch);
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
    			return "<i>Your hosting does not have PHP curl installed. Please install php curl S3Bubble requires PHP curl to work!</i>";
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
			//print_r($track);
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
			//close connection
			curl_close($ch);
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

			$url = $this->endpoint . 'main_plugin/single_audio_media_element';
			$fields = http_build_query(array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $bucket,
			    'Key' => $track
			));

			if(!function_exists('curl_init')){
    			return "<i>Your hosting does not have PHP curl installed. Please install php curl S3Bubble requires PHP curl to work!</i>";
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
			curl_close($ch);
       }
	   
	    /*
		* Run the s3bubble jplayer playlist function
		* @author sameast
		* @none
		*/ 
	   function s3bubble_audio_player($atts){
	   	  
			/*
			 * player options
			 */ 		
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
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
            $player_id = uniqid();
								
            return '<div id="s3bubble-media-main-container-' . $player_id . '" class="s3bubble-media-main-audio">
            	<div class="s3bubble-media-main-video-playlist-wrap">
				    <div id="jquery_jplayer_' . $player_id . '" class="s3bubble-media-main-jplayer"></div>
				    <div class="s3bubble-media-main-gui">
				        <div class="s3bubble-media-main-interface s3bubble-media-main-interface-audio-playlist">
				        	<div class="s3bubble-media-main-audio-loading">
						    	<i class="s3icon s3icon-circle-o-notch s3icon-spin"></i>
						    </div>
				            <div class="s3bubble-media-main-controls-holder" style="display:none;">
					            <span class="s3bubble-media-main-left-controls">
									<a href="javascript:;" class="s3bubble-media-main-play" tabindex="1"><i class="s3icon s3icon-play"></i></a>
									<a href="javascript:;" class="s3bubble-media-main-pause" tabindex="1"><i class="s3icon s3icon-pause"></i></a>
								</span>
								<div class="s3bubble-media-main-progress" dir="auto">
								    <div class="s3bubble-media-main-seek-bar" dir="auto">
								        <div class="s3bubble-media-main-play-bar" dir="auto"></div>
								    </div>
								</div>
								<span class="s3bubble-media-main-right-controls">
									<a href="javascript:;" class="s3bubble-media-main-playlist-list" tabindex="3" title="Playlist List"><i class="s3icon s3icon-list-ul"></i></a>
									<a href="javascript:;" class="s3bubble-media-main-playlist-search" tabindex="3" title="Search List"><i class="s3icon s3icon-search"></i></a>
									<div class="s3bubble-media-main-volume-bar" dir="auto">
									    <div class="s3bubble-media-main-volume-bar-value" dir="auto"></div>
									</div>
									<a href="javascript:;" class="s3bubble-media-main-mute" tabindex="2" title="mute"><i class="s3icon s3icon-volume-up"></i></a>
									<a href="javascript:;" class="s3bubble-media-main-unmute" tabindex="2" title="unmute"><i class="s3icon s3icon-volume-off"></i></a>
									<div class="s3bubble-media-main-time-container">
										<div class="s3bubble-media-main-duration"></div>
									</div>
								</span>
				            </div>
				        </div>
				    </div>
			    </div>
			    <div class="s3search s3audible-search-' . $player_id .  '" style="display:none;">
	                <input type="text" id="s3bubble-audio-playlist-tsearch-' . $player_id .  '" class="s3bubble-audio-playlist-tsearch" name="s3bubble-audio-playlist-tsearch" placeholder="Search">
	            </div>
	            <div class="s3bubble-media-main-playlist s3bubble-audio-playlist-tracksearch-' . $player_id .  '" style="display:'. (($playlist == 'hidden') ? 'none' : 'block' ) .';">
					<ul class="s3bubble-audio-playlist-ul-' . $player_id .  '">
						<li>&nbsp;</li>
					</ul>
				</div>
			    <div class="s3bubble-media-main-no-solution" style="display:none;">
			        <span>Update Required</span>
			        Flash player is needed to use this player please download here. <a href="https://get2.adobe.com/flashplayer/" target="_blank">Download</a>
			    </div>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					var S3Bucket = "' . $bucket. '";
					var Current = -1;
					var IsMobile = false;				
					if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
						IsMobile = true;
					}
					var audioPlaylistS3Bubble = new jPlayerPlaylist({
		                jPlayer: "#jquery_jplayer_' . $player_id . '",
						cssSelectorAncestor: "#s3bubble-media-main-container-' . $player_id . '"
		            }, audioPlaylistS3Bubble, {
		                playlistOptions: {
		                    autoPlay: '.$autoplay.',
		                    displayTime: 0,
		                    downloadSet: '.$download.',
		                    playerWidth: $(this).width(),
		                    enableRemoveControls: false
		                },
		                ready: function(event) {
							var sendData = {
								"action": "s3bubble_audio_playlist_internal_ajax",
								"Timezone":"America/New_York",
							    "Bucket" : "' . $bucket. '",
							    "Folder" : "' . $folder. '"
							}
							$.post("' . admin_url('admin-ajax.php') . '", sendData, function(response) {
								
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-audio-loading").fadeOut();
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-controls-holder").fadeIn();
								
								if(response.error !== undefined){
									console.log(response.error);
								}else{
									audioPlaylistS3Bubble.setPlaylist(response);
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 280px 0 40px");
									if(IsMobile){
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 150px 0 40px");	
									}
									if(response.user === "s2member_level1" || response.user === "s2member_level2"){
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 320px 0 40px");
										if(IsMobile){
											$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 190px 0 40px");	
										}
										$(".s3bubble-media-main-right-controls").prepend("<a href=\"https://s3bubble.com\" class=\"s3bubble-media-main-logo\"><i id=\"icon-S3\" class=\"icon-S3\"></i></a>");
									}
									// hide playlist 
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-playlist-list").click(function() {
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-playlist").slideToggle();
										return false;
									});
									if ("'.$height.'" !== "") {
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-playlist").css({
											height : "'.$height.'px",
											"overflow-y" : "scroll"
										});
									}
									// Search tracks
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-playlist-search").click(function() {
										if ($("#s3bubble-media-main-container-' . $player_id . ' .s3audible-search-' . $player_id .  '").hasClass("searchOpen")) {
											$("#s3bubble-media-main-container-' . $player_id . ' .s3audible-search-' . $player_id .  '").fadeOut().removeClass("searchOpen");
										} else {
											$("#s3bubble-media-main-container-' . $player_id . ' .s3audible-search-' . $player_id .  '").fadeIn().addClass("searchOpen");
										}
										return false;
									});
									$("#s3bubble-media-main-container-' . $player_id . ' #s3bubble-audio-playlist-tsearch-' . $player_id .  '").keyup(function() {
										var searchText = $(this).val(),
							            $allListElements = $("#s3bubble-media-main-container-' . $player_id . ' ul.s3bubble-audio-playlist-ul-' . $player_id .  ' > li"),
							            $matchingListElements = $allListElements.filter(function(i, el){
							                return $(el).text().toLowerCase().indexOf(searchText.toLowerCase()) !== -1;
							            });
										$allListElements.hide();
		       							$matchingListElements.show();
									});
								}
							},"json");
						},
						timeupdate : function(t) {
							if (t.jPlayer.status.currentTime > 1) {
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut();
							}
						},
						resize: function (event) {
							
					    	
					    },
					    click: function (event) {
	
					    },
					    error: function (event) {
					    	console.log(event.jPlayer.error);
	    					console.log(event.jPlayer.error.type);
					    },
						loadedmetadata: function() {
						
						},
						waiting: function() {
							
						},
						canplay: function() {

						},
						pause: function() { 

						},
						playing: function() {

						},
						play: function() { 
							var CurrentState = audioPlaylistS3Bubble.current;
							var PlaylistKey  = audioPlaylistS3Bubble.playlist[CurrentState];
							if(Current !== CurrentState){
								addListener({
									app_id: s3bubble_all_object.s3appid,
									server: s3bubble_all_object.serveraddress,
									bucket: S3Bucket,
									key: PlaylistKey.key,
									type: "audio",
									advert: false
								});
								Current = CurrentState;
							}
						},
						suspend: function() { 
						    
						},
						stalled: function() { 
						    
						},
						loadstart: function() { 
						    
						},
						keyBindings: {
					        play: {
					            key: 32,
					            fn: function(f) {
					                if (f.status.paused) {
					                    f.play();
					                } else {
					                    f.pause();
					                }
					            }
					        },
					        muted: {
					            key: 77,
					            fn: function(f) {
					                f._muted(!f.options.muted);
					            }
					        },
					        volumeUp: {
					            key: 190,
					            fn: function(f) {
					                f.volume(f.options.volume + 0.1);
					            }
					        },
					        volumeDown: {
					            key: 188,
					            fn: function(f) {
					                f.volume(f.options.volume - 0.1);
					            }
					        },
					        loop: {
					            key: 76,
					            fn: function(f) {
					                f._loop(!f.options.loop);
					            }
					        },
					        goForwardFive: {
					            key: 72,
					            fn: function(f) {
					                f.playHead(f.status.currentPercentAbsolute + 5);
					            }
					        },
					        goBackFive: {
					            key: 66,
					            fn: function(f) {
					                f.playHead(f.status.currentPercentAbsolute - 5);
					            }
					        },
					        loopOn: {
					            key: 49,
					            fn: function(f) {
					                f.options.lon = f.status.currentPercentAbsolute;
					            }
					        },
					        loopOff: {
					            key: 50,
					            fn: function(f) {
					                f.options.loff = f.status.currentPercentAbsolute;
					            }
					        },
					        loopfinish: {
					            key: 51,
					            fn: function(f) {
					            	if (f.options.lfinish) {
					                    f.options.lfinish = false;
					                } else {
					                    f.options.lfinish = true;
					                }
					            }
					        },
					        speedUp: {
					            key: 83,
					            fn: function(f) {
					                f.playbackRate(f.status.playbackRate + 0.1);
					            }
					        },
					        slowDown: {
					            key: 65,
					            fn: function(f) {
					                f.playbackRate(f.status.playbackRate - 0.1);
					            }
					        },
					        normalSpeed: {
					            key: 68,
					            fn: function(f) {
					                f.playbackRate(1);
					            }
					        }
					    },
		                swfPath: "https://s3.amazonaws.com/s3bubble.assets/flash/latest.jplayer.swf",
		                preload: "'.$preload.'",
	                    supplied: "mp3,m4a",
		                wmode: "window",
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
		* Run the s3bubble jplayer single audio function
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_single_player($atts){

			 $s3bubble_access_key = get_option("s3-s3audible_username");
			 $s3bubble_secret_key = get_option("s3-s3audible_email");		
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
			), $atts, 's3bubbleAudioSingle' ) );
			extract( shortcode_atts( array(
				'style'      => 'bar',
				'download'   => 'false',
				'autoplay'   => 'false',
				'preload'    => 'auto',
				'bucket'     => '',
				'track'      => '',
				'cloudfront' => ''
			), $atts, 's3audibleSingle' ) );

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
			    <div id="jquery_jplayer_' . $player_id .  '" class="s3bubble-media-main-jplayer"></div>
			    <div class="s3bubble-media-main-gui">
			        <div class="s3bubble-media-main-interface s3bubble-media-main-interface-audio-playlist">
			        	<div class="s3bubble-media-main-audio-loading">
					    	<i class="s3icon s3icon-circle-o-notch s3icon-spin"></i>
					    </div>
			            <div class="s3bubble-media-main-controls-holder" style="display:none;">
				            <span class="s3bubble-media-main-left-controls">
								<a href="javascript:;" class="s3bubble-media-main-play" tabindex="1"><i class="s3icon s3icon-play"></i></a>
								<a href="javascript:;" class="s3bubble-media-main-pause" tabindex="1"><i class="s3icon s3icon-pause"></i></a>
							</span>
							<div class="s3bubble-media-main-progress" dir="auto">
							    <div class="s3bubble-media-main-seek-bar" dir="auto">
							        <div class="s3bubble-media-main-play-bar" dir="auto"></div>
							    </div>
							</div>
							<span class="s3bubble-media-main-right-controls">
								<div class="s3bubble-media-main-volume-bar" dir="auto">
								    <div class="s3bubble-media-main-volume-bar-value" dir="auto"></div>
								</div>
								<a href="javascript:;" class="s3bubble-media-main-mute" tabindex="2" title="mute"><i class="s3icon s3icon-volume-up"></i></a>
								<a href="javascript:;" class="s3bubble-media-main-unmute" tabindex="2" title="unmute"><i class="s3icon s3icon-volume-off"></i></a>
								<div class="s3bubble-media-main-time-container">
									<div class="s3bubble-media-main-duration"></div>
								</div>
							</span>
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
			jQuery(document).ready(function($) {
				
				var S3Bucket = "' . $bucket. '";
				var Current = -1;
				var aspect = $("#s3bubble-media-main-container-1").width()/16*9;
				var IsMobile = false;
				if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
					IsMobile = true;
				}
					
				var audioSingleS3Bubble = new jPlayerPlaylist({
	                jPlayer: "#jquery_jplayer_' . $player_id .  '",
					cssSelectorAncestor: "#s3bubble-media-main-container-' . $player_id .  '"
	            }, audioSingleS3Bubble, {
	                playlistOptions: {
	                    autoPlay: '.$autoplay.',
	                    displayTime: 0,
	                    downloadSet: '.$download.',
	                    playerWidth: $(this).width(),
	                    enableRemoveControls: false
	                },
	                ready: function(event) {
						var sendData = {
							"action": "s3bubble_audio_single_internal_ajax",
							"Timezone":"America/New_York",
						    "Bucket" : "' . $bucket. '",
						    "Key" : "' . $track . '",
						    "Cloudfront" : "' . $cloudfront . '",
						    "Server" : s3bubble_all_object.serveraddress
						}
						$.post("' . admin_url('admin-ajax.php') . '", sendData, function(response) {
							
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-audio-loading").fadeOut();
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-controls-holder").fadeIn();
							
							if(response.error !== undefined){
								console.log(response.error);
							}else{
								audioSingleS3Bubble.setPlaylist(response);
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 200px 0 40px");
								if(IsMobile){
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 60px 0 40px");	
								}
								if(response.user === "s2member_level1" || response.user === "s2member_level2"){
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 240px 0 40px");
									if(IsMobile){
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 100px 0 40px");	
									}
									$(".s3bubble-media-main-right-controls").prepend("<a href=\"https://s3bubble.com\" class=\"s3bubble-media-main-logo\"><i id=\"icon-S3\" class=\"icon-S3\"></i></a>");
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
					resize: function (event) {

				    },
	                loadedmetadata: function() {
						
					},
					waiting: function() {
		
					},
					canplay: function() {

					},
					pause: function() {

					},
					playing: function() {

					},
					play: function() { 
						var CurrentState = audioSingleS3Bubble.current;
						var PlaylistKey  = audioSingleS3Bubble.playlist[CurrentState];
						if(Current !== CurrentState){
							addListener({
								app_id: s3bubble_all_object.s3appid,
								server: s3bubble_all_object.serveraddress,
								bucket: S3Bucket,
								key: PlaylistKey.key,
								type: "audio",
								advert: false
							});
							Current = CurrentState;
						}
					},
					timeupdate: function(event) { 
	
					},
					suspend: function() { 
					    
					},
					stalled: function() { 
					    
					},
					loadstart: function() { 
					    
					},
					keyBindings: {
				        play: {
				            key: 32,
				            fn: function(f) {
				                if (f.status.paused) {
				                    f.play();
				                } else {
				                    f.pause();
				                }
				            }
				        },
				        muted: { 
				            key: 77,
				            fn: function(f) {
				                f._muted(!f.options.muted);
				            }
				        },
				        volumeUp: {
				            key: 190,
				            fn: function(f) {
				                f.volume(f.options.volume + 0.1);
								$(".single-audio-volume-' . $player_id .  '").val(f.options.volume + 0.1);
				            }
				        },
				        volumeDown: {
				            key: 188,
				            fn: function(f) {
				                f.volume(f.options.volume - 0.1);
								$(".single-audio-volume-' . $player_id .  '").val(f.options.volume - 0.1);
				            }
				        },
				        loop: {
				            key: 76,
				            fn: function(f) {
				                f._loop(!f.options.loop);
				            }
				        },
				        goForwardFive: {
				            key: 72,
				            fn: function(f) {
				                f.playHead(f.status.currentPercentAbsolute + 5);
				            }
				        },
				        goBackFive: {
				            key: 66,
				            fn: function(f) {
				                f.playHead(f.status.currentPercentAbsolute - 5);
				            }
				        },
				        loopOn: {
				            key: 49,
				            fn: function(f) {
				                f.options.lon = f.status.currentPercentAbsolute;
				            }
				        },
				        loopOff: {
				            key: 50,
				            fn: function(f) {
				                f.options.loff = f.status.currentPercentAbsolute;
				            }
				        },
				        loopfinish: {
				            key: 51,
				            fn: function(f) {
				            	if (f.options.lfinish) {
				                    f.options.lfinish = false;
				                } else {
				                    f.options.lfinish = true;
				                }
				            }
				        },
				        speedUp: {
				            key: 83,
				            fn: function(f) {
				                f.playbackRate(f.status.playbackRate + 0.1);
				            }
				        },
				        slowDown: {
				            key: 65,
				            fn: function(f) {
				                f.playbackRate(f.status.playbackRate - 0.1);
				            }
				        },
				        normalSpeed: {
				            key: 68,
				            fn: function(f) {
				                f.playbackRate(1);
				            }
				        }
				    },
	                swfPath: "https://s3.amazonaws.com/s3bubble.assets/flash/latest.jplayer.swf",
                    supplied: "mp3,m4a,wav",
	                wmode: "window",
	                preload: "metadata",
					useStateClassSkin: true,
					autoBlur: false,
					smoothPlayBar: false,
					keyEnabled: true,
					audioFullScreen: false,
					remainingDuration: true
	            });
			});
			</script>';

		}
        
		/*
		* Run the s3bubble jplayer video playlist function
		* @author sameast
		* @none
		*/ 
        function s3bubble_video_player($atts){
	        
			/*
			 * Player options
			 */ 
			$loggedin           = get_option("s3-loggedin");
			$search             = get_option("s3-search");
			$responsive         = get_option("s3-responsive");
			$stream             = get_option("s3-stream");
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
            $player_id = uniqid();
            
            return '<div id="s3bubble-media-main-container-' . $player_id . '" class="s3bubble-media-main-video">
            	<div class="s3bubble-media-main-video-playlist-wrap">
				    <div id="jquery_jplayer_' . $player_id . '" class="s3bubble-media-main-jplayer"></div>
				    <div class="s3bubble-media-main-video-skip">
						<h2>Skip Ad</h2>
						<i class="s3icon s3icon-step-forward"></i>
						<img id="s3bubble-media-main-skip-tumbnail" src=""/>
					</div>
					<div class="s3bubble-media-main-video-search">
						<input type="text" id="s3bubble-video-playlist-tsearch-' . $player_id .  '" class="s3bubble-video-playlist-tsearch" name="s3bubble-video-playlist-tsearch" placeholder="Search">
					</div>
				    <div class="s3bubble-media-main-video-loading">
				    	<i class="s3icon s3icon-circle-o-notch s3icon-spin"></i>
				    </div>
				    <div class="s3bubble-media-main-video-play">
						<i class="s3icon s3icon-play"></i>
					</div>
				    <div class="s3bubble-media-main-gui" style="visibility: hidden;">
				        <div class="s3bubble-media-main-interface">
				            <div class="s3bubble-media-main-controls-holder">
					            <span class="s3bubble-media-main-left-controls">
									<a href="javascript:;" class="s3bubble-media-main-play" tabindex="1"><i class="s3icon s3icon-play"></i></a>
									<a href="javascript:;" class="s3bubble-media-main-pause" tabindex="1"><i class="s3icon s3icon-pause"></i></a>
								</span>
								<div class="s3bubble-media-main-progress" dir="auto">
								    <div class="s3bubble-media-main-seek-bar" dir="auto">
								        <div class="s3bubble-media-main-play-bar" dir="auto"></div>
								    </div>
								</div>
								<span class="s3bubble-media-main-right-controls">
									<a href="javascript:;" class="s3bubble-media-main-full-screen" tabindex="3" title="full screen"><i class="s3icon s3icon-arrows-alt"></i></a>
									<a href="javascript:;" class="s3bubble-media-main-restore-screen" tabindex="3" title="restore screen"><i class="s3icon s3icon-arrows-alt"></i></a>
									<a href="javascript:;" class="s3bubble-media-main-playlist-list" tabindex="3" title="Playlist List"><i class="s3icon s3icon-list-ul"></i></a>
									<a href="javascript:;" class="s3bubble-media-main-playlist-search" tabindex="3" title="Search List"><i class="s3icon s3icon-search"></i></a>
									<div class="s3bubble-media-main-volume-bar" dir="auto">
									    <div class="s3bubble-media-main-volume-bar-value" dir="auto"></div>
									</div>
									<a href="javascript:;" class="s3bubble-media-main-mute" tabindex="2" title="mute"><i class="s3icon s3icon-volume-up"></i></a>
									<a href="javascript:;" class="s3bubble-media-main-unmute" tabindex="2" title="unmute"><i class="s3icon s3icon-volume-off"></i></a>
									<div class="s3bubble-media-main-time-container">
										<div class="s3bubble-media-main-duration"></div>
									</div>
								</span>
				            </div>
				        </div>
				    </div>
			    </div>
			    <div class="s3bubble-media-main-playlist" style="' . (($playlist == 'show') ? "" : "display:none;" ) . '">
					<ul class="s3bubble-video-playlist-ul-' . $player_id .  '">
						<li></li>
					</ul>
				</div>
			    <div class="s3bubble-media-main-no-solution" style="display:none;">
			        <span>Update Required</span>
			        Flash player is needed to use this player please download here. <a href="https://get2.adobe.com/flashplayer/" target="_blank">Download</a>
			    </div>
			</div>
            <script type="text/javascript">
			jQuery(document).ready(function($) {
				
				var S3Bucket = "' . $bucket. '";
				var Current = -1;
				var aspects  = "' . $aspect . '";
				var aspects = aspects.split(":");
				var aspect = $("#s3bubble-media-main-container-' . $player_id . '").width()/aspects[0]*aspects[1];
				var IsMobile = false;
				if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
					IsMobile = true;
				}
				$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-playlist-wrap").height(aspect);

				var videoPlaylistS3Bubble = new jPlayerPlaylist({
					jPlayer: "#jquery_jplayer_' . $player_id . '",
					cssSelectorAncestor: "#s3bubble-media-main-container-' . $player_id . '"
				}, videoPlaylistS3Bubble, {
					playlistOptions : {
						autoPlay : '.$autoplay.',
						downloadSet: '.$download.'
					},
					ready : function(event) {
						var sendData = {
							"action": "s3bubble_video_playlist_internal_ajax",
							"Timezone":"America/New_York",
						    "Bucket" : "' . $bucket. '",
						    "Folder" : "' . $folder. '"
						}
						$.post("' . admin_url('admin-ajax.php') . '", sendData, function(response) {
							if(response.error !== undefined){
								console.log(response.error);
							}else{
								videoPlaylistS3Bubble.setPlaylist(response);
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 320px 0 40px");
								if(IsMobile){
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 160px 0 40px");	
								}
								if(response.user === "s2member_level1" || response.user === "s2member_level2"){
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 360px 0 40px");
									if(IsMobile){
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 200px 0 40px");	
									}
									$(".s3bubble-media-main-right-controls").prepend("<a href=\"https://s3bubble.com\" class=\"s3bubble-media-main-logo\"><i id=\"icon-S3\" class=\"icon-S3\"></i></a>");
								}
								$("video").bind("contextmenu", function(e) {
									return false
								});
								//hide playlist
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-playlist-list").click(function() {
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-playlist").slideToggle();
									return false;
								});
								//Search tracks
								$( "#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-playlist-search" ).click(function() {
									videoPlaylistS3Bubble.pause();
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-play").fadeOut();
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-search").fadeIn();
									return false;
								});
								$("#s3bubble-video-playlist-tsearch-' . $player_id .  '").keyup(function() {
									var searchText = $(this).val(),
						            $allListElements = $("#s3bubble-media-main-container-' . $player_id . ' ul.s3bubble-video-playlist-ul-' . $player_id .  ' > li"),
						            $matchingListElements = $allListElements.filter(function(i, el){
						                return $(el).text().toLowerCase().indexOf(searchText.toLowerCase()) !== -1;
						            });
									$allListElements.hide();
	       							$matchingListElements.show();
								});
								setTimeout(function(){
									if ("'.$height.'" !== "") {
										$(".s3bubble-video-playlist-ul-' . $player_id .  '").css({
											height : "'.$height.'px",
											"overflow-y" : "scroll"
										});
									}
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut();
									$(".s3bubble-media-main-gui").css("visibility", "visible");
								},2000);
							}
						},"json");

					},
					timeupdate : function(t) {
						if (t.jPlayer.status.currentTime > 1) {
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut();
						}
					},
					resize: function (event) {

				    },
				    click: function (event) {
				    	if(event.jPlayer.status.paused){
				    		videoPlaylistS3Bubble.play();
				    	}else{
				    		videoPlaylistS3Bubble.pause();
				    	}
				    },
				    error: function (event) {
				    	console.log(event.jPlayer.error);
    					console.log(event.jPlayer.error.type);
				    }, 
					loadedmetadata : function(t) {
						$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut();
					},
					loadeddata : function(t) {
						$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut();
					},
					emptied : function(t) {
						$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeIn()
					},
					ended : function(t) {
						$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeIn()
					},
					stalled : function(t) {
						$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeIn()
					},
					waiting: function() {
						$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeIn(); 
					},
					canplay: function() {
						$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut(); 
					},
					pause: function() { 

					},
					playing: function() {
						$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut();
						$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-search").fadeOut();
						// Reset search
						$("#s3bubble-video-playlist-tsearch-' . $player_id . '").removeAttr("value");
						$("#s3bubble-media-main-container-' . $player_id . ' ul.s3bubble-video-playlist-ul-' . $player_id .  ' > li").show(); 
					},
					play: function() {
						$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-search").fadeOut();
						$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut(); 
						var CurrentState = videoPlaylistS3Bubble.current;
						var PlaylistKey  = videoPlaylistS3Bubble.playlist[CurrentState];
						if(IsMobile === false){
							if(PlaylistKey.advert){
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-skip").animate({
								    left: "0"
								}, 50, function() {
								    // Animation complete.
								});
							}
						}
						if(Current !== CurrentState && PlaylistKey.advert !== true){
							addListener({
								app_id: s3bubble_all_object.s3appid,
								server: s3bubble_all_object.serveraddress,
								bucket: S3Bucket,
								key: PlaylistKey.key,
								type: "video",
								advert: false
							});
							Current = CurrentState;
						}
					},
					suspend: function() { 
					    
					},
					stalled: function() { 
					    
					},
					loadstart: function() { 
					    
					},
					keyBindings: {
					  play: {
					    key: 32,
					    fn: function(f) {
					      if(f.status.paused) {
					        f.play();
					      } else {
					        f.pause();
					      }
					    }
					  },
					  fullScreen: {
					    key: 70,
					    fn: function(f) {
					      if(f.status.video || f.options.audioFullScreen) {
					        f._setOption("fullScreen", !f.options.fullScreen);
					      }
					    }
					  },
					  muted: {
					    key: 77,
					    fn: function(f) {
					      f._muted(!f.options.muted);
					    }
					  },
					  volumeUp: {
					    key: 190,
					    fn: function(f) {
					      f.volume(f.options.volume + 0.1);
					    }
					  },
					  volumeDown: {
					    key: 188,
					    fn: function(f) {
					      f.volume(f.options.volume - 0.1);
					    }
					  },
					  loop: {
					    key: 76,
					    fn: function(f) {
					      f._loop(!f.options.loop);
					    }
					  }
					},
					swfPath: "https://s3.amazonaws.com/s3bubble.assets/flash/latest.jplayer.swf",
                    supplied: "m4v",
	                wmode: "window",
	                preload: "metadata",
					useStateClassSkin: true,
					autoBlur: false,
					smoothPlayBar: false,
					keyEnabled: true,
					remainingDuration: true,
					size: {
			            width: "100%",
			            height: aspect
			        },
			        autohide : {
						full : true,
						restored : true,
						hold : 3000
					}
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
			
			// get option from database	
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$responsive          = get_option("s3-responsive");
			$stream              = get_option("s3-stream");
	        extract( shortcode_atts( array(
	        	'download'   => 'false',
				'aspect'     => '16:9',
				'responsive' => $responsive,
				'autoplay'   => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3bubbleVideoSingle' ) );
			extract( shortcode_atts( array(
				'download'   => 'false',
				'aspect'     => '16:9',
				'responsive' => $responsive,
				'autoplay'   => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3videoSingle' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
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
            $player_id = uniqid();
		
            return '<div id="s3bubble-media-main-container-' . $player_id . '" class="s3bubble-media-main-video">
			    <div id="jquery_jplayer_' . $player_id . '" class="s3bubble-media-main-jplayer"></div>
			    <div class="s3bubble-media-main-video-skip">
					<h2>Skip Ad</h2>
					<i class="s3icon s3icon-step-forward"></i>
					<img id="s3bubble-media-main-skip-tumbnail" src=""/>
				</div>
			    <div class="s3bubble-media-main-video-loading">
			    	<i class="s3icon s3icon-circle-o-notch s3icon-spin"></i>
			    </div>
			    <div class="s3bubble-media-main-video-play">
					<i class="s3icon s3icon-play"></i>
				</div>
			    <div class="s3bubble-media-main-gui" style="visibility: hidden;">
			        <div class="s3bubble-media-main-interface">
			            <div class="s3bubble-media-main-controls-holder">
			            	<span class="s3bubble-media-main-left-controls">
								<a href="javascript:;" class="s3bubble-media-main-play" tabindex="1"><i class="s3icon s3icon-play"></i></a>
								<a href="javascript:;" class="s3bubble-media-main-pause" tabindex="1"><i class="s3icon s3icon-pause"></i></a>
							</span>
							<div class="s3bubble-media-main-progress" dir="auto">
							    <div class="s3bubble-media-main-seek-bar" dir="auto">
							        <div class="s3bubble-media-main-play-bar" dir="auto"></div>
							    </div>
							</div>
							<span class="s3bubble-media-main-right-controls">
								<a href="javascript:;" class="s3bubble-media-main-full-screen" tabindex="3" title="full screen"><i class="s3icon s3icon-arrows-alt"></i></a>
								<a href="javascript:;" class="s3bubble-media-main-restore-screen" tabindex="3" title="restore screen"><i class="s3icon s3icon-arrows-alt"></i></a>
								<div class="s3bubble-media-main-volume-bar" dir="auto">
								    <div class="s3bubble-media-main-volume-bar-value" dir="auto"></div>
								</div>
								<a href="javascript:;" class="s3bubble-media-main-mute" tabindex="2" title="mute"><i class="s3icon s3icon-volume-up"></i></a>
								<a href="javascript:;" class="s3bubble-media-main-unmute" tabindex="2" title="unmute"><i class="s3icon s3icon-volume-off"></i></a>
								<div class="s3bubble-media-main-time-container">
									<div class="s3bubble-media-main-duration"></div>
								</div>
							</span>
			            </div>
			        </div>
			    </div>
			    <div class="s3bubble-media-main-playlist" style="display:none !important;">
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
				jQuery(document).ready(function($) {
					
					var S3Bucket = "' . $bucket. '";
					var Current = -1;
					var aspects  = "' . $aspect . '";
					var aspects = aspects.split(":");
					var aspect = $("#s3bubble-media-main-container-' . $player_id . '").width()/aspects[0]*aspects[1];
					var IsMobile = false;
					if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
						IsMobile = true;
					}

					var videoSingleS3Bubble = new jPlayerPlaylist({
						jPlayer: "#jquery_jplayer_' . $player_id . '",
						cssSelectorAncestor: "#s3bubble-media-main-container-' . $player_id . '"
					}, videoSingleS3Bubble, {
						playlistOptions : {
							autoPlay : '.$autoplay.',
							downloadSet: '.$download.'
						},
						ready : function(event) {
							var sendData = {
								"action": "s3bubble_video_single_internal_ajax",
								"Timezone":"America/New_York",
							    "Bucket" : "' . $bucket. '",
							    "Key" : "' . $track. '",
							    "Cloudfront" : "' . $cloudfront .'",
							    "Server" : s3bubble_all_object.serveraddress
							}
							$.post("' . admin_url('admin-ajax.php') . '", sendData, function(response) {
								if(response.error){
									console.log(response.message);   
								}else{
									videoSingleS3Bubble.setPlaylist(response.results);	

									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 240px 0 40px");	
									if(IsMobile){
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 60px 0 40px");	
									}
									if(response.user === "s2member_level1" || response.user === "s2member_level2"){
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 280px 0 40px");
										if(IsMobile){
											$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").css("margin","12px 100px 0 40px");	
										}
										$(".s3bubble-media-main-right-controls").prepend("<a href=\"https://s3bubble.com\" class=\"s3bubble-media-main-logo\"><i id=\"icon-S3\" class=\"icon-S3\"></i></a>");
									}
									$("#s3bubble-media-main-container-' . $player_id . ' #s3bubble-media-main-skip-tumbnail").attr("src", response.results[0].poster);
									$("video").bind("contextmenu", function(e) {
										return false;
									}); 
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-skip").on( "click", function() {
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeIn();
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-skip").animate({
										    left: "-230"
										}, 50, function() {
										    videoSingleS3Bubble.next();
										});
									});
									setTimeout(function(){
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut();
										$(".s3bubble-media-main-gui").css("visibility", "visible");
									},2000);
									
								}
							},"json");
						},
						timeupdate : function(t) {
							if (t.jPlayer.status.currentTime > 1) {
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut();
							}
						},
						resize: function (event) {

					    },
					    click: function (event) {
					    	if(event.jPlayer.status.paused){
					    		videoSingleS3Bubble.play();
					    	}else{
					    		videoSingleS3Bubble.pause();
					    	}
					    },
					    error: function (event) {
					    	console.log(event.jPlayer.error);
        					console.log(event.jPlayer.error.type);
					    },
						loadedmetadata : function(t) {
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut();
						},
						loadeddata : function(t) {
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut();
						},
						emptied : function(t) {
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeIn()
						},
						ended : function(t) {
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeIn();
							var CurrentState = videoSingleS3Bubble.current;
							var PlaylistKey  = videoSingleS3Bubble.playlist[CurrentState];
							if(PlaylistKey.advert && IsMobile === false){
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-skip").animate({
								    left: "0"
								}, 50, function() {
								    // Animation complete.
								});
							}else{
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-skip").animate({
								    left: "-230"
								}, 50, function() {
								    
								});
							}
						},
						stalled : function(t) {
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeIn()
						},
						waiting: function() {
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeIn(); 
						},
						canplay: function() {
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut(); 
						},
						pause: function() { 

						},
						playing: function() {
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut(); 
						},
						play: function() { 
						    $("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut(); 
							var CurrentState = videoSingleS3Bubble.current;
							var PlaylistKey  = videoSingleS3Bubble.playlist[CurrentState];
							if(PlaylistKey.advert && IsMobile === false){
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-skip").animate({
								    left: "0"
								}, 50, function() {
								    // Animation complete.
								});
							}
							if(Current !== CurrentState && PlaylistKey.advert !== true){
								addListener({
									app_id: s3bubble_all_object.s3appid,
									server: s3bubble_all_object.serveraddress,
									bucket: "' . $bucket. '",
									key: PlaylistKey.key,
									type: "video",
									advert: false
								});
								Current = CurrentState;
							}

						},
						suspend: function() { 
						    
						},
						keyBindings: {
						  play: {
						    key: 32,
						    fn: function(f) {
						      if(f.status.paused) {
						        f.play();
						      } else {
						        f.pause();
						      }
						    }
						  },
						  fullScreen: {
						    key: 70,
						    fn: function(f) {
						      if(f.status.video || f.options.audioFullScreen) {
						        f._setOption("fullScreen", !f.options.fullScreen);
						      }
						    }
						  },
						  muted: {
						    key: 77,
						    fn: function(f) {
						      f._muted(!f.options.muted);
						    }
						  },
						  volumeUp: {
						    key: 190,
						    fn: function(f) {
						      f.volume(f.options.volume + 0.1);
						    }
						  },
						  volumeDown: {
						    key: 188,
						    fn: function(f) {
						      f.volume(f.options.volume - 0.1);
						    }
						  },
						  loop: {
						    key: 76, 
						    fn: function(f) {
						      f._loop(!f.options.loop);
						    }
						  }
						},
						swfPath: "https://s3.amazonaws.com/s3bubble.assets/flash/latest.jplayer.swf",
	                    supplied: "m4v",
		                wmode: "window",
		                preload: "metadata",
						useStateClassSkin: true,
						autoBlur: false,
						smoothPlayBar: false,
						keyEnabled: true,
						remainingDuration: true,
						size: {
				            width: "100%",
				            height: aspect
				        },
				        autohide : {
							full : true,
							restored : true,
							hold : 3000
						}
					});
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