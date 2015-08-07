(function($){
	
	var win = null;
	
	$.fn.audioPlaylist = function(options,callback){
		// Default parameters of the video playlist player:
		options = $.extend({
			url:window.location.href
		}, options);

		var player = '<div id="s3bubble-media-main-container-' + options.Pid + '" class="s3bubble-media-main-audio">' +
            	'<div class="s3bubble-media-main-video-playlist-wrap">' +
				    '<div id="jquery_jplayer_' + options.Pid + '" class="s3bubble-media-main-jplayer"></div>' +
				    '<div class="s3bubble-media-main-gui">' +
				        '<div class="s3bubble-media-main-interface s3bubble-media-main-interface-audio-playlist">' +
				        	'<div class="s3bubble-media-main-audio-loading">' +
						    	'<i class="s3icon s3icon-circle-o-notch s3icon-spin"></i>' +
						    '</div>' +
				            '<div class="s3bubble-media-main-controls-holder" style="display:none;">' +
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
			    '<div class="s3search s3audible-search-' + options.Pid + '" style="display:none;">' +
	                '<input type="text" id="s3bubble-audio-playlist-tsearch-' + options.Pid + '" class="s3bubble-audio-playlist-tsearch" name="s3bubble-audio-playlist-tsearch" placeholder="Search">' +
	            '</div>' +
	            '<div class="s3bubble-media-main-playlist s3bubble-audio-playlist-tracksearch-' + options.Pid + '" style="display:' + options.Playlist + ';">' +
					'<ul class="s3bubble-audio-playlist-ul-' + options.Pid + '">' +
						'<li>&nbsp;</li>' +
					'</ul>' +
				'</div>' +
			    '<div class="s3bubble-media-main-no-solution" style="display:none;">' +
			        '<span>Update Required</span>Flash player is needed to use this player please download here. <a href="https://get2.adobe.com/flashplayer/" target="_blank">Download</a>' +
			    '</div>' +
			'</div>';

		// Setu the player
		this.html(player);

		var Current = -1;
		var OldKey;
		var OldEndTime;
		var OldCurrentTime;
		var MissFirst = false;
		var IsMobile = false;				
		if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
			IsMobile = true;
		}

		var audioPlaylistS3Bubble = new jPlayerPlaylist({
            jPlayer: "#jquery_jplayer_" + options.Pid,
			cssSelectorAncestor: "#s3bubble-media-main-container-" + options.Pid
        }, audioPlaylistS3Bubble, {
            playlistOptions: {
                autoPlay : options.AutoPlay,
				downloadSet: options.Download,
                displayTime: 0,
                playerWidth: $(this).width(),
                enableRemoveControls: false
            },
            ready: function(event) {
				var sendData = {
					action : "s3bubble_audio_playlist_internal_ajax",
					security : options.Security,
					Timezone :"America/New_York",
				    Bucket : options.Bucket,
				    Folder : options.Folder
				}
				$.post(options.Ajax, sendData, function(response) {
					
					$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-audio-loading").fadeOut();
					$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-controls-holder").fadeIn();
					
					if(response.error){
						$("#s3bubble-media-main-container-" + options.Pid + "").append("<span class=\"s3bubble-alert\"><p>" + response.message + ".</p></span>");
						console.log(response.message);
					}else{

						audioPlaylistS3Bubble.setPlaylist(response);
						$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-progress").css("margin","12px 280px 0 40px");
						if(IsMobile){
							$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-progress").css("margin","12px 150px 0 40px");	
						}
						if(response.user === "s2member_level1" || response.user === "s2member_level2"){
							$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-progress").css("margin","12px 320px 0 40px");
							if(IsMobile){
								$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-progress").css("margin","12px 190px 0 40px");	
							}
							$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-right-controls").prepend("<a href='https://s3bubble.com/?brand=plugin' class='s3bubble-media-main-logo'><i id='icon-S3' class='icon-S3'></i></a>");
						}
						// hide playlist 
						$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-playlist-list").click(function() {
							$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-playlist").slideToggle();
							return false;
						});
						if (options.Height !== "") {
							$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-playlist").css({
								height : options.Height + "px",
								"overflow-y" : "scroll"
							});
						}
						// Search tracks
						$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-playlist-search").click(function() {
							if ($("#s3bubble-media-main-container-" + options.Pid + " .s3audible-search-" + options.Pid).hasClass("searchOpen")) {
								$("#s3bubble-media-main-container-" + options.Pid + " .s3audible-search-" + options.Pid).fadeOut().removeClass("searchOpen");
							} else {
								$("#s3bubble-media-main-container-" + options.Pid + " .s3audible-search-" + options.Pid).fadeIn().addClass("searchOpen");
							}
							return false;
						});
						$("#s3bubble-media-main-container-" + options.Pid + " #s3bubble-audio-playlist-tsearch-" + options.Pid).keyup(function() {
							var searchText = $(this).val(),
				            $allListElements = $("#s3bubble-media-main-container-" + options.Pid + " ul.s3bubble-audio-playlist-ul-" + options.Pid + " > li"),
				            $matchingListElements = $allListElements.filter(function(i, el){
				                return $(el).text().toLowerCase().indexOf(searchText.toLowerCase()) !== -1;
				            });
							$allListElements.hide();
   							$matchingListElements.show();
						});
						var CurrentState = audioPlaylistS3Bubble.current;
						var PlaylistKey  = audioPlaylistS3Bubble.playlist[CurrentState];
						window.s3bubbleAnalytics = {
	                        app_id: s3bubble_all_object.s3appid,
	                        server: s3bubble_all_object.serveraddress,
	                        bucket: options.Bucket,
	                        key: PlaylistKey.key,
	                        type: "audio",
	                        advert: false,
	                        time_watched: 0,
	                        overall_watched: 0
	                    };
					}
				},"json");
			},
			timeupdate : function(event) {
				var CurrentTime = event.jPlayer.status.currentTime;
                var EndTime = event.jPlayer.status.duration; 
				if (CurrentTime > 1) {
					$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeOut();
					OldEndTime = EndTime;
					OldCurrentTime = CurrentTime;
                    var CurrentState = audioPlaylistS3Bubble.current;
					var PlaylistKey  = audioPlaylistS3Bubble.playlist[CurrentState];
					OldKey = PlaylistKey.key;
				}
			},
			play: function() { 
				if(MissFirst){
					window.s3bubbleAnalytics.time_watched = OldCurrentTime;
	                window.s3bubbleAnalytics.overall_watched = OldEndTime;
	                window.s3bubbleAnalytics.key = OldKey;
					addListener(window.s3bubbleAnalytics);
				}
				MissFirst = true;
			},
			loadedmetadata: function() {

			},
			resize: function (event) {
				
		    	
		    },
		    click: function (event) {

		    },
		    error: function (event) {
		    	console.log(event.jPlayer.error);
				console.log(event.jPlayer.error.type);
		    },
			waiting: function() {
				
			},
			canplay: function() {

			},
			pause: function() { 

			},
			playing: function() {

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
            preload: options.Preload,
            supplied: "mp3,m4a",
            wmode: "window",
			useStateClassSkin: true,
			autoBlur: false,
			smoothPlayBar: false,
			keyEnabled: true,
			audioFullScreen: true,
			remainingDuration: true
		});	
		
		return this.click(function(e){

			e.preventDefault();

		});
		
	};
	
})(jQuery);