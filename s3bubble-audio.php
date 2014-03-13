<?php
/*
Plugin Name: S3bubble Cloud Media Streaming Amazon S3 Cloudfront
Plugin URI: https://www.s3bubble.com/
Description: S3Bubble offers simple, secure media streaming from Amazon S3 to WordPress. In just 3 simple steps. 
Version: 1.6.8
Author: S3Bubble
Author URI: https://s3bubble.com/
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

		// property declaration
        public $s3audible_username = '';
		public $s3audible_email = '';
		public $bucket          = '';
		public $folder          = '';
		public $colour          = '#fff';
		public $width           = '100%';
		public $autoplay        = 'yes';
		public $jtoggle		    = 'true';
		public $s3bubble_share  = 'false';
		public $download		= 'false';
		public $loggedin        = 'false';
		public $search          = 'false';
		public $solution        = 'flash,html';
		public $responsive      = '360p';
		public $theme           = 's3bubble_clean';
		public $stream          = 'm4v';
		
	    function s3bubble_audio() { //constructor	
			$this->__construct();
		}
		
		function __construct(){
			
			// Put our defaults in the "wp-options" table
			add_option("s3-s3audible_username", $this->s3audible_username);
			add_option("s3-s3audible_email", $this->s3audible_email);
			add_option("s3-bucket", $this->bucket);
			add_option("s3-folder", $this->folder); 
			add_option("s3-colour", $this->colour);
			add_option("s3-width", $this->width);
			add_option("s3-autoplay", $this->autoplay);
			add_option("s3-jtoggle", $this->jtoggle);
			add_option("s3-download", $this->download);
			add_option("s3-loggedin", $this->loggedin);
			add_option("s3-search", $this->search);
			add_option("s3-solution", $this->solution);
			add_option("s3-responsive", $this->responsive);
			add_option("s3-theme", $this->theme);
			add_option("s3-stream", $this->stream);
			add_option("s3-s3bubble_share", $this->s3bubble_share);
			
			add_action('admin_menu', array( $this, 's3bubble_audio_admin_menu' ));
			add_action( 'wp_head', array( $this, 's3bubble_audio_css' ) );
			add_action( 'wp_footer', array( $this, 's3bubble_audio_javascript' ) );
			add_action( 'admin_head', array( $this, 's3bubble_audio_css_admin' ) );
			add_action( 'admin_footer', array( $this, 's3bubble_audio_javascript_admin' ) );
			add_shortcode( 's3bubbleAudio', array( $this, 's3bubble_audio_player' ) );
			add_shortcode( 's3bubbleAudioSingle', array( $this, 's3bubble_audio_single_player' ) );
			add_shortcode( 's3bubbleVideo', array( $this, 's3bubble_video_player' ) );
			add_shortcode( 's3bubbleVideoSingle', array( $this, 's3bubble_video_single_player' ) );
			// Legacy shortcode
			add_shortcode( 's3audible', array( $this, 's3bubble_audio_player' ) );
			add_shortcode( 's3audibleSingle', array( $this, 's3bubble_audio_single_player' ) );	
			add_shortcode( 's3video', array( $this, 's3bubble_video_player' ) );	
			add_shortcode( 's3videoSingle', array( $this, 's3bubble_video_single_player' ) );			
			
		} // function
		
		// include css
		function s3bubble_audio_css(){
			// Styles
		   	$colour	= get_option("s3-colour");    
			$theme = get_option("s3-theme");
			wp_register_style( 'font-awesome.min', plugins_url('assets/css/fa/font-awesome.min.css', __FILE__), array(), 7 );
			wp_enqueue_style('font-awesome.min');
			wp_register_style( 's3bubble-style-default', plugins_url('assets/css/default.css', __FILE__), array(), 7 );
			wp_enqueue_style('s3bubble-style-default');
			if($theme == 's3bubble_default'){
				wp_register_style( 's3bubble-style-style', plugins_url('assets/css/style.css', __FILE__), array(), 7 );
			    wp_enqueue_style('s3bubble-style-style');
			}else if($theme == 's3bubble_light'){
				wp_register_style( 's3bubble-style-light', plugins_url('assets/css/light.css', __FILE__), array(), 7 );
			    wp_enqueue_style('s3bubble-style-light');
			}else if($theme == 's3bubble_sound'){
				wp_register_style( 's3bubble-style-sound', plugins_url('assets/css/sound.css', __FILE__), array(), 7 );
			    wp_enqueue_style('s3bubble-style-sound');
			}else if($theme == 's3bubble_clean'){
				wp_register_style( 's3bubble-style-clean', plugins_url('assets/css/clean.css', __FILE__), array(), 7 );
			    wp_enqueue_style('s3bubble-style-clean');
				echo "<link href='//fonts.googleapis.com/css?family=Signika' rel='stylesheet' type='text/css'>";
			}else{
				wp_register_style( 's3bubble-style-default', plugins_url('assets/css/style.css', __FILE__), array(), 7 );
			    wp_enqueue_style('s3bubble-style-default');
			}
			// updated css
		    echo '<style type="text/css">
					.s3bubblePlayer a > * {color: '.stripcslashes($colour).' !important;}
					.s3-play-bar {background-color: '.stripcslashes($colour).' !important;}
					.s3-current-time, .s3-duration, .s3-time-seperator, .s3-playlist ul li a.s3-playlist-current {color: '.stripcslashes($colour).' !important;}
					}   
			</style>';
		}
		// include css
		function s3bubble_audio_css_admin(){
			wp_register_style( 'colorpicker', plugins_url('assets/css/colorpicker.css', __FILE__) );
			wp_enqueue_style('colorpicker');
		}
		
		// include javascript
		function s3bubble_audio_javascript(){
            wp_deregister_script( 'jquery' );
            wp_register_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
            wp_enqueue_script('jquery');
            wp_register_script( 'jquery-migrate', plugins_url('assets/js/jquery-migrate-1.2.1.min.js',__FILE__ ), array(), 7 );
            wp_enqueue_script('jquery-migrate');
			wp_register_script( 's3bubble.min', plugins_url('assets/js/s3audible.min.js',__FILE__ ), array(), 7 );
            wp_enqueue_script('s3bubble.min'); 
		}
		
		// include javascript
		function s3bubble_audio_javascript_admin(){
			wp_register_script( 's3bubble-colorpicker', plugins_url('assets/js/colorpicker.js',__FILE__ ));
            wp_enqueue_script('s3bubble-colorpicker');
			wp_register_script( 's3bubble-colorpicker-admin', plugins_url('assets/js/admin.js',__FILE__ ));
            wp_enqueue_script('s3bubble-colorpicker-admin');
		}
		
		function s3bubble_audio_admin_menu(){	
			add_menu_page( 's3bubble_audio', 'S3Bubble Media', 'manage_options', 's3bubble_audio', array($this, 's3bubble_audio_admin'), plugins_url('assets/images/wps3icon.png',__FILE__ ) );
    	}
		
		function s3bubble_audio_admin(){	
			if ( isset($_POST['submit']) ) {
				$nonce = $_REQUEST['_wpnonce'];
				if (! wp_verify_nonce($nonce, 'isd-updatesettings') ) die('Security check failed'); 
				if (!current_user_can('manage_options')) die(__('You cannot edit the search-by-category options.'));
				check_admin_referer('isd-updatesettings');	
				// Get our new option values
				$s3audible_username	= $_POST['s3audible_username'];
				$s3audible_email	= $_POST['s3audible_email'];
				$colour				= addslashes($_POST['colour']);
				$download			= addslashes($_POST['download']);
				$loggedin			= addslashes($_POST['loggedin']);
				//$search			    = addslashes($_POST['search']);
				$solution			= addslashes($_POST['solution']);
				$responsive			= addslashes($_POST['responsive']);
				$theme			    = addslashes($_POST['theme']);
				$s3bubble_share	    = addslashes($_POST['s3bubble_share']);
				
			    // Update the DB with the new option values
				update_option("s3-s3audible_username", mysql_real_escape_string($s3audible_username));
				update_option("s3-s3audible_email", mysql_real_escape_string($s3audible_email));
				update_option("s3-colour", mysql_real_escape_string($colour));
				update_option("s3-download", mysql_real_escape_string($download));
				update_option("s3-loggedin", mysql_real_escape_string($loggedin));
				//update_option("s3-search", mysql_real_escape_string($search));
				update_option("s3-solution", mysql_real_escape_string($solution));
				update_option("s3-responsive", mysql_real_escape_string($responsive));
				update_option("s3-theme", mysql_real_escape_string($theme));
				update_option("s3-s3bubble_share", mysql_real_escape_string($s3bubble_share));
			}
			
			$s3audible_username	= get_option("s3-s3audible_username");
			$s3audible_email	= get_option("s3-s3audible_email");
			$colour				= get_option("s3-colour");
			$download	        = get_option("s3-download");	
			$loggedin			= get_option("s3-loggedin");			
			$search			    = get_option("s3-search");
			$solution			= get_option("s3-solution");
			$responsive			= get_option("s3-responsive");
			$theme			    = get_option("s3-theme");
			$stream			    = get_option("s3-stream");
			$s3bubble_share	    = get_option("s3-s3bubble_share");
?>
<style>
			.s3bubble-pre {
				white-space: pre-wrap; /* css-3 */
				white-space: -moz-pre-wrap; /* Mozilla, since 1999 */
				white-space: -pre-wrap; /* Opera 4-6 */
				white-space: -o-pre-wrap; /* Opera 7 */
				word-wrap: break-word; /* Internet Explorer 5.5+ */
				background: #202020;
				padding: 15px;
				color: white;
			}
		</style>
<div class="wrap">
	
	<div id="icon-options-general" class="icon32"></div>
	<h2>S3Bubble Amazon S3 Media Cloud Media Streaming</h2>
	<h3><span>Manage all your media audio and video in one place. Listen watch and upload with your mobile. <a href="https://itunes.apple.com/us/app/s3bubble/id720256052?ls=1&mt=8" target="_blank">iPhone App</a> ~ <a href="https://play.google.com/store/apps/details?id=com.s3bubbleAndroid&hl=en_GB" target="_blank">Android App</a></span></h3>
	<div class="metabox-holder has-right-sidebar">
		
		
		<div class="inner-sidebar" style="width:50%">
			
			<div class="postbox">
				<h3><span>TUTORIAL SECTION</span></h3>
				<div class="inside">
					<a class="button button-primary button-hero" target="_blank" href="https://www.youtube.com/watch?v=lPoMKEQ4lng">WATCH TUTORIAL VIDEO</a>
				    <a style="float: right;" class="button button-primary button-hero" target="_blank" href="https://www.youtube.com/watch?v=dZZ8Ytmbc1U">FREE AMAZON S3 SETUP</a>
				
				</div> 
			</div>
                 <div class="postbox">
                 	 <h3 style="color: #31708f;background-color: #d9edf7;border-color: #bce8f1;padding: 15px;margin-bottom: 20px;border: 1px solid transparent;border-radius: 4px;">Stuck? this can be grabbed from your s3bubble account it will be auto generated for you. Why not checkout our growing <a href="https://s3bubble.com/forums/" target="_blank" title="S3bubble community forum">Community Forum</a>.</h3> 
				<h3><span>Audio Playlist Shortcode Example - These are auto generated within your s3bubble admin</span></h3>
				<div class="inside">
					<pre class="s3bubble-pre">[s3bubbleAudio bucket="enter-your-bucket" folder="enter-your-bucket-folder"]</pre>
				</div>
				<h3><span>Audio Single Player Shortcode Example - These are auto generated within your s3bubble admin</span></h3>
				<div class="inside">
					<pre class="s3bubble-pre">[s3bubbleAudioSingle bucket="enter-your-bucket" track="enter-your-track-name"]</pre>
				</div>
				<h3><span>Video Playlist Shortcode Example - These are auto generated within your s3bubble admin</span></h3>
				<div class="inside">
					<pre class="s3bubble-pre">[s3bubbleVideo bucket="enter-your-bucket" folder="enter-your-bucket-folder"]</pre>
				</div>
				<h3><span>Video Single Player Shortcode Example - These are auto generated within your s3bubble admin</span></h3>
				<div class="inside">
					<pre class="s3bubble-pre">[s3bubbleVideoSingle bucket="enter-your-bucket" track="enter-your-track-name"]</pre>
				</div>
				<h3><span>RTMP Shortcode Example</span></h3>
				<div class="inside">
					<pre class="s3bubble-pre">[s3bubbleVideo bucket="enter-your-bucket" folder="enter-your-bucket-folder" cloudfront="enter-your-cloudfront.net-url"]</pre>
				</div>
				<div class="inside">
					<h3><span>Params - these can be added to the shortcode</span></h3>
					<p>bucket: //your amazon bucket<br>
						folder: //your amazon s3 folder<br>
						autoplay: //true or false<br>
					    height: //height of the player<br>
					    playlist: //hidden<br>
					    order: //default asc can be desc<br>
					    style: //plain - this will remove the bar on the single player<br>
					    cloudfront: //cloudfront streaming url<br>
					    share: //show share links<br>
					    download: //show download links<br>
					    solution: //can be set to flash or html<br>
					    <p>
				</div>
	            <div class="inside">
	            	<h3><span>Legacy - old plugin versions</span></h3>
					<a href="https://isdcloud.s3.amazonaws.com/s3bubble_backups/s3bubble_amazon_s3_audio_streaming.zip">Download Last Plugin Version</a>
			    </div>
			</div>
 
		</div> <!-- .inner-sidebar -->
 
		<div id="post-body">
			<div id="post-body-content" style="margin-right: 51%;">
				<div class="postbox">
					<h3 style="color: #31708f;background-color: #d9edf7;border-color: #bce8f1;padding: 15px;margin-bottom: 20px;border: 1px solid transparent;border-radius: 4px;">Stuck? if you would like us to get you started and set you up with a audio playlist and embed video in your posts please just <a href="mailto:support@S3Bubble.com" target="_blank">contact us</a>.</h3>
					<h3><span>Please sign up for an account at <a href="https://s3bubble.com/auth/?action=register&utm_source=wordpress&utm_medium=link&utm_campaign=pluginpage" target="_blank">https://s3bubble.com</a> you will need to use the username and email you signed up with.</span></h3>
					<div class="inside">
						<form action="" method="post" id="isd-config" style="overflow: hidden;">
						    <table class="form-table">
						      <?php if (function_exists('wp_nonce_field')) { wp_nonce_field('isd-updatesettings'); } ?>
						       <tr>
						        <h3>Global S3Bubble Settings</h3>
						      </tr> 
						       <tr>
						        <th scope="row" valign="top"><label for="S3Bubble_username">S3Bubble Username:</label></th>
						        <td><input type="password" name="s3audible_username" id="s3audible_username" class="regular-text" value="<?php echo $s3audible_username; ?>"/>
						        	<br />
						       <span class="description">Username you signed up to S3Bubble.com found <a href="http://s3bubble.com/admin/#/profile" target="_blank">here</a></span>	
						        </td>
						      </tr> 
						       <tr>
						        <th scope="row" valign="top"><label for="s3audible_email">S3Bubble Email:</label></th>
						        <td><input type="password" name="s3audible_email" id="s3audible_email" class="regular-text" value="<?php echo $s3audible_email; ?>"/>
						        	<br />
						        	<span class="description">Email you signed up to S3Bubble.com found <a href="http://s3bubble.com/admin/#/profile" target="_blank">here</a></span>
						        </td>
						      </tr> 
						      <tr>
						        <th scope="row" valign="top"><label for="colour">Brand Color:</label></th>
						        <td><input type="text" name="colour" id="colour" class="regular-text" value="<?php echo $colour; ?>"/>
						        	<br />
						        	<span class="description">Sets the brand colour for the player.</p>
						        </td>
						      </tr>
						      <tr>
						        <th scope="row" valign="top"><label for="search">Player Theme:</label></th>
						        <td><select name="theme" id="theme">
						            <option value="<?php echo $theme; ?>"><?php echo $theme; ?></option>
						            <option value="s3bubble_default">default</option>
						            <option value="s3bubble_light">light</option>
						            <option value="s3bubble_sound">sound</option>
						            <option value="s3bubble_clean">clean</option>
						          </select>
						          <br />
						          <span class="description">Change the player theme.</p></td>
						      </tr>
						      <tr>
						        <th scope="row" valign="top"><label for="responsive">Aspect Ratio:</label></th>
						        <td><select name="responsive" id="responsive">
						            <option value="<?php echo $responsive; ?>"><?php echo $responsive; ?></option>
						            <option value="270p">270p</option>
						            <option value="360p">360p</option>
						            <option value="responsive">responsive</option>
						          </select>
						          <br />
						          <span class="description">This will set the aspect ration for the video players.</p></td>
						      </tr>
						      <tr>
						        <th scope="row" valign="top"><label for="stream">RTMP:</label></th>
						        <td>S3Bubble now supports RTMP (Real Time Messaging Protocol) Cloudfront Streaming please see this <a href="https://www.youtube.com/watch?v=lPoMKEQ4lng">tutorial</a><br>
						        	The default streaming is Progressive Streaming.
						          <br />
						          <span class="description">Real Time Messaging Protocol streaming is good for power users.</p></td>
						      </tr>
						      <tr>
						        <th scope="row" valign="top"><label for="solution">Media Options:</label></th>
						        <td><select name="solution" id="solution">
						            <option value="<?php echo $solution; ?>"><?php echo $solution; ?></option>
						            <option value="html,flash">HTML</option>
						            <option value="flash,html">FLASH</option>
						          </select>
						          <br />
						          <span class="description">Set whether you would like to use flash as primary or html5 video audio as primary.</p></td>
						      </tr>
						      <tr>
						        <th scope="row" valign="top"><label for="s3bubble_s3bubble">Show Share:</label></th>
						        <td><select name="s3bubble_share" id="s3bubble_share">
						            <option value="<?php echo $s3bubble_share; ?>"><?php echo $s3bubble_share; ?></option>
						            <option value="true">true</option>
						            <option value="false">false</option>
						          </select>
						          <br />
						          <span class="description">Allow users to share media.</p></td>
						      </tr>
						       <tr>
						        <th scope="row" valign="top"><label for="download">Show Download Links:</label></th>
						        <td><select name="download" id="download">
						            <option value="<?php echo $download; ?>"><?php echo $download; ?></option>
						            <option value="true">true</option>
						            <option value="false">false</option>
						          </select>
						          <br />
						          <span class="description">If set to true download links will show.</p>
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
						    </table>
						    <br/>
						    <span class="description">The powered by s3bubble link will only show during the trial period or if you upgrade it will be removed.</p>
						    <span class="submit" style="border: 0;">
						    <input type="submit" name="submit" class="button button-primary button-hero" value="Save Settings" />
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
	   
	   
	   function s3bubble_audio_player($atts){
	   	    // get option from database
			$s3audible_username = get_option("s3-s3audible_username");
			$s3audible_email    = get_option("s3-s3audible_email");		
			$loggedin           = get_option("s3-loggedin");
			$search             = get_option("s3-search");
			$share              = get_option("s3-s3bubble_share");
			$solution           = get_option("s3-solution");
			$stream             = get_option("s3-stream");
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = get_option("s3-download");
				}
			}else{
				$download = get_option("s3-download");
			}
		    $array = array($s3audible_username, $s3audible_email);
            $bind = implode("|", $array);
		    $userdata = base64_encode($bind); 
	        extract( shortcode_atts( array(
				'playlist'   => '',
				'height'     => '',
				'bucket'     => '',
				'folder'     => '',
				'order'      => 'asc',
				'download'   => $download,
				'share'      => $share,
				'search'     => $search,
				'solution'   => $solution,
				'autoplay'   => 'false',
			), $atts, 's3bubbleAudio' ) );
			extract( shortcode_atts( array(
				'playlist'   => '',
				'height'     => '',
				'bucket'     => '',
				'folder'     => '',
				'order'      => 'asc',
				'download'   => $download,
				'share'      => $share,
				'search'     => $search,
				'solution'   => $solution,
				'autoplay'   => 'false',
			), $atts, 's3audible' ) );
		   return '<div class="s3audible s3bubblePlayer" data-solution="'.$solution.'" data-s3hare="'.$share.'" data-playlist="'.$playlist.'" data-height="'.$height.'" data-download="'.$download.'" data-search="'.$search.'" data-userdata="'.$userdata.'" data-bucket="'.$bucket.'" data-folder="'.$folder.'" data-order="'.$order.'" data-autoplay="'.$autoplay.'"></div>';
		
        }


		function s3bubble_audio_single_player($atts){
			 // get option from database
			 $s3audible_username = get_option("s3-s3audible_username");
			 $s3audible_email    = get_option("s3-s3audible_email");		
			 $loggedin           = get_option("s3-loggedin");
			 $search             = get_option("s3-search");
			 $share              = get_option("s3-s3bubble_share");
			 $solution           = get_option("s3-solution");
			 $stream             = get_option("s3-stream");
			 if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = get_option("s3-download");
				}
			 }else{
				$download = get_option("s3-download");
			 }
		     $array = array($s3audible_username, $s3audible_email);
             $bind = implode("|", $array);
		     $userdata = base64_encode($bind);
			 extract( shortcode_atts( array(
				'style'      => '',
				'bucket'     => '',
				'track'      => '',
				'stream'     => $stream,
				'download'   => $download,
				'share'      => $share,
				'solution'   => $solution,
				'autoplay'   => 'false',
			), $atts, 's3bubbleAudioSingle' ) );
			extract( shortcode_atts( array(
				'style'      => '',
				'bucket'     => '',
				'track'      => '',
				'stream'     => $stream,
				'download'   => $download,
				'share'      => $share,
				'solution'   => $solution,
				'autoplay'   => 'false',
			), $atts, 's3audibleSingle' ) );
		   return '<div class="s3audibleSingle s3bubblePlayer" data-stream="'.$stream.'" data-solution="'.$solution.'" data-style="'.$style.'" data-s3hare="'.$share.'" data-download="'.$download.'" data-userdata="'.$userdata.'" data-bucket="'.$bucket.'" data-track="'.$track.'" data-autoplay="'.$autoplay.'"></div>';
		
        }
        
        function s3bubble_video_player($atts){
        	 // get option from database
			$s3audible_username = get_option("s3-s3audible_username");
			$s3audible_email    = get_option("s3-s3audible_email");		
			$loggedin           = get_option("s3-loggedin");
			$search             = get_option("s3-search");
			$responsive         = get_option("s3-responsive");
			$share              = get_option("s3-s3bubble_share");
			$solution           = get_option("s3-solution");
			$stream             = get_option("s3-stream");
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = get_option("s3-download");
				}
			}else{
				$download = get_option("s3-download");
			}
		   $array = array($s3audible_username, $s3audible_email);
           $bind = implode("|", $array);
		   $userdata = base64_encode($bind);
        	 extract( shortcode_atts( array(
				'playlist'   => '',
				'height'     => '',
				'bucket'     => '',
				'folder'     => '',
				'order'      => 'asc',
				'cloudfront' => '',
				'download'   => $download,
				'share'      => $share,
				'search'     => $search,
				'solution'   => $solution,
				'responsive' => $responsive,
				'autoplay'   => 'false',
			), $atts, 's3bubbleVideo' ) );
			extract( shortcode_atts( array(
				'playlist'   => '',
				'height'     => '',
				'bucket'     => '',
				'folder'     => '',
				'order'      => 'asc',
				'cloudfront' => '',
				'download'   => $download,
				'share'      => $share,
				'search'     => $search,
				'solution'   => $solution,
				'responsive' => $responsive,
				'autoplay'   => 'false',
			), $atts, 's3video' ) );

			
		   return '<div class="s3video s3bubblePlayer" data-cloudfront="'.$cloudfront.'" data-solution="'.$solution.'" data-responsive="'.$responsive.'" data-s3hare="'.$share.'" data-playlist="'.$playlist.'" data-height="'.$height.'" data-download="'.$download.'" data-search="'.$search.'" data-userdata="'.$userdata.'" data-bucket="'.$bucket.'" data-folder="'.$folder.'" data-order="'.$order.'" data-autoplay="'.$autoplay.'"></div>';
		
        }
		
		function s3bubble_video_single_player($atts){
			// get option from database
			$s3audible_username = get_option("s3-s3audible_username");
			$s3audible_email    = get_option("s3-s3audible_email");		
			$loggedin           = get_option("s3-loggedin");
			$search             = get_option("s3-search");
			$responsive         = get_option("s3-responsive");
			$share              = get_option("s3-s3bubble_share");
			$solution           = get_option("s3-solution");
			$stream             = get_option("s3-stream");
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = get_option("s3-download");
				}
			}else{
				$download = get_option("s3-download");
			}
		    $array = array($s3audible_username, $s3audible_email);
            $bind = implode("|", $array);
		    $userdata = base64_encode($bind); 
	        extract( shortcode_atts( array(
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => '',
				'download'   => $download,
				'share'      => $share,
				'solution'   => $solution,
				'responsive' => $responsive,
				'autoplay'   => 'false',
			), $atts, 's3bubbleVideoSingle' ) );
			extract( shortcode_atts( array(
				'playlist'   => '',
				'height'     => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => '',
				'download'   => $download,
				'share'      => $share,
				'solution'   => $solution,
				'responsive' => $responsive,
				'autoplay'   => 'false',
			), $atts, 's3videoSingle' ) );
            
		   return '<div class="s3videoSingle s3bubblePlayer" data-cloudfront="'.$cloudfront.'" data-solution="'.$solution.'" data-responsive="'.$responsive.'" data-s3hare="'.$share.'" data-playlist="'.$playlist.'" data-height="'.$height.'" data-download="'.$download.'"  data-track="'.$track.'" data-userdata="'.$userdata.'" data-bucket="'.$bucket.'" data-folder="'.$folder.'" data-autoplay="'.$autoplay.'" data-style="'.$style.'"></div>';
		
        }
		
	}
// Initiate the class
$s3bubble_audio = new s3bubble_audio();
add_action( 'widgets_init', create_function( '', 'register_widget( "s3bubble_audio_widget" );' ) );
} //End Class s3audible

/**
 * Adds Foo_Widget widget.
 */
class s3bubble_audio_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		's3bubble_audio_widget', // Base ID
			'S3Bubble', // Name
			array( 'description' => __( 'S3Bubble Cloud Player and media manager', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$autoplay = apply_filters( 'autoplay', $instance['autoplay'] );
		$s3bucket = $instance['s3bucket'];
		$s3folder = $instance['s3folder'];
	
		echo $before_widget;
           
		    // get option from database
			$s3audible_username = get_option("s3-s3audible_username");
			$s3audible_email = get_option("s3-s3audible_email");		
			$loggedin        = get_option("s3-loggedin");
			$search          = get_option("s3-search");
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = get_option("s3-download");
				}
			}else{
				$download = get_option("s3-download");
			}
			$array = array($s3audible_username, $s3audible_email);
            $bind = implode("|", $array);
		    $userdata = base64_encode($bind);
		    echo '<div class="s3audible s3bubblePlayer" data-download="'.$download.'" data-search="'.$search.'" data-userdata="'.$userdata.'" data-bucket="'.$s3bucket.'" data-folder="'.$s3folder.'" data-autoplay="'.$autoplay.'"></div>';
		
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update(  $new_instance, $old_instance  ) {
		$instance = $old_instance;
		$instance = array();
		$instance['autoplay'] = strip_tags( $new_instance['autoplay'] );
		$instance['s3bucket'] = strip_tags( $new_instance['s3bucket'] );
		$instance['s3folder'] = strip_tags( $new_instance['s3folder'] );
		return $instance;

	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		
		if ( isset( $instance[ 'autoplay' ] ) ) {
			$autoplay = $instance[ 'autoplay' ];
		}
		else {
			$autoplay = __( 'false', 'text_domain' );
		}
		if ( isset( $instance[ 's3bucket' ] ) ) {
			$s3bucket = $instance[ 's3bucket' ];
		}
		else {
			$s3bucket = __( 'enter bucket', 'text_domain' );
		}
		if ( isset( $instance[ 's3folder' ] ) ) {
			$s3folder = $instance[ 's3folder' ];
		}
		else {
			$s3folder = __( '', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'autoplay' ); ?>"><?php _e( 'Autoplay:true/false' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'autoplay' ); ?>" name="<?php echo $this->get_field_name( 'autoplay' ); ?>" type="text" value="<?php echo esc_attr( $autoplay ); ?>" />
		</p>
        <p>
		<label for="<?php echo $this->get_field_id( 's3bucket' ); ?>"><?php _e( 'Bucket' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 's3bucket' ); ?>" name="<?php echo $this->get_field_name( 's3bucket' ); ?>" type="text" value="<?php echo esc_attr( $s3bucket ); ?>" />
		</p>
         <p>
		<label for="<?php echo $this->get_field_id( 's3folder' ); ?>"><?php _e( 'Bucket Folder:Optional' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 's3folder' ); ?>" name="<?php echo $this->get_field_name( 's3folder' ); ?>" type="text" value="<?php echo esc_attr( $s3folder ); ?>" />
		</p>
		<?php 
	}

} // class s3audible widget