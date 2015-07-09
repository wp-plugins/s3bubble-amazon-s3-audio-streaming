(function($){
	
	var win = null;
	
	$.fn.videoPlaylist = function(options,callback){
		// Default parameters of the video playlist player:
		options = $.extend({
			url:window.location.href
		}, options);

		var player = '<div id="s3bubble-media-main-container-' + options.Pid + '" class="s3bubble-media-main-video">' +
            	'<div class="s3bubble-media-main-video-playlist-wrap">' +
				    '<div id="jquery_jplayer_' + options.Pid + '" class="s3bubble-media-main-jplayer"></div>' +
				    '<div class="s3bubble-media-main-video-skip">' +
						'<h2>Skip Ad</h2>' +
						'<i class="s3icon s3icon-step-forward"></i>' +
					'</div>' +
					'<div class="s3bubble-media-main-video-search">' +
						'<input type="text" id="s3bubble-video-playlist-tsearch-' + options.Pid +  '" class="s3bubble-video-playlist-tsearch" name="s3bubble-video-playlist-tsearch" placeholder="Search">' +
					'</div>' +
				    '<div class="s3bubble-media-main-video-loading">' +
				    	'<i class="s3icon s3icon-circle-o-notch s3icon-spin"></i>' +
				    '</div>' +
				    '<div class="s3bubble-media-main-video-play">' +
						'<i class="s3icon s3icon-play"></i>' +
					'</div>' +
				    '<div class="s3bubble-media-main-gui" style="visibility: hidden;">' +
				        '<div class="s3bubble-media-main-interface">' +
				            '<div class="s3bubble-media-main-controls-holder">' +
					            '<div class="s3bubble-media-main-left-controls">' +
									'<a href="javascript:;" class="s3bubble-media-main-play" tabindex="1"><i class="s3icon s3icon-play"></i></a>' +
									'<a href="javascript:;" class="s3bubble-media-main-pause" tabindex="1"><i class="s3icon s3icon-pause"></i></a>' +
								'</div>' +
								'<div class="s3bubble-media-main-progress" dir="auto">' +
								    '<div class="s3bubble-media-main-seek-bar" dir="auto">' +
								        '<div class="s3bubble-media-main-play-bar" dir="auto"></div>' +
								    '</div>' +
								'</div>' +
								'<div class="s3bubble-media-main-right-controls">' +
									'<a href="javascript:;" class="s3bubble-media-main-full-screen" tabindex="3" title="full screen"><i class="s3icon s3icon-arrows-alt"></i></a>' +
									'<a href="javascript:;" class="s3bubble-media-main-restore-screen" tabindex="3" title="restore screen"><i class="s3icon s3icon-arrows-alt"></i></a>' +
									'<a href="javascript:;" class="s3bubble-media-main-playlist-list" tabindex="3" title="Playlist List"><i class="s3icon s3icon-list-ul"></i></a>' +
									'<a href="javascript:;" class="s3bubble-media-main-playlist-search" tabindex="3" title="Search List"><i class="s3icon s3icon-search"></i></a>' +
									'<div class="s3bubble-media-main-volume-bar" dir="auto">' +
									    '<div class="s3bubble-media-main-volume-bar-value" dir="auto"></div>' +
									'</div>' +
									'<a href="javascript:;" class="s3bubble-media-main-mute" tabindex="2" title="mute"><i class="s3icon s3icon-volume-up"></i></a>' +
									'<a href="javascript:;" class="s3bubble-media-main-unmute" tabindex="2" title="unmute"><i class="s3icon s3icon-volume-off"></i></a>' +
									'<div class="s3bubble-media-main-time-container">' +
										'<div class="s3bubble-media-main-duration"></div>' +
									'</div>' +
								'</div>' +
				            '</div>' +
				        '</div>' +
				    '</div>' +
			    '</div>' +
			    '<div class="s3bubble-media-main-playlist" style="' + options.Playlist + '">' +
					'<ul class="s3bubble-video-playlist-ul-' + options.Pid +  '">' +
						'<li></li>' +
					'</ul>' +
				'</div>' +
			    '<div class="s3bubble-media-main-no-solution" style="display:none;">' +
			        '<span>Update Required</span>Flash player is needed to use this player please download here. <a href="https://get2.adobe.com/flashplayer/" target="_blank">Download</a>' +
			    '</div>' +
			'</div>';

		// Setu the player
		this.html(player);

		var Current = -1;
		var aspects  = options.Aspect;
		var aspects = aspects.split(":");
		var aspect = $("#s3bubble-media-main-container-" + options.Pid).width()/aspects[0]*aspects[1];
		var IsMobile = false;
		if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
			IsMobile = true;
		}
		$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-playlist-wrap").height(aspect);
		var videoPlaylistS3Bubble = new jPlayerPlaylist({
			jPlayer: "#jquery_jplayer_" + options.Pid,
			cssSelectorAncestor: "#s3bubble-media-main-container-" + options.Pid
		}, videoPlaylistS3Bubble, {
			playlistOptions : {
				autoPlay : options.AutoPlay,
				downloadSet: options.Download
			},
			ready : function(event) {
				var sendData = {
					action: "s3bubble_video_playlist_internal_ajax",
					security : options.Security,
					Timezone :"America/New_York",
				    Bucket : options.Bucket,
				    Folder : options.Folder
				}
				$.post(options.Ajax, sendData, function(response) {
					if(response.error){
						$("#s3bubble-media-main-container-" + options.Pid).append("<span class=\"s3bubble-alert\"><p>" + response.message + ".</p></span>");
						console.log(response.message);
					}else{
						videoPlaylistS3Bubble.setPlaylist(response);
						$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-progress").css("margin","12px 320px 0 40px");
						if(IsMobile){
							$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-progress").css("margin","12px 160px 0 40px");	
						}
						if(response.user === "s2member_level1" || response.user === "s2member_level2"){
							$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-progress").css("margin","12px 360px 0 40px");
							if(IsMobile){
								$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-progress").css("margin","12px 200px 0 40px");	
							}
							$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-right-controls").prepend("<a href=\"https://s3bubble.com/?brand=plugin\" class=\"s3bubble-media-main-logo\"><i id=\"icon-S3\" class=\"icon-S3\"></i></a>");
						}
						$("video").bind("contextmenu", function(e) {
							return false
						});
						//hide playlist
						$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-playlist-list").click(function() {
							$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-playlist").slideToggle();
							return false;
						});
						//Search tracks
						$( "#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-playlist-search" ).click(function() {
							videoPlaylistS3Bubble.pause();
							$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-play").fadeOut();
							$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-search").fadeIn();
							return false;
						});
						$("#s3bubble-video-playlist-tsearch-" + options.Pid).keyup(function() {
							var searchText = $(this).val(),
				            $allListElements = $("#s3bubble-media-main-container-" + options.Pid + " ul.s3bubble-video-playlist-ul-" + options.Pid + " > li"),
				            $matchingListElements = $allListElements.filter(function(i, el){
				                return $(el).text().toLowerCase().indexOf(searchText.toLowerCase()) !== -1;
				            });
							$allListElements.hide();
   							$matchingListElements.show();
						});
						setTimeout(function(){
							if (options.Height !== "") {
								$(".s3bubble-video-playlist-ul-" + options.Pid).css({
									height : options.Height + "px",
									"overflow-y" : "scroll"
								});
							}
							$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeOut();
							$(".s3bubble-media-main-gui").css("visibility", "visible");
						},2000);
					}
				},"json");

			},
			timeupdate : function(t) {
				if (t.jPlayer.status.currentTime > 1) {
					$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeOut();
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
				$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeOut();
			},
			loadeddata : function(t) {
				$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeOut();
			},
			emptied : function(t) {
				$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeIn()
			},
			ended : function(t) {
				$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeIn()
			},
			stalled : function(t) {
				$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeIn()
			},
			waiting: function() {
				$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeIn(); 
			},
			canplay: function() {
				$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeOut(); 
			},
			pause: function() { 

			},
			playing: function() {
				$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeOut();
				$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-search").fadeOut();
				// Reset search
				$("#s3bubble-video-playlist-tsearch-" + options.Pid + "").removeAttr("value");
				$("#s3bubble-media-main-container-" + options.Pid + " ul.s3bubble-video-playlist-ul-" + options.Pid + " > li").show(); 
			},
			play: function() {
				$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-search").fadeOut();
				$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeOut(); 
				var CurrentState = videoPlaylistS3Bubble.current;
				var PlaylistKey  = videoPlaylistS3Bubble.playlist[CurrentState];
				if(IsMobile === false){
					if(PlaylistKey.advert){
						$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-skip").animate({
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
						bucket: options.Bucket,
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
		
		return this.click(function(e){

			e.preventDefault();

		});
		
	};
	
})(jQuery);