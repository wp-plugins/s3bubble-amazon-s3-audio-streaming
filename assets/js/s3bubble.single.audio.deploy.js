(function($){
	
	var win = null;
	
	$.fn.singleAudio = function(options,callback){
		// Default parameters of the single video player:
		options = $.extend({
			url:window.location.href
		}, options);
		var player = '<div id="s3bubble-media-main-container-' + options.Pid + '" class="s3bubble-media-main-audio">' +
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
			    '<div class="s3bubble-media-main-playlist">' +
					'<ul>' +
						'<li></li>' +
					'</ul>' +
				'</div>' +
			    '<div class="s3bubble-media-main-no-solution" style="display:none;">' +
			        '<span>Update Required</span>Flash player is needed to use this player please download here. <a href="https://get2.adobe.com/flashplayer/" target="_blank">Download</a>' +
			    '</div>' +
			'</div>';
		
		this.html(player);

		var Current = -1;
		var aspect = $("#s3bubble-media-main-container-1").width()/16*9;
		var IsMobile = false;
		if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
			IsMobile = true;
		}
			
		var audioSingleS3Bubble = new jPlayerPlaylist({
            jPlayer: "#jquery_jplayer_" + options.Pid,
			cssSelectorAncestor: "#s3bubble-media-main-container-" + options.Pid
        }, audioSingleS3Bubble, {
            playlistOptions: {
                autoPlay : options.AutoPlay,
				downloadSet: options.Download,
                displayTime: 0,
                playerWidth: $(this).width(),
                enableRemoveControls: false
            },
            ready: function(event) {
				var sendData = {
					action: "s3bubble_audio_single_internal_ajax",
					security : options.Security,
					Timezone :"America/New_York",
				    Bucket : options.Bucket,
				    Key : options.Key,
				    Cloudfront : options.Cloudfront,
				    Server : s3bubble_all_object.serveraddress
				}
				$.post(options.Ajax, sendData, function(response) {
					
					$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-audio-loading").fadeOut();
					$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-controls-holder").fadeIn();
					
					if(response.error){
						$("#s3bubble-media-main-container-" + options.Pid + "").append("<span class=\"s3bubble-alert\"><p>" + response.message + ".</p></span>");
						console.log(response.message);
					}else{
						audioSingleS3Bubble.setPlaylist(response);
						$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-progress").css("margin","12px 200px 0 40px");
						if(IsMobile){
							$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-progress").css("margin","12px 60px 0 40px");	
						}
						if(response.user === "s2member_level1" || response.user === "s2member_level2"){
							$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-progress").css("margin","12px 240px 0 40px");
							if(IsMobile){
								$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-progress").css("margin","12px 100px 0 40px");	
							}
							$("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-right-controls").prepend("<a href=\"https://s3bubble.com/?brand=plugin\" class=\"s3bubble-media-main-logo\"><i id=\"icon-S3\" class=\"icon-S3\"></i></a>");
						}
						//Make it plain
						if (options.Styles === "plain") {
							$("#s3bubble-media-main-container-" + options.Pid).css({
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
						bucket: options.Bucket,
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
						$(".single-audio-volume-" + options.Pid).val(f.options.volume + 0.1);
		            }
		        },
		        volumeDown: {
		            key: 188,
		            fn: function(f) {
		                f.volume(f.options.volume - 0.1);
						$(".single-audio-volume-" + options.Pid).val(f.options.volume - 0.1);
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
		
		return this.click(function(e){

			e.preventDefault();
		});
		
	};
	
})(jQuery);