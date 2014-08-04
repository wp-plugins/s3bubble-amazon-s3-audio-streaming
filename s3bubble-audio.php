<?php
/*
Plugin Name: S3Bubble Amazon S3 Cloudfront Video And Audio Streaming
Plugin URI: https://www.s3bubble.com/
Description: S3Bubble offers simple, secure media streaming from Amazon S3 to WordPress with Cloudfront. In just 4 simple steps. 
Version: 1.7.7
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
		public $loggedin        = 'false';
		public $search          = 'false';
		public $responsive      = '360p';
		public $theme           = 's3bubble_clean';
		public $stream          = 'm4v';
		public $version         = 17;
		private $endpoint       = 'https://api.s3bubble.com/';
		
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
		    $s3bubble_access_key = get_option("s3-s3audible_username");
		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        	$('#TB_ajaxContent').css({
                    	'width' : 'none',
                    	'height' : '470px'
                    });
			        var sendData = {
						AccessKey: '<?php echo $s3bubble_access_key; ?>'
					};
					$.post("<?php echo $this->endpoint; ?>s3media/buckets/", sendData, function(response) {
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
								AccessKey: '<?php echo $s3bubble_access_key; ?>',
								Bucket: bucket
							};
							$.post("<?php echo $this->endpoint; ?>s3media/folders/", data, function(response) {
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
	        	        var shortcode = '[s3bubbleAudio bucket="' + bucket + '" folder="' + folder + '" cloudfront="' + cloudfront + '"  height="' + height + '"  autoplay="' + autoplay + '" playlist="' + playlist + '" ' + order + ' download="' + download + '"  preload="' + preload + '"/]';
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
		    $s3bubble_access_key = get_option("s3-s3audible_username");
		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        	$('#TB_ajaxContent').css({
                    	'width' : 'none',
                    	'height' : '470px'
                    });
			        var sendData = {
						AccessKey: '<?php echo $s3bubble_access_key; ?>'
					};
					$.post("<?php echo $this->endpoint; ?>s3media/buckets/", sendData, function(response) {	
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
								AccessKey: '<?php echo $s3bubble_access_key; ?>',
								Bucket: bucket
							};
							$.post("<?php echo $this->endpoint; ?>s3media/folders/", data, function(response) {
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
						if($("#s3download").is(':checked')){
						    var download = true;
						}else{
						    var download = false;
						}
	        	        var shortcode = '[s3bubbleVideo bucket="' + bucket + '" folder="' + folder + '" cloudfront="' + cloudfront + '"  height="' + height + '"  autoplay="' + autoplay + '" playlist="' + playlist + '" ' + order + ' download="' + download + '"/]';
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
		    $s3bubble_access_key = get_option("s3-s3audible_username");
		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        	$('#TB_ajaxContent').css({
                    	'width' : 'none',
                    	'height' : '450px'
                    });
			        var sendData = {
						AccessKey: '<?php echo $s3bubble_access_key; ?>'
					};
					$.post("<?php echo $this->endpoint; ?>s3media/buckets/", sendData, function(response) {
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
								AccessKey: '<?php echo $s3bubble_access_key; ?>',
								Bucket: bucket
							};
							$.post("<?php echo $this->endpoint; ?>s3media/video_files/", data, function(response) {
								var html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder"><option value="">Choose video</option>';
							    $.each(response, function (i, item) {
							    	var folder = item.Key;
							    	var ext    = folder.split('.').pop();
							    	if(ext == 'mp4' || ext === 'm4v'){
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
						var shortcode = '[s3bubbleVideoSingle bucket="' + bucket + '" track="' + folder + '" cloudfront="' + cloudfront + '" autoplay="' + autoplay + '" download="' + download + '"/]';
						if($("#s3mediaelement").is(':checked')){
						    shortcode = '[s3bubbleMediaElementVideo bucket="' + bucket + '" track="' + folder + '" cloudfront="' + cloudfront + '" autoplay="' + autoplay + '" download="' + download + '"/]';
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
		    $s3bubble_access_key = get_option("s3-s3audible_username");
		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        		
                    $('#TB_ajaxContent').css({
                    	'width' : 'none',
                    	'height' : '450px'
                    }); 
			        var sendData = {
						AccessKey: '<?php echo $s3bubble_access_key; ?>'
					};
					$.post("<?php echo $this->endpoint; ?>s3media/buckets/", sendData, function(response) {
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
								AccessKey: '<?php echo $s3bubble_access_key; ?>',
								Bucket: bucket
							};
							$.post("<?php echo $this->endpoint; ?>s3media/audio_files/", data, function(response) {
								var html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder"><option value="">Choose audio</option>';
							    $.each(response, function (i, item) {
							    	var folder = item.Key;
							    	var ext    = folder.split('.').pop();
							    	if(ext == 'mp3' || ext === 'm4a'){
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
						var shortcode = '[s3bubbleAudioSingle bucket="' + bucket + '" track="' + folder + '" cloudfront="' + cloudfront + '" autoplay="' + autoplay + '" download="' + download + '" style="' + style + '" preload="' + preload + '"/]';
						if($("#s3mediaelement").is(':checked')){
						    shortcode = '[s3bubbleMediaElementAudio bucket="' + bucket + '" track="' + folder + '" cloudfront="' + cloudfront + '" autoplay="' + autoplay + '" download="' + download + '" style="' + style + '" preload="' + preload + '"/]';
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
			add_menu_page( 's3bubble_audio', 'S3Bubble Media', 'manage_options', 's3bubble_audio', array($this, 's3bubble_audio_admin'), plugins_url('assets/images/s3bubblelogo.png',__FILE__ ) );
    	}
        
		/*
		* Add css to wordpress admin to run colourpicker
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_css_admin(){
			wp_register_style( 's3bubble-media-admin-css', plugins_url('assets/css/admin.css', __FILE__) );
			wp_enqueue_style('s3bubble-media-admin-css');
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
			wp_register_style( 'fonts.lato', '//fonts.googleapis.com/css?family=Lato', false, null);
	        wp_enqueue_style('fonts.lato');
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
	            wp_deregister_script( 'jquery' );
	            wp_register_script( 'jquery', '//code.jquery.com/jquery-1.11.0.min.js', false, null);
	            wp_enqueue_script('jquery');
	            wp_register_script( 'jquery-migrate', '//code.jquery.com/jquery-migrate-1.2.1.min.js', false, null);
	            wp_enqueue_script('jquery-migrate');
				wp_register_script( 'jquery.s3player.min', plugins_url('assets/js/jquery.s3player.min.js',__FILE__ ), array(), $this->version );
	            wp_enqueue_script('jquery.s3player.min');
				wp_register_script( 'jquery.s3player.inspector', plugins_url('assets/js/jquery.s3player.inspector.js',__FILE__ ), array(), $this->version );
	            wp_enqueue_script('jquery.s3player.inspector');
				wp_register_script( 'jplayer.s3playlist.min', plugins_url('assets/js/jplayer.s3playlist.min.js',__FILE__ ), array(), $this->version );
	            wp_enqueue_script('jplayer.s3playlist.min');
				wp_register_script( 's3bubble.min', plugins_url('assets/js/s3bubble.min.js',__FILE__ ), array(), $this->version );
	            wp_enqueue_script('s3bubble.min');
				wp_register_script( 'mediaelement-and-player.min', plugins_url('assets/mediaelementjs/build/mediaelement-and-player.min.js',__FILE__ ), array(), $this->version );
	            wp_enqueue_script('mediaelement-and-player.min');
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
				$loggedin			= addslashes($_POST['loggedin']);
				$responsive			= addslashes($_POST['responsive']);
				$theme			    = addslashes($_POST['theme']);

			    // Update the DB with the new option values
				update_option("s3-s3audible_username", $s3audible_username);
				update_option("s3-s3audible_email", $s3audible_email);
				update_option("s3-colour", $colour);
				update_option("s3-loggedin", $loggedin);
				update_option("s3-responsive", $responsive);
				update_option("s3-theme", $theme);

			}
			
			$s3audible_username	= get_option("s3-s3audible_username");
			$s3audible_email	= get_option("s3-s3audible_email");
			$colour				= get_option("s3-colour");	
			$loggedin			= get_option("s3-loggedin");			
			$search			    = get_option("s3-search");
			$responsive			= get_option("s3-responsive");
			$theme			    = get_option("s3-theme");
			$stream			    = get_option("s3-stream");

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
						<h3><span>PLEASE WATCH TUTORIAL VIDEO</span></h3>
						<div class="inside">
							<iframe width="100%" height="315" src="//www.youtube.com/embed/EyBTpJ9GJCw" frameborder="0" allowfullscreen></iframe>
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
							    download: //show download links<br>
							    <p>
						</div>
			            <div class="inside">
			            	<h3><span>Legacy - old plugin versions</span></h3>
			            	<a href="https://isdcloud.s3.amazonaws.com/ver1.7.5/s3bubble_amazon_s3_audio_streaming_2_.zip">Download Last Plugin Version 1.7.5</a><br>
			            	<a href="https://isdcloud.s3.amazonaws.com/ver1.7.3/s3bubble_amazon_s3_audio_streaming_legacy.zip">Download Last Plugin Version 1.7.3</a><br>
			            	<a href="https://isdcloud.s3.amazonaws.com/ver1.7/s3bubble_amazon_s3_audio_streaming_1_.zip">Download Last Plugin Version 1.7.0</a><br>
							<a href="https://isdcloud.s3.amazonaws.com/s3bubble_backups/s3bubble_amazon_s3_audio_streaming.zip">Download Last Plugin Version</a><br>
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
								        <th scope="row" valign="top"><label for="S3Bubble_username">App Access Key:</label></th>
								        <td><input type="text" name="s3audible_username" id="s3audible_username" class="regular-text" value="<?php echo $s3audible_username; ?>"/>
								        	<br />
								       <span class="description">App Access Key can be found <a href="https://s3bubble.com/admin/#/apps" target="_blank">here</a></span>	
								        </td>
								      </tr> 
								       <tr>
								        <th scope="row" valign="top"><label for="s3audible_email">App Secret Key:</label></th>
								        <td><input type="password" name="s3audible_email" id="s3audible_email" class="regular-text" value="<?php echo $s3audible_email; ?>"/>
								        	<br />
								        	<span class="description">App Secret Key can be found <a href="https://s3bubble.com/admin/#/apps" target="_blank">here</a></span>
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
								        <td>S3Bubble now supports RTMP (Real Time Messaging Protocol) Cloudfront Streaming please see this <a href="https://www.youtube.com/watch?v=YZ2QIozCyb8">tutorial</a><br>
								        	The default streaming is Progressive Streaming.
								          <br />
								          <span class="description">Real Time Messaging Protocol streaming is good for power users.</p></td>
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
	   	
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");	
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$stream              = get_option("s3-stream");
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
            
			//set POST variables
			$url = $this->endpoint . 's3media/single_video_object';
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
			//execute post
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
	   	
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");		
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$stream              = get_option("s3-stream");
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

			$url = $this->endpoint . 's3media/single_audio_object';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $bucket,
			    'Key' => $track
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
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");		
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$stream              = get_option("s3-stream");
	        extract( shortcode_atts( array(
				'playlist'   => '',
				'height'     => '',
				'bucket'     => '',
				'folder'     => '',
				'order'      => 'asc',
				'download'   => '',
				'search'     => $search,
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
				'download'   => '',
				'search'     => $search,
				'cloudfront' => '',
				'autoplay'   => 'false',
				'preload'   => 'auto',
			), $atts, 's3audible' ) );
			
			// Check download
			$dc = 'false';
			if($download == 'true'){
				$dc = 'true';
				if($loggedin == 'true'){
					if ( is_user_logged_in() ) {
						$dc = 'true';
					}else{
						$dc = 'false';
					}
				}
			}
		   //set POST variables
			$url = $this->endpoint . 's3media/playlist_audio_objects';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $bucket,
			    'Folder' => $folder
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
			//execute post
		   $result = curl_exec($ch);
           $player_id = uniqid();
           
           return '<div class="s3audible s3bubblePlayer" id="' . $player_id .  '">
			    <div id="jp_container_' . $player_id .  '" class="s3-playlist-wrapper">
			        <div class="s3-type-playlist">
			            <div id="jquery_jplayer_' . $player_id .  '" class="s3-jplayer"></div>
			            <div class="s3-gui s3-gui-audio">
			                <div class="s3-interface interfaceApp-' . $player_id .  '">
			                    <ul class="s3-controls">
			                        <li><a href="javascript:;" class="s3-previous" tabindex="1"><i class="s3icon-backward"></i></a>
			                        </li>
			                        <li><a href="javascript:;" class="s3-play" tabindex="1"><i class="s3icon-play"></i></a>
			                        </li>
			                        <li><a href="javascript:;" class="s3-pause" tabindex="1"><i class="s3icon-pause"></i></a>
			                        </li>
			                        <li><a href="javascript:;" class="s3-next" tabindex="1"><i class="s3icon-forward"></i></a>
			                        </li>
			                    </ul>
			                    <div class="s3-time-container">
			                        <div class="s3-duration"></div>
			                    </div>
			                    <div class="s3bubble-rail s3rail-' . $player_id .  '">
			                        <div class="s3-progress">
			                            <div class="s3-seek-bar">
			                                <div class="s3-play-bar"></div>
			                            </div>
			                        </div>
			                    </div>
			                    <ul class="s3-toggles">
			                        <li><a href="javascript:;" class="s3-playlist-hide' . $player_id .  '" tabindex="1"><i class="s3icon-list"></i></a>
			                        </li>
			                        <li><a href="javascript:;" class="s3-mute" tabindex="1"><i class="s3icon-volume-up"></i></a>
			                        </li>
			                        <li><a href="javascript:;" class="s3-unmute" tabindex="1"><i class="s3icon-volume-off"></i></a>
			                        </li>
			                        <li><a href="javascript:;" class="s3-shuffle" tabindex="1" title="shuffle"><i class="s3icon-random"></i></a>
			                        </li>
			                        <li><a href="javascript:;" class="s3-shuffle-off" tabindex="1" title="shuffle off"><i class="s3icon-random"></i></a>
			                        </li>
			                        <li><a href="javascript:;" data-snum="' . $player_id .  '" class="search-tracks" tabindex="1"><i class="s3icon-search"></i></a>
			                        </li>
			                    </ul>
			                </div>
			            </div>
			            <div class="s3search s3audible-search-' . $player_id .  '" style="display:none;">
			                <input type="text" id="s3bubble-audio-playlist-tsearch-' . $player_id .  '" class="s3bubble-audio-playlist-tsearch" name="s3bubble-audio-playlist-tsearch" placeholder="Search">
			            </div>
			            <div class="s3-playlist s3bubble-audio-playlist-tracksearch-' . $player_id .  '" style="display:'. (($playlist == 'hidden') ? 'none' : 'block' ) .';">
			                <ul class="s3bubble-audio-playlist-ul-' . $player_id .  '">
			                    <li class="list-fix"></li>
			                </ul>
			            </div>
			            <div class="s3-no-solution" style="display:none;"><span>Update Required</span>To play the media you will need to either update your browser to a recent version or update your <a href="https://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.</div>
			        </div>
			    </div>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
				var audioPlaylistS3Bubble = new jPlayerPlaylist({
					jPlayer : "#jquery_jplayer_' . $player_id .  '",
					cssSelectorAncestor : "#jp_container_' . $player_id .  '"
	            }, audioPlaylistS3Bubble, {
	                playlistOptions: {
	                    autoPlay: '.$autoplay.',
	                    displayTime: 0,
	                    download: '.$dc.',
	                    playerWidth: $(this).width(),
	                    enableRemoveControls: false
	                },
	                ready: function(event) {
	                	var res = ' . $result . ';
						if(res.error !== undefined){
							console.log(res.error);
						}else{
							audioPlaylistS3Bubble.setPlaylist(res);
							// hide playlist
							$(".s3audible .s3-playlist-hide' . $player_id .  '").click(function() {
								$(".s3audible .s3bubble-audio-playlist-tracksearch-' . $player_id .  '").slideToggle();
								return false;
							});
							if ("'.$height.'" !== "") {
								$(".s3audible .s3bubble-audio-playlist-tracksearch-' . $player_id .  '").css({
									height : "'.$height.'px",
									"overflow-y" : "scroll"
								});
							}
							// Search tracks
							$(".s3audible .search-tracks").click(function() {
								if ($(".s3audible .s3audible-search-' . $player_id .  '").hasClass("searchOpen")) {
									$(".s3audible .s3audible-search-' . $player_id .  '").fadeOut().removeClass("searchOpen");
								} else {
									$(".s3audible .s3audible-search-' . $player_id .  '").fadeIn().addClass("searchOpen");
								}
								return false;
							});
							$(".s3audible #s3bubble-audio-playlist-tsearch-' . $player_id .  '").keyup(function() {
								var searchText = $(this).val(),
					            $allListElements = $("ul.s3bubble-audio-playlist-ul-' . $player_id .  ' > li"),
					            $matchingListElements = $allListElements.filter(function(i, el){
					                return $(el).text().toLowerCase().indexOf(searchText.toLowerCase()) !== -1;
					            });
								$allListElements.hide();
       							$matchingListElements.show();
							});
						}
	                },
	                swfPath: "https://soaudible.s3.amazonaws.com/audio/Jplayer.swf",
	                preload: "'.$preload.'",
	                supplied: "mp3,m4a",
	                cssSelector: {
	                    play: ".s3-play",
	                    pause: ".s3-pause",
	                    mute: ".s3-mute",
	                    unmute: ".s3-unmute",
	                    seekBar: ".s3-seek-bar",
	                    playBar: ".s3-play-bar",
	                    currentTime: ".s3-current-time",
	                    duration: ".s3-duration"
	                },
	                smoothPlayBar: true,
					keyEnabled: true,
					remainingDuration: true,
					toggleDuration: true,
	                wmode: "window"
	            });
			});
			</script>';
			curl_close($ch);
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
			 $stream              = get_option("s3-stream");
			 extract( shortcode_atts( array(
				'style'      => '',
				'bucket'     => '',
				'track'      => '',
				'stream'     => $stream,
				'download'   => '',
				'cloudfront' => '',
				'autoplay'   => 'false',
				'preload'   => 'auto',
			), $atts, 's3bubbleAudioSingle' ) );
			extract( shortcode_atts( array(
				'style'      => '',
				'bucket'     => '',
				'track'      => '',
				'stream'     => $stream,
				'download'   => '',
				'cloudfront' => '',
				'autoplay'   => 'false',
				'preload'   => 'auto',
			), $atts, 's3audibleSingle' ) );
			
			// Check download
			$dc = 'false';
			if($download == 'true'){
				$dc = 'true';
				if($loggedin == 'true'){
					if ( is_user_logged_in() ) {
						$dc = 'true';
					}else{
						$dc = 'false';
					}
				}
			}
			//set POST variables
			$url = $this->endpoint . 's3media/single_audio_object';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $bucket,
			    'Key' => $track
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
			//execute post
		   $result = curl_exec($ch);
           $player_id = uniqid();
           
           return '<div class="s3audibleSingle s3bubblePlayer" id="' . $player_id .  '">
			    <div id="s3-single-container-' . $player_id .  '" class="s3-playlist-wrapper">
			        <div class="s3-type-playlist">
			            <div id="s3-single-player-' . $player_id .  '" class="s3-jplayer"></div>
			            <div class="s3-gui s3-gui-audio">
			                <div class="s3-interface interfaceApp-' . $player_id .  '">
			                    <ul class="s3-controls">
			                        <li><a href="javascript:;" class="s3-play" tabindex="1"><i class="s3icon-play"></i></a>
			                        </li>
			                        <li><a href="javascript:;" class="s3-pause" tabindex="1"><i class="s3icon-pause"></i></a>
			                        </li>
			                    </ul>
			                    <div class="s3-time-container-single-audio">
			                        <div class="s3-duration"></div>
			                    </div>
			                    <div class="s3bubble-rail s3singlerail-' . $player_id .  '">
			                        <div class="s3-progress">
			                            <div class="s3-seek-bar">
			                                <div class="s3-play-bar"></div>
			                            </div>
			                        </div>
			                    </div>
			                    <ul class="s3-toggles">
			                        <li><a href="javascript:;" class="s3-mute" tabindex="1" title="mute"><i class="s3icon-volume-up"></i></a>
			                        </li>
			                        <li><a href="javascript:;" class="s3-unmute" tabindex="1" title="unmute" style="display: none;"><i class="s3icon-volume-off"></i></a>
			                        </li>
			                    </ul>
			                </div>
			            </div>
			            <div class="s3-playlist">
			                <ul>
			                    <li class="list-fix"></li>
			                </ul>
			            </div>
			            <div class="s3-no-solution" style="display:none;"><span>Update Required</span>To play the media you will need to either update your browser to a recent version or update your <a href="https://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.</div>
			        </div>
			    </div>
			</div>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				var audioSingleS3Bubble = new jPlayerPlaylist({
	                jPlayer: "#s3-single-player-' . $player_id .  '",
	                cssSelectorAncestor: "#s3-single-container-' . $player_id .  '"
	            }, audioSingleS3Bubble, {
	                playlistOptions: {
	                    autoPlay: '.$autoplay.',
	                    displayTime: 0,
	                    download: '.$dc.',
	                    playerWidth: $(this).width(),
	                    enableRemoveControls: false
	                },
	                ready: function(event) {
	                	var res = ' . $result . ';
						if(res.error !== undefined){
							console.log(res.error);
						}else{
		                	audioSingleS3Bubble.setPlaylist(res);
							//Download
							if ('.$dc.' === true) {
								$("#s3-single-container-' . $player_id . ' .s3-gui .s3-toggles").append(\'<li><a  target="_self" href="\' + res[0].download + \'" class="s3-cloud-download" tabindex="1" style="display: block;"><i class="s3icon-cloud-download"></i></a></li>\');
							}
							//Make it plain
							if ("' . $style . '" === "plain") {
								$("#s3-single-container-' . $player_id . '").css({
									overflow : "hidden",
									height : "40px"
								})
							}
						}
	                },
	                swfPath: "https://soaudible.s3.amazonaws.com/audio/Jplayer.swf",
	                preload: "'.$preload.'",
	                supplied: "mp3,m4a",
	                cssSelector: {
	                    play: ".s3-play",
	                    pause: ".s3-pause",
	                    mute: ".s3-mute",
	                    unmute: ".s3-unmute",
	                    seekBar: ".s3-seek-bar",
	                    playBar: ".s3-play-bar",
	                    currentTime: ".s3-current-time",
	                    duration: ".s3-duration"
	                },
	                smoothPlayBar: true,
					keyEnabled: true,
					remainingDuration: true,
					toggleDuration: true,
	                wmode: "window"
	            });
			});
			</script>';
			curl_close($ch);

		}
        
		/*
		* Run the s3bubble jplayer video playlist function
		* @author sameast
		* @none
		*/ 
        function s3bubble_video_player($atts){
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");		
			$loggedin           = get_option("s3-loggedin");
			$search             = get_option("s3-search");
			$responsive         = get_option("s3-responsive");
			$stream             = get_option("s3-stream");
        	 extract( shortcode_atts( array(
				'playlist'   => '',
				'height'     => '',
				'bucket'     => '',
				'folder'     => '',
				'order'      => 'asc',
				'cloudfront' => '',
				'download'   => '',
				'aspect'     => '16:9',
				'search'     => $search,
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
				'download'   => '',
				'aspect'     => '16:9',
				'search'     => $search,
				'responsive' => $responsive,
				'autoplay'   => 'false',
			), $atts, 's3video' ) );
			
			// Check download
			$dc = 'false';
			if($download == 'true'){
				$dc = 'true';
				if($loggedin == 'true'){
					if ( is_user_logged_in() ) {
						$dc = 'true';
					}else{
						$dc = 'false';
					}
				}
			}

            //set POST variables
			$url = $this->endpoint . 's3media/playlist_video_objects';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $bucket,
			    'Folder' => $folder
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
			//execute post
		   $result = curl_exec($ch);
           $player_id = uniqid();
           
           return '<div class="s3bubblePlayer s3video" id="s3video-' . $player_id .  '">
		    <div id="s3-container-video-' . $player_id .  '" class="s3-playlist-wrapper">
		        <div class="s3-type-playlist jp-jplayer">
		            <div class="s3-contain">
		                <img class="s3bubble-loading" src="https://isdcloud.s3.amazonaws.com/ajax_loaders/712.GIF" />
		                <div class="s3-video-play"><a href="javascript:;" class="s3-video-play-icon" tabindex="1"><i class="s3icon-play"></i></a>
		                </div>
		                <div id="s3-jplayer-video-' . $player_id .  '" class="s3-jplayer videoPoster"></div>
		            </div>
		            <div class="s3-gui s3-gui-video">
		                <div class="s3-interface">
		                    <ul class="s3-controls">
		                        <li><a href="javascript:;" class="s3-previous" tabindex="1"><i class="s3icon-backward"></i></a>
		                        </li>
		                        <li><a href="javascript:;" class="s3-play" tabindex="1"><i class="s3icon-play"></i></a>
		                        </li>
		                        <li><a href="javascript:;" class="s3-pause" tabindex="1"><i class="s3icon-pause"></i></a>
		                        </li>
		                        <li><a href="javascript:;" class="s3-next" tabindex="1"><i class="s3icon-forward"></i></a>
		                        </li>
		                    </ul>
		                    <div class="s3-time-container">
		                        <div class="s3-duration"></div>
		                    </div>
		                    <div class="s3bubble-rail">
		                        <div class="s3-progress">
		                            <div class="s3-seek-bar">
		                                <div class="s3-play-bar"></div>
		                            </div>
		                        </div>
		                    </div>
		                    <ul class="s3-toggles">
		                        <li><a href="javascript:;" class="s3-playlist-hide' . $player_id .  '" tabindex="1"><i class="s3icon-list"></i></a>
		                        </li>
		                        <li><a href="javascript:;" class="s3-mute" tabindex="1" title="mute"><i class="s3icon-volume-up"></i></a>
		                        </li>
		                        <li><a href="javascript:;" class="s3-unmute" tabindex="1" title="unmute"><i class="s3icon-volume-off"></i></a>
		                        </li>
		                        <li><a href="javascript:;" class="s3-full-screen" tabindex="1"><i class="s3icon-resize-full"></i></a>
		                        </li>
		                        <li><a href="javascript:;" class="s3-restore-screen" tabindex="1" style="display: block;"><i class="s3icon-resize-small"></i></a>
		                        </li>
		                        <li><a href="javascript:;" class="s3-shuffle-off" tabindex="1" title="shuffle off"><i class="s3icon-random"></i></a>
		                        </li>
		                        <li><a href="javascript:;" data-snum="' . $player_id .  '" class="search-tracks" tabindex="1"><i class="s3icon-search"></i></a>
		                        </li>
		                    </ul>
		                </div>
		            </div>
		            <div class="s3search s3audible-search-' . $player_id .  '" style="display:none;">
		                <input type="text" id="s3bubble-video-playlist-tsearch-' . $player_id .  '" class="s3bubble-video-playlist-tsearch" name="s3bubble-video-playlist-tsearch" placeholder="Search">
		            </div>
		            <div class="s3-playlist s3bubble-video-playlist-tracksearch-' . $player_id .  '" style="display:' . $player_id .  ';">
		                <ul class="s3bubble-video-playlist-ul-' . $player_id .  '">
		                    <li class="list-fix"></li>
		                </ul>
		            </div>
		            <div class="s3-no-solution" style="display:none;"><span>Update Required</span>To play the media you will need to either update your browser to a recent version or update your <a href="https://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.</div>
		        </div>
		    </div>
            <script type="text/javascript">
			jQuery(document).ready(function($) {
				if ("'.$responsive.'" === "270p") {
					aratio = "s3bubble-video-270p";
					wratio = "480px";
					hratio = "270px"
				} else if ("'.$responsive.'" === "360p") {
					aratio = "s3bubble-video-360p";
					wratio = "640px";
					hratio = "360px"
				} else if ("'.$responsive.'" === "responsive") {
					var aspect  = "' . $aspect . '";
					var aspects = aspect.split(":");
					var conWidth = $("#s3-container-video-' . $player_id .  '").width();
					var valueHeight = Math.round((conWidth/aspects[0])*aspects[1]);
					aratio = "s3bubble-video-responsive";
					wratio = "100%";
					hratio = valueHeight
				} else {
					aratio = "s3bubble-video-360p";
					wratio = "640px";
					hratio = "360px"
				}
				var videoPlaylistS3Bubble = new jPlayerPlaylist({
					jPlayer : "#s3-jplayer-video-' . $player_id .  '",
					cssSelectorAncestor : "#s3-container-video-' . $player_id .  '"
				}, videoPlaylistS3Bubble, {
					playlistOptions : {
						autoPlay : '.$autoplay.',
						download : '.$dc.'
					},
					ready : function(event) {
						var res = ' . $result . ';
						if(res.error !== undefined){
							console.log(res.error);
						}else{
							videoPlaylistS3Bubble.setPlaylist(res);
							if (res[0].status === "InProgress") {
								console.log("You cloudfront distribution has not deployed yet please wait normally takes up to 15 minutes... This message will not display once deployed...")
							}
							$("video").bind("contextmenu", function(e) {
								return false
							});
							if ("'.$height.'" !== "") {
								$(".s3video .s3bubble-video-playlist-tracksearch-' . $player_id .  '").css({
									height : "'.$height.'px",
									"overflow-y" : "scroll"
								});
							}
							//hide playlist
							$(".s3video .s3-playlist-hide' . $player_id .  '").click(function() {
								$(".s3video .s3bubble-video-playlist-tracksearch-' . $player_id .  '").slideToggle();
								return false;
							});
							//Search tracks
							$(".s3video .search-tracks").click(function() {
								if ($(".s3video .s3audible-search-' . $player_id .  '").hasClass("searchOpen")) {
									$(".s3video .s3audible-search-' . $player_id .  '").fadeOut().removeClass("searchOpen");
								} else {
									$(".s3video .s3audible-search-' . $player_id .  '").fadeIn().addClass("searchOpen");
								}
								return false;
							});
							
							$("#s3bubble-video-playlist-tsearch-' . $player_id .  '").keyup(function() {
								var searchText = $(this).val(),
					            $allListElements = $("ul.s3bubble-video-playlist-ul-' . $player_id .  ' > li"),
					            $matchingListElements = $allListElements.filter(function(i, el){
					                return $(el).text().toLowerCase().indexOf(searchText.toLowerCase()) !== -1;
					            });
								$allListElements.hide();
       							$matchingListElements.show();
							});
						}
					},
					timeupdate : function(t) {
						if (t.jPlayer.status.currentTime > 1) {
							$(".s3bubble-loading").fadeOut()
						}
					},
					loadedmetadata : function(t) {
						$(".s3bubble-loading").fadeOut()
					},
					loadeddata : function(t) {
						$(".s3bubble-loading").fadeOut()
					},
					emptied : function(t) {
						$(".s3bubble-loading").fadeIn()
					},
					ended : function(t) {
						$(".s3bubble-loading").fadeIn()
					},
					stalled : function(t) {
						$(".s3bubble-loading").fadeIn()
					},
					swfPath : "https://soaudible.s3.amazonaws.com/audio/Jplayer.swf",
					cssSelector : {
						play : ".s3-play",
						pause : ".s3-pause",
						mute : ".s3-mute",
						unmute : ".s3-unmute",
						seekBar : ".s3-seek-bar",
						playBar : ".s3-play-bar",
						currentTime : ".s3-current-time",
						duration : ".s3-duration",
						videoPlay : ".s3-video-play",
						fullScreen : ".s3-full-screen",
						restoreScreen : ".s3-restore-screen",
						repeat : ".s3-repeat",
						repeatOff : ".s3-repeat-off",
						gui : ".s3-gui",
						noSolution : ".s3-no-solution"
					},
					supplied : "m4v",
					smoothPlayBar: true,
					keyEnabled: true,
					remainingDuration: true,
					toggleDuration: true,
					consoleAlerts : true,
					size : {
						width : wratio,
						height : hratio,
						cssClass : aratio
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
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");	
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$responsive          = get_option("s3-responsive");
			$stream              = get_option("s3-stream");
	        extract( shortcode_atts( array(
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => '',
				'download'   => '',
				'aspect'     => '16:9',
				'responsive' => $responsive,
				'autoplay'   => 'false',
			), $atts, 's3bubbleVideoSingle' ) );
			extract( shortcode_atts( array(
				'playlist'   => '',
				'height'     => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => '',
				'download'   => '',
				'aspect'     => '16:9',
				'responsive' => $responsive,
				'autoplay'   => 'false',
			), $atts, 's3videoSingle' ) );
			
			// Check download
			$dc = 'false';
			if($download == 'true'){
				$dc = 'true';
				if($loggedin == 'true'){
					if ( is_user_logged_in() ) {
						$dc = 'true';
					}else{
						$dc = 'false';
					}
				}
			}

			//set POST variables
			$url = $this->endpoint . 's3media/single_video_object';
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
			//execute post
		   $result = curl_exec($ch);
           $player_id = uniqid();
           
           return '<div class="s3videoSingle s3bubblePlayer" id="' . $player_id .  '">
			    <div id="s3-container-video-single-' . $player_id .  '" class="s3-playlist-wrapper">
			        <div class="s3-type-playlist">
			            <div class="s3-contain">
			                <img class="s3bubble-loading" src="https://isdcloud.s3.amazonaws.com/ajax_loaders/712.GIF" />
			                <div class="s3-video-play"><a href="javascript:;" class="s3-video-play-icon" tabindex="1"><i class="s3icon-play"></i></a>
			                </div>
			                <div id="s3-jplayer-video-single-' . $player_id .  '" class="s3-jplayer videoPoster"></div>
			            </div>
			            <div class="s3-gui s3-gui-video" style="display:none;">
			                <div class="s3-interface">
			                    <ul class="s3-controls">
			                        <li><a href="javascript:;" class="s3-play" tabindex="1"><i class="s3icon-play"></i></a>
			                        </li>
			                        <li><a href="javascript:;" class="s3-pause" tabindex="1"><i class="s3icon-pause"></i></a>
			                        </li>
			                    </ul>
			                    <div class="s3-time-container-single-audio">
			                        <div class="s3-duration"></div>
			                    </div>
			                    <div class="s3bubble-rail">
			                        <div class="s3-progress">
			                            <div class="s3-seek-bar">
			                                <div class="s3-play-bar"></div>
			                            </div>
			                        </div>
			                    </div>
			                    <ul class="s3-toggles">
			                        <li><a href="javascript:;" class="s3-full-screen" tabindex="1"><i class="s3icon-resize-full"></i></a>
			                        </li>
			                        <li><a href="javascript:;" class="s3-restore-screen" tabindex="1"><i class="s3icon-resize-small"></i></a>
			                        </li>
			                        <li><a href="javascript:;" class="s3-mute" tabindex="1"><i class="s3icon-volume-up"></i></a>
			                        </li>
			                        <li><a href="javascript:;" class="s3-unmute" tabindex="1"><i class="s3icon-volume-off"></i></a>
			                        </li>
			                    </ul>
			                </div>
			            </div>
			            <div class="s3-playlist" style="display:none;">
			                <ul>
			                    <li class="list-fix"></li>
			                </ul>
			            </div>
			            <div class="s3-no-solution" style="display:none;"><span>Update Required</span>To play the media you will need to either update your browser to a recent version or update your <a href="https://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.</div>
			        </div>
			    </div>
			</div>
            <script type="text/javascript">
				jQuery(document).ready(function($) {
					if ("'.$responsive.'" === "270p") {
						aratio = "s3bubble-video-270p";
						wratio = "480px";
						hratio = "270px"
					} else if ("'.$responsive.'" === "360p") {
						aratio = "s3bubble-video-360p";
						wratio = "640px";
						hratio = "360px"
					} else if ("'.$responsive.'" === "responsive") {
						var aspect  = "' . $aspect . '";
						var aspects = aspect.split(":");
						var conWidth = $("#s3-container-video-single-' . $player_id .  '").width();
						var valueHeight = Math.round((conWidth/aspects[0])*aspects[1]);
						aratio = "s3bubble-video-responsive";
						wratio = "100%";
						hratio = valueHeight
					} else {
						aratio = "s3bubble-video-360p";
						wratio = "640px";
						hratio = "360px"
					}
					var videoSingleS3Bubble = new jPlayerPlaylist({
						jPlayer : "#s3-jplayer-video-single-' . $player_id .  '",
						cssSelectorAncestor : "#s3-container-video-single-' . $player_id .  '"
					}, videoSingleS3Bubble, {
						playlistOptions : {
							autoPlay : '.$autoplay.'
						},
						ready : function(event) {
							var res = ' . $result . ';
							if(res.error !== undefined){
								console.log(res.error);
							}else{
								videoSingleS3Bubble.setPlaylist(res);
								$(".s3-gui").fadeIn();
								$("video").bind("contextmenu", function(e) {
									return false;
								});
								//Download
								if ('.$dc.' === true) {
									$("#s3-container-video-single-' . $player_id .  ' .s3-gui .s3-toggles").append(\'<li><a  target="_self" href="\' + res[0].download + \'" class="s3-cloud-download" tabindex="1" style="display: block;"><i class="s3icon-cloud-download"></i></a></li>\');
								}
								//Firefox fix
								var i = typeof InstallTrigger !== "undefined";
								if (i) {
									var o = document.getElementById("s3-jplayer-video-single-' . $player_id .  '");
									function a() {
										$(".s3-gui").fadeIn();
										document.getElementById("s3-jplayer-video-single-' . $player_id .  '").removeEventListener("mousemove", a, true);
										setTimeout(function() {
											f()
										}, 500)
									}
		
									function f() {
										$(".s3-gui").fadeOut();
										document.getElementById("s3-jplayer-video-single-' . $player_id .  '").addEventListener("mousemove", a, true);
									}
									f();
								}
							}
						},
						timeupdate : function(t) {
							if (t.jPlayer.status.currentTime > 1) {
								$(".s3bubble-loading").fadeOut()
							}
						},
						loadedmetadata : function(t) {
							$(".s3bubble-loading").fadeOut()
						},
						loadeddata : function(t) {
							$(".s3bubble-loading").fadeOut()
						},
						emptied : function(t) {
							$(".s3bubble-loading").fadeIn()
						},
						ended : function(t) {
							$(".s3bubble-loading").fadeIn()
						},
						stalled : function(t) {
							$(".s3bubble-loading").fadeIn()
						},
						swfPath : "https://soaudible.s3.amazonaws.com/audio/Jplayer.swf",
						cssSelector : {
							play : ".s3-play",
							pause : ".s3-pause",
							mute : ".s3-mute",
							unmute : ".s3-unmute",
							seekBar : ".s3-seek-bar",
							playBar : ".s3-play-bar",
							currentTime : ".s3-current-time",
							duration : ".s3-duration",
							videoPlay : ".s3-video-play",
							fullScreen : ".s3-full-screen",
							restoreScreen : ".s3-restore-screen",
							repeat : ".s3-repeat",
							repeatOff : ".s3-repeat-off",
							gui : ".s3-gui",
							noSolution : ".s3-no-solution"
						},
						supplied : "m4v",
						smoothPlayBar: true,
						keyEnabled: true,
						remainingDuration: true,
						toggleDuration: true,
						noConflict : "jQuery",
						consoleAlerts : true,
						warningAlerts : true,
						//errorAlerts : true,
						autohide : {
							full : true,
							restored : true,
							hold : 3000
						},
						size : {
							width : wratio,
							height : valueHeight,
							cssClass : aratio
						}
					});
				});
			</script>';
			curl_close($ch);
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