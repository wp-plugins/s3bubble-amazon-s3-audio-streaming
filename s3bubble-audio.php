<?php
/*
Plugin Name: S3Bubble Amazon S3 Cloudfront Video And Audio Streaming
Plugin URI: https://www.s3bubble.com/
Description: S3Bubble offers simple, secure media streaming from Amazon S3 to WordPress with CLoudfront. In just 3 simple steps. 
Version: 1.7.1
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

		/*
		 * Class properties
		 * @author sameast
		 * @params noen
		 */ 
        public $s3audible_username = '';
		public $s3audible_email = '';
		public $bucket          = '';
		public $folder          = '';
		public $colour          = '#fff';
		public $width           = '100%';
		public $autoplay        = 'yes';
		public $jtoggle		    = 'true';
		public $security		= 'true';
		public $s3bubble_share  = 'false';
		public $download		= 'false';
		public $loggedin        = 'false';
		public $search          = 'false';
		public $solution        = 'flash,html';
		public $responsive      = '360p';
		public $theme           = 's3bubble_clean';
		public $stream          = 'm4v';
		public $version         = 10;
		
		/*
		 * Run Constructor method 
		 * @author sameast
		 * @params none
		 */ 
	    function s3bubble_audio() { 
			$this->__construct();
		}
		
		/*
		 * Constructor method to intiat the class
		 * @author sameast
		 * @params none
		 */ 
		function __construct(){
			
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
			add_option("s3-security", $this->security);
			add_option("s3-download", $this->download);
			add_option("s3-loggedin", $this->loggedin);
			add_option("s3-search", $this->search);
			add_option("s3-solution", $this->solution);
			add_option("s3-responsive", $this->responsive);
			add_option("s3-theme", $this->theme);
			add_option("s3-stream", $this->stream);
			add_option("s3-s3bubble_share", $this->s3bubble_share);

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
			add_action( 'wp_head', array( $this, 's3bubble_audio_javascript' ), 11 );
			
			/*
			 * Add css to the wordpress admin document
			 * @author sameast
			 * @params none
			 */ 
			add_action( 'admin_head', array( $this, 's3bubble_audio_css_admin' ) );
			
			/*
			 * Add javascript to the frontend footer connects to wp_footer
			 * @author sameast
			 * @params none
			 */ 
			add_action( 'admin_footer', array( $this, 's3bubble_audio_javascript_admin' ) );
			
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
			 * Media Element shortcodes for the plugin
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3bubbleMediaElementVideo', array( $this, 's3bubble_media_element_video' ) );
			add_shortcode( 's3bubbleMediaElementAudio', array( $this, 's3bubble_media_element_audio' ) );
			
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
		}
        
        /*
		* Audio playlist button callback
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_playlist_ajax(){
		    // echo the form
		    $s3audible_username = get_option("s3-s3audible_username");
			$s3audible_email    = get_option("s3-s3audible_email");	
		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        	$('#TB_ajaxContent').css({
                    	'width' : 'none',
                    	'height' : '470px'
                    });
			        var sendData = {
						action: 's3bubble_plugin_get_buckets_api',
						username: '<?php echo $s3audible_username; ?>'
					};
					$.post("https://s3bubble.com/wp-admin/admin-ajax.php", sendData, function(response) {
						var html = '<select class="form-control input-lg" tabindex="1" name="s3bucket" id="s3bucket"><option value="">Choose bucket</option>';
					    $.each(response.Buckets, function (i, item) {
					    	var bucket = item.Name;
					    	html += '<option value="' + bucket + '">' + bucket + '</option>';
						});
						html += '</select>';
						$('#s3bubble-buckets-shortcode').html(html);
						$( "#s3bucket" ).change(function() {
						   $('#s3bubble-folders-shortcode').html('<img src="<?php echo plugins_url('/assets/js/ajax-loader.gif',__FILE__); ?>"/> loading folders');
						   var bucket = $(this).val();
						   var data = {
								action: 's3bubble_plugin_get_folders_api',
								username: '<?php echo $s3audible_username; ?>',
								bucket: bucket
							};
							$.post("https://s3bubble.com/wp-admin/admin-ajax.php", data, function(response) {
								var html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder"><option value="">Choose folder</option><option value="">Root</option>';
							    $.each(response, function (i, item) {
							    	var folder = item;
							    	html += '<option value="' + folder + '">' + folder + '</option>';
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
						    var playlist = true;
						}else{
						    var playlist = false;
						}
			        	var order      = $('#s3order').val();
			        	if($("#s3order").is(':checked')){
						    var order = 'order="desc"';
						}
						if($("#s3share").is(':checked')){
						    var share = true;
						}else{
						    var share = false;
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
	        	        var shortcode = '[s3bubbleAudio bucket="' + bucket + '" folder="' + folder + '" cloudfront="' + cloudfront + '"  height="' + height + '"  autoplay="' + autoplay + '" playlist="' + playlist + '" ' + order + ' share="' + share + '" download="' + download + '"  preload="' + preload + '"/]';
	                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
	                    tb_remove();
			        });
		        })
		    </script>
		    <form class="s3bubble-form-general">
		    	<blockquote class="bs-callout-s3bubble"><strong>Please select your bucket and then folder below</strong> when you select your bucket S3Bubble will auto generate a list of folders to choose from.</blockquote>
		    	<p>
			    	<span class="s3bubble-pull-left" id="s3bubble-buckets-shortcode">loading buckets...</span>
					<span class="s3bubble-pull-right" id="s3bubble-folders-shortcode"></span>
				</p>
				<p>
					<span class="s3bubble-pull-left">
						<label for="fname">Set A Playlist Height: <i>(Do Not Add PX)</i></label><input type="text" class="s3bubble-form-input" name="height" id="s3height">
				    </span>
				    <!--<span class="s3bubble-pull-right">
				    	<label for="fname">Cloudfront Distribution ID: </label><input type="text" class="s3bubble-form-input" name="cloudfront" id="s3cloudfront">
				    </span>-->
				</p>
				<input type="hidden" class="s3bubble-form-input" name="cloudfront" id="s3cloudfront">
				<blockquote class="bs-callout-s3bubble"><strong>Extra options</strong> please just select any extra options from the list below and S3Bubble will automatically add it to the shortcode.</blockquote>
				<p><input type="checkbox" name="autoplay" id="s3autoplay">Autoplay <i>(Start Video On Page Load)</i><br />
				<input type="checkbox" name="playlist" id="s3playlist" value="hidden">Hide Playlist <i>(Hide Playlist On Page Load)</i><br />
				<input type="checkbox" name="order" id="s3order" value="desc">Reverse Order <i>(Reverse The Playlist Order)</i><br />
				<input type="checkbox" name="share" id="s3share" value="true">Show Twitter Share <i>(Adds A Twitter Share Button To The Tracks)</i><br />
				<input class="s3bubble-checkbox" type="checkbox" name="s3preload" id="s3preload" value="true">Preload Off <i>(Prevent Tracks From Preloading)</i><br />
				<input type="checkbox" name="download" id="s3download" value="true">Show Download Links <i>(Adds A Download Button To The Tracks)</i></p>
				<p>
					<a href="#"  id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
				</p>
			</form>
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
		    $s3audible_username = get_option("s3-s3audible_username");
			$s3audible_email    = get_option("s3-s3audible_email");	
		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        	$('#TB_ajaxContent').css({
                    	'width' : 'none',
                    	'height' : '470px'
                    });
			        var sendData = {
						action: 's3bubble_plugin_get_buckets_api',
						username: '<?php echo $s3audible_username; ?>'
					};
					$.post("https://s3bubble.com/wp-admin/admin-ajax.php", sendData, function(response) {	
						var html = '<select class="form-control input-lg" tabindex="1" name="s3bucket" id="s3bucket"><option value="">Choose bucket</option>';
					    $.each(response.Buckets, function (i, item) {
					    	var bucket = item.Name;
					    	html += '<option value="' + bucket + '">' + bucket + '</option>';
						});
						html += '</select>';
						$('#s3bubble-buckets-shortcode').html(html);
						$( "#s3bucket" ).change(function() {
						   $('#s3bubble-folders-shortcode').html('<img src="<?php echo plugins_url('/assets/js/ajax-loader.gif',__FILE__); ?>"/> loading folders');
						   var bucket = $(this).val();
						   var data = {
								action: 's3bubble_plugin_get_folders_api',
								username: '<?php echo $s3audible_username; ?>',
								bucket: bucket
							};
							$.post("https://s3bubble.com/wp-admin/admin-ajax.php", data, function(response) {
								var html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder"><option value="">Choose folder</option><option value="">Root</option>';
							    $.each(response, function (i, item) {
							    	var folder = item;
							    	html += '<option value="' + folder + '">' + folder + '</option>';
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
						    var playlist = true;
						}else{
						    var playlist = false;
						}
			        	var order      = $('#s3order').val();
			        	if($("#s3order").is(':checked')){
						    var order = 'order="desc"';
						}
						if($("#s3share").is(':checked')){
						    var share = true;
						}else{
						    var share = false;
						}
						if($("#s3download").is(':checked')){
						    var download = true;
						}else{
						    var download = false;
						}
	        	        var shortcode = '[s3bubbleVideo bucket="' + bucket + '" folder="' + folder + '" cloudfront="' + cloudfront + '"  height="' + height + '"  autoplay="' + autoplay + '" playlist="' + playlist + '" ' + order + ' share="' + share + '" download="' + download + '"/]';
	                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
	                    tb_remove();
			        });
		        })
		    </script>
		    <form class="s3bubble-form-general">
		    	<blockquote class="bs-callout-s3bubble"><strong>Please select your bucket and then folder below</strong> when you select your bucket S3Bubble will auto generate a list of folders to choose from.</blockquote>
		    	<p>
			    	<span class="s3bubble-pull-left" id="s3bubble-buckets-shortcode">loading buckets...</span>
					<span class="s3bubble-pull-right" id="s3bubble-folders-shortcode"></span>
				</p>
				<p>
					<span class="s3bubble-pull-left">
				    	<label for="fname">Cloudfront Distribution ID: </label><input type="text" class="s3bubble-form-input" name="cloudfront" id="s3cloudfront">
				    </span>
					<span class="s3bubble-pull-right">
						<label for="fname">Set A Playlist Height:</label><input type="text" class="s3bubble-form-input" name="height" id="s3height">
				    </span>
				</p>
				<blockquote class="bs-callout-s3bubble"><strong>Extra options</strong> please just select any extra options from the list below and S3Bubble will automatically add it to the shortcode.</blockquote>
				<p><input type="checkbox" name="autoplay" id="s3autoplay">Autoplay <i>(Start Video On Page Load)</i><br />
				<input type="checkbox" name="playlist" id="s3playlist" value="hidden">Hide Playlist <i>(Hide Playlist On Page Load)</i><br />
				<input type="checkbox" name="order" id="s3order" value="desc">Reverse Order <i>(Reverse The Playlist Order)</i><br />
				<input type="checkbox" name="share" id="s3share" value="true">Show Twitter Share <i>(Adds A Twitter Share Button To The Videos)</i><br />
				<input type="checkbox" name="download" id="s3download" value="true">Show Download Links <i>(Adds A Download Button To The Videos)</i></p>
			    <p>
					<a href="#"  id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
				</p>
			</form>
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
		    $s3audible_username = get_option("s3-s3audible_username");
			$s3audible_email    = get_option("s3-s3audible_email");	
		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        	$('#TB_ajaxContent').css({
                    	'width' : 'none',
                    	'height' : '450px'
                    });
			        var sendData = {
						action: 's3bubble_plugin_get_buckets_api',
						username: '<?php echo $s3audible_username; ?>'
					};
					$.post("https://s3bubble.com/wp-admin/admin-ajax.php", sendData, function(response) {
						var html = '<select class="form-control input-lg" tabindex="1" name="s3bucket" id="s3bucket"><option value="">Choose bucket</option>';
					    $.each(response.Buckets, function (i, item) {
					    	var bucket = item.Name;
					    	html += '<option value="' + bucket + '">' + bucket + '</option>';
						});
						html += '</select>';
						$('#s3bubble-buckets-shortcode').html(html);
						$( "#s3bucket" ).change(function() {
						   $('#s3bubble-folders-shortcode').html('<img src="<?php echo plugins_url('/assets/js/ajax-loader.gif',__FILE__); ?>"/> loading videos files');
						   var bucket = $(this).val();
						   var data = {
								action: 's3bubble_plugin_get_video_files_api',
								username: '<?php echo $s3audible_username; ?>',
								bucket: bucket
							};
							$.post("https://s3bubble.com/wp-admin/admin-ajax.php", data, function(response) {
								var html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder"><option value="">Choose video</option>';
							    $.each(response, function (i, item) {
							    	var folder = item;
							    	html += '<option value="' + folder + '">' + folder + '</option>';
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
			        	if($("#s3autoplay").is(':checked')){
						    var autoplay = true;
						}else{
						    var autoplay = false;
						}
						if($("#s3share").is(':checked')){
						    var share = true;
						}else{
						    var share = false;
						}
						if($("#s3download").is(':checked')){
						    var download = true;
						}else{
						    var download = false;
						}
						var shortcode = '[s3bubbleVideoSingle bucket="' + bucket + '" track="' + folder + '" cloudfront="' + cloudfront + '" autoplay="' + autoplay + '" share="' + share + '" download="' + download + '"/]';
						if($("#s3mediaelement").is(':checked')){
						    shortcode = '[s3bubbleMediaElementVideo bucket="' + bucket + '" track="' + folder + '" cloudfront="' + cloudfront + '" autoplay="' + autoplay + '" share="' + share + '" download="' + download + '"/]';
						}
	                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
	                    tb_remove();
			        });
		        })
		    </script>
		    <form class="s3bubble-form-general">
                <blockquote class="bs-callout-s3bubble"><strong>Please select your bucket and then file below.</strong> When you select your bucket S3Bubble will auto generate a list of files to choose from.</blockquote>
		    	<p>
			    	<span class="s3bubble-pull-left" id="s3bubble-buckets-shortcode">loading buckets...</span>
					<span class="s3bubble-pull-right" id="s3bubble-folders-shortcode"></span>
				</p>
				<p>
					<span class="s3bubble-pull-left">
				    	<label for="fname">Cloudfront Distribution ID: </label><input type="text" class="s3bubble-form-input" name="cloudfront" id="s3cloudfront">
				    </span>
				</p> 
                <blockquote class="bs-callout-s3bubble"><strong>Extra options:</strong> please just select any extra options from the list below, and S3Bubble will automatically add it to the shortcode.</blockquote>
				<p><input type="checkbox" name="autoplay" id="s3autoplay">Autoplay <i>(Start Video On Page Load)</i><br />
				<input type="checkbox" name="share" id="s3share" value="true">Show Twitter Share <i>(Adds A Twitter Share Button To The Video)</i><br />
				<input type="checkbox" name="download" id="s3download" value="true">Show Download Links <i>(Add A Download Button To The Video)</i><br />
				<input type="checkbox" name="mediaelement" id="s3mediaelement" value="true">Use Media Elements JS <i>(Changes the player from default to media element js player)</i></p>

				<p>
					<a href="#"  id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
				</p>
			</form>
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
		    $s3audible_username = get_option("s3-s3audible_username");
			$s3audible_email    = get_option("s3-s3audible_email");	
		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        		
                    $('#TB_ajaxContent').css({
                    	'width' : 'none',
                    	'height' : '450px'
                    }); 
			        var sendData = {
						action: 's3bubble_plugin_get_buckets_api',
						username: '<?php echo $s3audible_username; ?>'
					};
					$.post("https://s3bubble.com/wp-admin/admin-ajax.php", sendData, function(response) {
						var html = '<select class="form-control input-lg" tabindex="1" name="s3bucket" id="s3bucket"><option value="">Choose bucket</option>';
					    $.each(response.Buckets, function (i, item) {
					    	var bucket = item.Name;
					    	html += '<option value="' + bucket + '">' + bucket + '</option>';
						});
						html += '</select>';
						$('#s3bubble-buckets-shortcode').html(html);
						$( "#s3bucket" ).change(function() {
						   $('#s3bubble-folders-shortcode').html('<img src="<?php echo plugins_url('/assets/js/ajax-loader.gif',__FILE__); ?>"/> loading audio files');
						   var bucket = $(this).val();
						   var data = {
								action: 's3bubble_plugin_get_audio_files_api',
								username: '<?php echo $s3audible_username; ?>',
								bucket: bucket
							};
							$.post("https://s3bubble.com/wp-admin/admin-ajax.php", data, function(response) {
								var html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder"><option value="">Choose audio</option>';
							    $.each(response, function (i, item) {
							    	var folder = item;
							    	html += '<option value="' + folder + '">' + folder + '</option>';
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
						if($("#s3share").is(':checked')){
						    var share = true;
						}else{
						    var share = false;
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
						var shortcode = '[s3bubbleAudioSingle bucket="' + bucket + '" track="' + folder + '" cloudfront="' + cloudfront + '" autoplay="' + autoplay + '" share="' + share + '" download="' + download + '" style="' + style + '" preload="' + preload + '"/]';
						if($("#s3mediaelement").is(':checked')){
						    shortcode = '[s3bubbleMediaElementAudio bucket="' + bucket + '" track="' + folder + '" cloudfront="' + cloudfront + '" autoplay="' + autoplay + '" share="' + share + '" download="' + download + '" style="' + style + '" preload="' + preload + '"/]';
						}  
	                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
	                    tb_remove();
			        });
		        })
		    </script>
		    <form class="s3bubble-form-general">
		    	<blockquote class="bs-callout-s3bubble"><strong>Please select your bucket and then file below.</strong> When you select your bucket S3Bubble will auto generate a list of files to choose from.</blockquote>
		    	<p>
			    	<span class="s3bubble-pull-left" id="s3bubble-buckets-shortcode">loading buckets...</span>
					<span class="s3bubble-pull-right" id="s3bubble-folders-shortcode"></span>
				</p>
				<!--<p>
					<span class="s3bubble-pull-left">
				    	<label for="fname">Cloudfront Distribution ID: </label><input type="hidden" class="s3bubble-form-input" name="cloudfront" id="s3cloudfront">
				    </span>
				</p>--> 
				<input type="hidden" class="s3bubble-form-input" name="cloudfront" id="s3cloudfront">
				<blockquote class="bs-callout-s3bubble"><strong>Extra options:</strong> please just select any extra options from the list below, and S3Bubble will automatically add it to the shortcode.</blockquote>
				<p><input class="s3bubble-checkbox" type="checkbox" name="autoplay" id="s3autoplay">Autoplay <i>(Start Audio On Page Load)</i><br />
				<input class="s3bubble-checkbox" type="checkbox" name="style" id="s3style" value="true">Remove Bar <i>(Remove The Info Bar Under Player)</i><br />
				<input class="s3bubble-checkbox" type="checkbox" name="share" id="s3share" value="true">Show Twitter Share <i>(Adds A Twitter Share Button To The Track)</i><br />
				<input class="s3bubble-checkbox" type="checkbox" name="preload" id="s3preload" value="true">Preload Off <i>(Prevent Track From Preloading)</i><br />
				<input class="s3bubble-checkbox" type="checkbox" name="download" id="s3download" value="true">Show Download Links <i>(Add A Download Button To The Track)</i><br />
				<input type="checkbox" name="mediaelement" id="s3mediaelement" value="true">Use Media Elements JS <i>(Changes the player from default to media element js player)</i></p>
				<p>
					<a href="#"  id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
				</p>
			</form>
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
		    $plugin_array['s3bubble'] = plugins_url('/assets/js/s3bubble-plugin.js',__FILE__);
		    return $plugin_array;
		}
		
		/*
		* Registers the amount of buttons
		* @author sameast
		* @none
		*/ 
		function s3bubble_register_buttons( $buttons ) {
		    array_push( $buttons, 's3bubble_audio_single_shortcode', 's3bubble_audio_playlist_shortcode', 's3bubble_video_single_shortcode', 's3bubble_video_playlist_shortcode' ); 
		    return $buttons;
		}
        
		/*
		* Adds the menu item to the wordpress admin
		* @author sameast
		* @none
		*/ 
        function s3bubble_audio_admin_menu(){	
			add_menu_page( 's3bubble_audio', 'S3Bubble Media', 'manage_options', 's3bubble_audio', array($this, 's3bubble_audio_admin'), plugins_url('assets/images/wps3icon.png',__FILE__ ) );
    	}
        
		/*
		* Add css to wordpress admin to run colourpicker
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_css_admin(){
			wp_register_style( 'colorpicker', plugins_url('assets/css/colorpicker.css', __FILE__) );
			wp_enqueue_style('colorpicker');
			wp_register_style( 's3bubble-plugin', plugins_url('assets/css/s3bubble-plugin.css', __FILE__) );
			wp_enqueue_style('s3bubble-plugin');
		}
		
        /*
		* Add javascript to the admin header
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_javascript_admin(){
			wp_register_script( 's3bubble-colorpicker', plugins_url('assets/js/colorpicker.js',__FILE__ ));
            wp_enqueue_script('s3bubble-colorpicker');
			wp_register_script( 's3bubble-colorpicker-admin', plugins_url('assets/js/admin.js',__FILE__ ));
            wp_enqueue_script('s3bubble-colorpicker-admin');
		} 
		
		/*
		* Add css ties into wp_head() function
		* @author sameast
		* @params none
        */ 
		function s3bubble_audio_css(){
		   	$colour	= get_option("s3-colour");    
			$theme = get_option("s3-theme");
			wp_register_style( 'font-awesome.min', plugins_url('assets/css/fa/font-awesome.min.css', __FILE__), array(), $this->version );
			wp_enqueue_style('font-awesome.min');
			wp_register_style( 's3bubble-style-default', plugins_url('assets/css/default.css', __FILE__), array(), $this->version );
			wp_enqueue_style('s3bubble-style-default');
			wp_register_style( 'mediaelementplayer.min', plugins_url('assets/mediaelementjs/build/mediaelementplayer.min.css', __FILE__), array(), $this->version );
			wp_enqueue_style('mediaelementplayer.min');
			// updated css
		    echo '<style type="text/css">
					.s3bubblePlayer a > * {color: '.stripcslashes($colour).' !important;}
					.s3-play-bar, .s3-playlist ul li.s3-playlist-current {background-color: '.stripcslashes($colour).' !important;}
					.s3-current-time, .s3-duration, .s3-time-seperator, .s3-playlist ul li a.s3-playlist-current {color: '.stripcslashes($colour).' !important;}
					}   
			</style>';
			if($theme == 's3bubble_default'){
				wp_register_style( 's3bubble-style-style', plugins_url('assets/css/style.css', __FILE__), array(), $this->version );
			    wp_enqueue_style('s3bubble-style-style');
			}else if($theme == 's3bubble_light'){
				wp_register_style( 's3bubble-style-light', plugins_url('assets/css/light.css', __FILE__), array(), $this->version );
			    wp_enqueue_style('s3bubble-style-light');
			}else if($theme == 's3bubble_sound'){
				wp_register_style( 's3bubble-style-sound', plugins_url('assets/css/sound.css', __FILE__), array(), $this->version );
			    wp_enqueue_style('s3bubble-style-sound');
			}else if($theme == 's3bubble_clean'){
				wp_register_style( 's3bubble-style-clean', plugins_url('assets/css/clean.css', __FILE__), array(), $this->version );
			    wp_enqueue_style('s3bubble-style-clean');
				echo "<link href='//fonts.googleapis.com/css?family=Signika' rel='stylesheet' type='text/css'>";
			}else{
				wp_register_style( 's3bubble-style-default', plugins_url('assets/css/style.css', __FILE__), array(), $this->version );
			    wp_enqueue_style('s3bubble-style-default');
			}
		}
		
		/*
		* Add javascript to the footer connect to wp_footer()
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_javascript(){
           if (!is_admin()) {
           	    $security = get_option("s3-security");
	            wp_deregister_script( 'jquery' );
	            wp_register_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js', false, null);
	            wp_enqueue_script('jquery');
	            wp_register_script( 'jquery-migrate', plugins_url('assets/js/jquery-migrate-1.2.1.min.js',__FILE__ ), array(), $this->version );
	            wp_enqueue_script('jquery-migrate');
				wp_register_script( 's3bubble.min', plugins_url('assets/js/s3audible.min.js',__FILE__ ), array(), $this->version );
	            wp_enqueue_script('s3bubble.min');
				wp_register_script( 'mediaelement-and-player.min', plugins_url('assets/mediaelementjs/build/mediaelement-and-player.min.js',__FILE__ ), array(), $this->version );
	            wp_enqueue_script('mediaelement-and-player.min');
				if($security == 'true'){
					wp_register_script( 'devtools-detect', plugins_url('assets/js/devtools-detect.js',__FILE__ ), array(), $this->version );
		            wp_enqueue_script('devtools-detect');
				}
            } 
		}
        
		/*
		* Add javascript to the footer connect to wp_footer()
		* @author sameast
		* @none
		*/ 
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
				$solution			= addslashes($_POST['solution']);
				$security			= addslashes($_POST['security']);
				$responsive			= addslashes($_POST['responsive']);
				$theme			    = addslashes($_POST['theme']);
				$s3bubble_share	    = addslashes($_POST['s3bubble_share']);

			    // Update the DB with the new option values
				update_option("s3-s3audible_username", mysql_real_escape_string($s3audible_username));
				update_option("s3-s3audible_email", mysql_real_escape_string($s3audible_email));
				update_option("s3-colour", mysql_real_escape_string($colour));
				update_option("s3-download", mysql_real_escape_string($download));
				update_option("s3-loggedin", mysql_real_escape_string($loggedin));
				update_option("s3-solution", mysql_real_escape_string($solution));
				update_option("s3-security", mysql_real_escape_string($security));
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
			$security			= get_option("s3-security");
			$responsive			= get_option("s3-responsive");
			$theme			    = get_option("s3-theme");
			$stream			    = get_option("s3-stream");
			$s3bubble_share	    = get_option("s3-s3bubble_share");
		?>
		<style>
			.s3bubble-pre {
				white-space: pre-wrap;
				white-space: -moz-pre-wrap; 
				white-space: -pre-wrap; 
				white-space: -o-pre-wrap; 
				word-wrap: break-word; 
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
							<a class="button button-primary button-hero" target="_blank" href="https://www.youtube.com/watch?v=KNYfHwpAYxM">WATCH TUTORIAL VIDEO</a>
						    <a style="float: right;" class="button button-primary button-hero" target="_blank" href="https://www.youtube.com/watch?v=dZZ8Ytmbc1U">FREE AMAZON S3 SETUP</a>
						
						</div> 
					</div>
					
					<div class="postbox">
						<h3><span>*BRAND NEW WYSIWYG EDITOR BUTTONS</span></h3>
						<div class="inside">
							<img style="width: 100%;" src="https://isdcloud.s3.amazonaws.com/wp_editor.png" />
						</div> 
					</div>
					
		                 <div class="postbox">
		                 	 <h3 style="color: #31708f;background-color: #d9edf7;border-color: #bce8f1;padding: 15px;margin-bottom: 20px;border: 1px solid transparent;border-radius: 4px;">Stuck? This can be grabbed from your s3bubble account, it will be auto generated for you. Why not checkout our growing <a href="https://s3bubble.com/forums/" target="_blank" title="S3bubble community forum">Community Forum</a>?</h3> 
						<h3><span>Audio Playlist Shortcode Example - These are auto generated within your s3bubble admin</span></h3>
						<div class="inside">
							<pre class="s3bubble-pre">[s3bubbleAudio bucket="enter-your-bucket" folder="enter-your-bucket-folder"]</pre>
						</div>
						<h3><span>Audio Single Player Shortcode Example - These are auto generated within your s3bubble admin</span></h3>
						<div class="inside">
							<pre class="s3bubble-pre">[s3bubbleAudioSingle bucket="enter-your-bucket" track="enter-your-track-name"]</pre>
						</div>
						<h3><span>Media Elements JS Audio Single Player Shortcode Example - These are auto generated within your s3bubble admin</span></h3>
						<div class="inside">
							<pre class="s3bubble-pre">[s3bubbleMediaElementAudio bucket="enter-your-bucket" track="enter-your-track-name"]</pre>
						</div>
						<h3><span>Video Playlist Shortcode Example - These are auto generated within your s3bubble admin</span></h3>
						<div class="inside">
							<pre class="s3bubble-pre">[s3bubbleVideo bucket="enter-your-bucket" folder="enter-your-bucket-folder"]</pre>
						</div>
						<h3><span>Video Single Player Shortcode Example - These are auto generated within your s3bubble admin</span></h3>
						<div class="inside">
							<pre class="s3bubble-pre">[s3bubbleVideoSingle bucket="enter-your-bucket" track="enter-your-track-name"]</pre>
						</div>
						<h3><span>Media Elements JS Video Single Shortcode Example - These are auto generated within your s3bubble admin</span></h3>
						<div class="inside">
							<pre class="s3bubble-pre">[s3bubbleMediaElementVideo bucket="enter-your-bucket" track="enter-your-track-name" cloudfront="does-support-cloudfront"]</pre>
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
			            	<a href="https://isdcloud.s3.amazonaws.com/ver1.7/s3bubble_amazon_s3_audio_streaming_1_.zip">Download Last Plugin Version 1.7.0</a>
							<a href="https://isdcloud.s3.amazonaws.com/s3bubble_backups/s3bubble_amazon_s3_audio_streaming.zip">Download Last Plugin Version</a>
					    </div>
					</div>
				</div>
				<div id="post-body">
					<div id="post-body-content" style="margin-right: 51%;">
						<div class="postbox">
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
								        <td><input type="text" name="s3audible_username" id="s3audible_username" class="regular-text" value="<?php echo $s3audible_username; ?>"/>
								        	<br />
								       <span class="description">Username you signed up to S3Bubble.com found <a href="http://s3bubble.com/admin/#/profile" target="_blank">here</a></span>	
								        </td>
								      </tr> 
								       <tr>
								        <th scope="row" valign="top"><label for="s3audible_email">S3Bubble Email:</label></th>
								        <td><input type="text" name="s3audible_email" id="s3audible_email" class="regular-text" value="<?php echo $s3audible_email; ?>"/>
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
								        <th scope="row" valign="top"><label for="security">Extra Security:</label></th>
								        <td><select name="security" id="security">
								            <option value="<?php echo $security; ?>"><?php echo $security; ?></option>
								            <option value="true">true</option>
								            <option value="false">false</option>
								          </select>
								          <br />
								          <span class="description">Optimise security stop people from inspecting element on video & audio players.</p></td>
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
								        <td>S3Bubble now supports RTMP (Real Time Messaging Protocol) Cloudfront Streaming please see this <a href="https://www.youtube.com/watch?v=YZ2QIozCyb8">tutorial</a><br>
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
	   
	   /*
		* Run the media element video supports RTMP streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_media_element_video($atts){
			$s3audible_username = get_option("s3-s3audible_username");
			$s3audible_email    = get_option("s3-s3audible_email");		
			$loggedin           = get_option("s3-loggedin");
			$search             = get_option("s3-search");
			$share              = get_option("s3-s3bubble_share");
			$solution           = get_option("s3-solution");
			$stream             = get_option("s3-stream");
			$security           = get_option("s3-security");
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
				'autoplay'   => 'false',
			), $atts, 's3bubbleMediaElementVideo' ) );

			$url = 'https://s3bubble.com/wp-admin/admin-ajax.php';
			$fields = array(
                'action' => 's3bubble_plugin_video_single_api',
				'username' => $s3audible_username,
				'email' => $s3audible_email,
				'bucket' => $bucket,
				'track' => $track,
				'cloudfront' => $cloudfront
			);
			$fields_string = '';
			foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			rtrim($fields_string, '&');
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			$track = json_decode($result, true);
			$video = 'video_' . substr(md5(rand()), 0, 7);
			if(is_array($track)){
				if($cloudfront != ''){
					$end = explode('mp4:', $track[0]['rtmpv']);
			    	return '<video width="100%" height="415px" id="' . $video . '" src="mp4:' . $end[1] .'" poster="' . $track[0]['poster'] .'" type="video/rtmp" controls="controls"></video><script>jQuery(document).ready(function($) {$(\'#' . $video . '\').mediaelementplayer({flashStreamer:"' . $track[0]['rtmpv'] . '"});});</script>';
			    }else{
					return '<video width="100%" height="415px" id="' . $video . '" poster="' . $track[0]['poster'] . '" controls="controls" preload="none"><source type="video/mp4" src="' . $track[0]['m4v'] . '" /><object width="640" height="360" type="application/x-shockwave-flash" data="' . plugins_url('assets/mediaelementjs/build/flashmediaelement.swf',__FILE__ ) . '"><param name="movie" value="' . plugins_url('assets/mediaelementjs/build/flashmediaelement.swf',__FILE__ ) . '" /><param name="flashvars" value="controls=true&amp;file=' . $track[0]['m4v'] . '" /><img src="' . $track[0]['poster'] . '" width="640" height="360" alt="S3Bubble RTMP Streaming" title="S3Bubble RTMP Streaming" /></object></video><script>jQuery(document).ready(function($) {var player = new MediaElementPlayer(\'#' . $video . '\');'. (($autoplay == 'true') ? 'player.play();' : '') . '});</script>';
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
			$s3audible_username = get_option("s3-s3audible_username");
			$s3audible_email    = get_option("s3-s3audible_email");		
			$loggedin           = get_option("s3-loggedin");
			$search             = get_option("s3-search");
			$share              = get_option("s3-s3bubble_share");
			$solution           = get_option("s3-solution");
			$stream             = get_option("s3-stream");
			$security           = get_option("s3-security");
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
				'autoplay'   => 'false',
			), $atts, 's3bubbleMediaElementAudio' ) );

			$url = 'https://s3bubble.com/wp-admin/admin-ajax.php';
			$fields = array(
                'action' => 's3bubble_plugin_audio_single_api',
				'username' => $s3audible_username,
				'email' => $s3audible_email,
				'bucket' => $bucket,
				'track' => $track,
				'cloudfront' => $cloudfront
			);
			$fields_string = '';
			foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			rtrim($fields_string, '&');
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//execute post
			$result = curl_exec($ch);
			$track = json_decode($result, true);
			$audio = 'audio_' . substr(md5(rand()), 0, 7);
			if(is_array($track)){
				if($cloudfront != ''){
			    	return '<p>rtmp only currently support for video</p>';
			    }else{
					return '<audio width="100%" src="' . $track[0]['mp3'] . '" id="' . $audio . '"></audio><script>jQuery(document).ready(function($) {var player = new MediaElementPlayer(\'#' . $audio . '\');'. (($autoplay == 'true') ? 'player.play();' : '') . '});</script>';
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
			$s3audible_username = get_option("s3-s3audible_username");
			$s3audible_email    = get_option("s3-s3audible_email");		
			$loggedin           = get_option("s3-loggedin");
			$search             = get_option("s3-search");
			$share              = get_option("s3-s3bubble_share");
			$solution           = get_option("s3-solution");
			$stream             = get_option("s3-stream");
			$security           = get_option("s3-security");
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
				'solution'   => '',
				'cloudfront' => '',
				'autoplay'   => 'false',
				'preload'   => 'auto',
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
				'solution'   => '',
				'cloudfront' => '',
				'autoplay'   => 'false',
				'preload'   => 'auto',
			), $atts, 's3audible' ) );
		   return '<div class="s3audible s3bubblePlayer" data-security="'.$security.'" data-cloudfront="'.$cloudfront.'" data-solution="'.$solution.'" data-s3hare="'.$share.'" data-playlist="'.$playlist.'" data-height="'.$height.'" data-download="'.$download.'" data-search="'.$search.'" data-userdata="'.$userdata.'" data-bucket="'.$bucket.'" data-folder="'.$folder.'" data-order="'.$order.'" data-autoplay="'.$autoplay.'" data-preload="'.$preload.'"></div>';
		
        }

        /*
		* Run the s3bubble jplayer single audio function
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_single_player($atts){
			 $s3audible_username = get_option("s3-s3audible_username");
			 $s3audible_email    = get_option("s3-s3audible_email");		
			 $loggedin           = get_option("s3-loggedin");
			 $search             = get_option("s3-search");
			 $share              = get_option("s3-s3bubble_share");
			 $solution           = get_option("s3-solution");
			 $stream             = get_option("s3-stream");
			 $security           = get_option("s3-security");
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
				'cloudfront' => '',
				'autoplay'   => 'false',
				'preload'   => 'auto',
			), $atts, 's3bubbleAudioSingle' ) );
			extract( shortcode_atts( array(
				'style'      => '',
				'bucket'     => '',
				'track'      => '',
				'stream'     => $stream,
				'download'   => $download,
				'share'      => $share,
				'solution'   => $solution,
				'cloudfront' => '',
				'autoplay'   => 'false',
				'preload'   => 'auto',
			), $atts, 's3audibleSingle' ) );
		   return '<div class="s3audibleSingle s3bubblePlayer" data-security="'.$security.'" data-cloudfront="'.$cloudfront.'" data-stream="'.$stream.'" data-solution="'.$solution.'" data-style="'.$style.'" data-s3hare="'.$share.'" data-download="'.$download.'" data-userdata="'.$userdata.'" data-bucket="'.$bucket.'" data-track="'.$track.'" data-autoplay="'.$autoplay.'" data-preload="'.$preload.'"></div>';
        }
        
		/*
		* Run the s3bubble jplayer video playlist function
		* @author sameast
		* @none
		*/ 
        function s3bubble_video_player($atts){
			$s3audible_username = get_option("s3-s3audible_username");
			$s3audible_email    = get_option("s3-s3audible_email");		
			$loggedin           = get_option("s3-loggedin");
			$search             = get_option("s3-search");
			$responsive         = get_option("s3-responsive");
			$share              = get_option("s3-s3bubble_share");
			$solution           = get_option("s3-solution");
			$stream             = get_option("s3-stream");
			$security           = get_option("s3-security");
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
		   return '<div class="s3video s3bubblePlayer" data-security="'.$security.'" data-cloudfront="'.$cloudfront.'" data-solution="'.$solution.'" data-responsive="'.$responsive.'" data-s3hare="'.$share.'" data-playlist="'.$playlist.'" data-height="'.$height.'" data-download="'.$download.'" data-search="'.$search.'" data-userdata="'.$userdata.'" data-bucket="'.$bucket.'" data-folder="'.$folder.'" data-order="'.$order.'" data-autoplay="'.$autoplay.'"></div>';
        }
		
		/*
		* Run the s3bubble jplayer single video function
		* @author sameast
		* @none
		*/ 
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
			$security           = get_option("s3-security");
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
		   return '<div class="s3videoSingle s3bubblePlayer" data-security="'.$security.'" data-cloudfront="'.$cloudfront.'" data-solution="'.$solution.'" data-responsive="'.$responsive.'" data-s3hare="'.$share.'" data-playlist="'.$playlist.'" data-height="'.$height.'" data-download="'.$download.'"  data-track="'.$track.'" data-userdata="'.$userdata.'" data-bucket="'.$bucket.'" data-folder="'.$folder.'" data-autoplay="'.$autoplay.'" data-style="'.$style.'"></div>';
        }
	}
	/*
	* Initiate the class
	* @author sameast
	* @none
	*/ 
	$s3bubble_audio = new s3bubble_audio();
	add_action( 'widgets_init', create_function( '', 'register_widget( "s3bubble_audio_widget" );' ) );
} //End Class s3audible

/*
* Adds the S3Bubble Widget
* @author sameast
* @none
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
} 