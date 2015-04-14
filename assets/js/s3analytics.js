// add a analytic listener
function addListener(obj){
	navigator.s3detectBrowser= (function(){
	    var ua= navigator.userAgent, tem, 
	    M= ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
	    if(/trident/i.test(M[1])){
	        tem=  /\brv[ :]+(\d+)/g.exec(ua) || [];
	        return 'IE '+(tem[1] || '');
	    }
	    if(M[1]=== 'Chrome'){
	        tem= ua.match(/\bOPR\/(\d+)/)
	        if(tem!= null) return 'Opera '+tem[1];
	    }
	    M= M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
	    if((tem= ua.match(/version\/(\d+)/i))!= null) M.splice(1, 1, tem[1]);
	    return M.join(' ');
	})();
	var isMobile = {
    	Android: function() {
	        return navigator.userAgent.match(/Android/i);
	    },
	    BlackBerry: function() {
	        return navigator.userAgent.match(/BlackBerry/i);
	    },
	    iOS: function() {
	        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
	    },
	    Opera: function() {
	        return navigator.userAgent.match(/Opera Mini/i);
	    },
	    Windows: function() {
	        return navigator.userAgent.match(/IEMobile/i) || navigator.userAgent.match(/WPDesktop/i);
	    },
	    any: function() {
	        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
	    }
	};
	jQuery.get("http://ipinfo.io", function(response) {
	    var data = {
			app_id : obj.app_id,
			user_ip : obj.server,	
			user_hostname : response.hostname,	
			user_loc_lat : response.loc,	
			user_loc_lon : response.loc,	
			user_org : response.org,
			user_city : response.city,		
			user_region : response.region,		
			user_country : response.country,		
			user_phone : response.phone,
			bucket : obj.bucket,
			browser : navigator.s3detectBrowser,
			navigator_user_agent : navigator.userAgent,
			navigator_vendor : navigator.vendor,
			navigator_product : navigator.product,
			navigator_hardware : navigator.hardwareConcurrency,
			navigator_cookie : navigator.cookieEnabled,
			navigator_language : navigator.language,
			navigator_languages : JSON.stringify(navigator.languages),
			location_host : location.host,
			location_hostname : location.hostname,
			location_href : location.href,
			location_origin : location.origin,
			location_pathname : location.pathname,
			location_protocol : location.protocol,
			mobile : ((isMobile.any()) ? true : false),
			advert : obj.advert,
			key : obj.key,
			type : obj.type					
		};
		jQuery.post("https://api.s3bubble.com/v1/analytics/add", data, function(response) {
			//console.log(response);
		}, "json");
	}, "jsonp");
}