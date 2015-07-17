(function($){
	
	var win = null;
	
	$.fn.singleVideo = function(options,callback){
		// Default parameters of the single video player:
		options = $.extend({
			url:window.location.href
		}, options);

		var player = '<div id="s3bubble-media-main-container-' + options.Pid + '" class="s3bubble-media-main-video">' +
			    '<div id="jquery_jplayer_' + options.Pid + '" class="s3bubble-media-main-jplayer"></div>' +
			    '<div class="s3bubble-media-main-video-skip">' +
					'<h2>Skip Ad</h2>' +
					'<i class="s3icon s3icon-step-forward"></i>' +
				'</div>' +
                '<div class="s3bubble-media-main-preview-over-container">' +
                    '<div class="s3bubble-media-main-preview-over">' +
                    '</div>' +
                    '<h2>Preview Over</h2>' +
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
			    '<div class="s3bubble-media-main-playlist" style="display:none !important;">' +
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
        var aspects = options.Aspect;
        var aspects = aspects.split(":");
        var aspect = $("#s3bubble-media-main-container-" + options.Pid).width() / aspects[0] * aspects[1];
        var IsMobile = false;
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            IsMobile = true;
        }
        var videoSingleS3Bubble = new jPlayerPlaylist({
            jPlayer: "#jquery_jplayer_" + options.Pid,
            cssSelectorAncestor: "#s3bubble-media-main-container-" + options.Pid
        }, videoSingleS3Bubble, {
            playlistOptions: {
                autoPlay: options.AutoPlay,
                downloadSet: options.Download
            },
            ready: function(event) {
                var sendData = {
                    action: "s3bubble_video_single_internal_ajax",
                    security: options.Security,
                    Timezone: "America/New_York",
                    Bucket: options.Bucket,
                    Key: options.Key,
                    Cloudfront: options.Cloudfront,
                    Server: s3bubble_all_object.serveraddress
                }
                $.post(options.Ajax, sendData, function(response) {
                    if (response.error) {
                        $("#s3bubble-media-main-container-" + options.Pid).append("<span class=\"s3bubble-alert\"><p>" + response.message + ".</p></span>");
                        console.log(response.message);
                    } else {
                        videoSingleS3Bubble.setPlaylist(response.results);

                        $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-progress").css("margin", "12px 240px 0 40px");
                        if (IsMobile) {
                            $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-progress").css("margin", "12px 60px 0 40px");
                        }
                        if (response.user === "s2member_level1" || response.user === "s2member_level2") {
                            $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-progress").css("margin", "12px 280px 0 40px");
                            if (IsMobile) {
                                $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-progress").css("margin", "12px 100px 0 40px");
                            }
                            $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-right-controls").prepend("<a href=\"https://s3bubble.com/?brand=plugin\" class=\"s3bubble-media-main-logo\"><i id=\"icon-S3\" class=\"icon-S3\"></i></a>");
                        }
                        if (options.Download) {
                            if (options.Twitter) {
                                $("#s3bubble-media-main-container-" + options.Pid).prepend("<a href=\"#\" class=\"s3bubble-cloud-download\" title=\"Free Download\"><i class=\"s3icon s3icon-cloud-download\"></i></a>");
                                $(".s3bubble-cloud-download").tweetAction({
                                    text: options.TwitterText,
                                    url: window.href,
                                    via: options.TwitterHandler,
                                    related: options.TwitterHandler
                                }, function() {
                                    window.open(response.results[0].download);
                                });
                            } else {
                                $("#s3bubble-media-main-container-" + options.Pid + "").prepend("<a href=\"" + response.results[0].download + "\" class=\"s3bubble-cloud-download\" title=\"Free Download\"><i class=\"s3icon s3icon-cloud-download\"></i></a>");
                            }
                        }
                        $("video").bind("contextmenu", function(e) {
                            return false;
                        });
                        $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-skip").on("click", function() {
                            $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeIn();
                            $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-skip").animate({
                                left: "-120"
                            }, 50, function() {
                                videoSingleS3Bubble.next();
                            });
                        });
                        setTimeout(function() {
                            $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeOut();
                            $(".s3bubble-media-main-gui").css("visibility", "visible");
                        }, 2000);
                        if(options.Start){
		            		$("#jquery_jplayer_" + options.Pid).jPlayer("playHead", options.Start);
		            	}
                    }
                }, "json");
            },
            timeupdate: function(event) {
                var CurrentState = videoSingleS3Bubble.current;
                var PlaylistKey = videoSingleS3Bubble.playlist[CurrentState];
                if (PlaylistKey.advert && IsMobile === false) {

                }else{
                	if(options.Finish){
                		var perc = event.jPlayer.status.currentPercentAbsolute;
                		if(Math.round(perc) > options.Finish){
                			$("#jquery_jplayer_" + options.Pid).jPlayer("pause");
                            $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-preview-over-container").fadeIn();
                		}
                	}
                }
                if (event.jPlayer.status.currentTime > 1) {
                    $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeOut();
                }
            },
            resize: function(event) {

            },
            click: function(event) {
                if (event.jPlayer.status.paused) {
                    videoSingleS3Bubble.play();
                } else {
                    videoSingleS3Bubble.pause();
                }
            },
            error: function(event) {
                console.log(event.jPlayer.error);
                console.log(event.jPlayer.error.type);
            },
            loadedmetadata: function(t) {
                $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeOut();
            },
            loadeddata: function(t) {
                $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeOut();
            },
            emptied: function(t) {
                $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeIn()
            },
            ended: function(t) {
                $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeIn();
                var CurrentState = videoSingleS3Bubble.current;
                var PlaylistKey = videoSingleS3Bubble.playlist[CurrentState];
                if (PlaylistKey.advert && IsMobile === false) {
                    $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-skip").animate({
                        left: "0"
                    }, 50, function() {
                        // Animation complete.
                    });
                } else {
                    $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-skip").animate({
                        left: "-120"
                    }, 50, function() {

                    });
                }
            },
            stalled: function(t) {
                $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeIn()
            },
            waiting: function() {
                $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeIn();
            },
            canplay: function() {
                $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeOut();
            },
            pause: function() {
                $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-cloud-download").fadeIn();
            },
            playing: function() {
                $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeOut();
            },
            play: function() {
                $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-cloud-download").fadeOut();
                $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-loading").fadeOut();
                var CurrentState = videoSingleS3Bubble.current;
                var PlaylistKey = videoSingleS3Bubble.playlist[CurrentState];
                if (PlaylistKey.advert && IsMobile === false) {
                    $("#s3bubble-media-main-container-" + options.Pid + " .s3bubble-media-main-video-skip").animate({
                        left: "0"
                    }, 50, function() {
                        // Animation complete.
                    });
                }
                if (Current !== CurrentState && PlaylistKey.advert !== true) {
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
                fullScreen: {
                    key: 70, // F key
                    fn: function(f) {
                        if (f.status.video || f.options.audioFullScreen) {
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
                },
                speedUp: { //S key
                    key: 83,
                    fn: function(f) {
                        f.playbackRate(f.status.playbackRate + 0.1);
                    }
                },
                slowDown: {
                    key: 65, //A key
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
            supplied: "m4v",
            useStateClassSkin: true,
            autoBlur: false,
            smoothPlayBar: false,
            keyEnabled: true,
            remainingDuration: true,
            size: {
                width: "100%",
                height: aspect
            },
            autohide: {
                full: true,
                restored: true,
                hold: 3000
            }
        });

        return this.click(function(e) {
            e.preventDefault();
        });

    };

})(jQuery);