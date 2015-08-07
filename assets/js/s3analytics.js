function addListener(a) {
    if(a){
        navigator.s3BrowserDetect= (function(){
            var ua= navigator.userAgent, tem, 
            M= ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
            if(/trident/i.test(M[1])){
                tem=  /\brv[ :]+(\d+)/g.exec(ua) || [];
                return 'IE '+(tem[1] || '');
            }
            if(M[1]=== 'Chrome'){
                tem= ua.match(/\bOPR\/(\d+)/);
                if(tem!= null) return 'Opera '+tem[1];
            }
            M= M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
            if((tem= ua.match(/version\/(\d+)/i))!= null) M.splice(1, 1, tem[1]);
            return M.join(' ');
        })();
        var s3Device = {
            Android: function() {
                return navigator.userAgent.match(/Android/i)
            },
            BlackBerry: function() {
                return navigator.userAgent.match(/BlackBerry/i)
            },
            iOS: function() {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i)
            },
            Opera: function() {
                return navigator.userAgent.match(/Opera Mini/i)
            },
            Windows: function() {
                return navigator.userAgent.match(/IEMobile/i) || navigator.userAgent.match(/WPDesktop/i)
            },
            any: function() {
                return s3Device.Android() || s3Device.BlackBerry() || s3Device.iOS() || s3Device.Opera() || s3Device.Windows()
            }
        };
        var sendData = {
            app_id: s3bubble_all_object.s3appid,
            user_ip: s3bubble_all_object.serveraddress,
            bucket: a.bucket,
            browser: navigator.s3BrowserDetect,
            navigator_user_agent: navigator.userAgent,
            navigator_vendor: navigator.vendor,
            navigator_product: navigator.product,
            navigator_hardware: navigator.hardwareConcurrency,
            navigator_cookie: navigator.cookieEnabled,
            navigator_language: navigator.language,
            navigator_languages: JSON.stringify(navigator.languages),
            location_host: location.host,
            location_hostname: location.hostname,
            location_href: location.href,
            location_origin: location.origin,
            location_pathname: location.pathname,
            location_protocol: location.protocol,
            mobile: s3Device.any() ? !0 : !1,
            advert: a.advert,
            key: a.key,
            type: a.type,
            time_watched: Math.round(a.time_watched),
            overall_watched :  Math.round(a.overall_watched)
        };
        jQuery.ajax({
            url: "https://s3api.com/v1/analytics/add",
            dataType: "json",
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            data: sendData,
            async: false, //blocks window close
            success: function( data, textStatus, jQxhr ){
                var country = data.analytics.user_country.toLowerCase();
                var Map = '<iframe style="width:100%;" height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=' + data.analytics.user_loc_lat + '&hl=' + country + ';z=14&amp;output=embed"></iframe>';
                jQuery(".s3bubble-output-analytics").html(Map);
            },
            error: function( jqXhr, textStatus, errorThrown ){
                console.log( errorThrown );
            }
        });
    }
}