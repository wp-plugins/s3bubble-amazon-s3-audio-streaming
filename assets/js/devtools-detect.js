/*!
	devtools-detect
	Detect if DevTools is open
	https://github.com/sindresorhus/devtools-detect
	by Sindre Sorhus
	MIT License
*/
(function () {
	'use strict';
	var devtools = {open: false};
	var threshold = 160;
	var CustomEvent;
	CustomEvent = function(event, params) {
	  var evt;
	  params = params || {
	    bubbles: false,
	    cancelable: false,
	    detail: undefined
	  };
	  evt = document.createEvent("CustomEvent");
	  evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
	  return evt;
	};
	
	CustomEvent.prototype = window.Event.prototype;
	
	window.CustomEvent = CustomEvent;
	var emitEvent = function (state) {
		window.dispatchEvent(new CustomEvent('devtoolschange', {
			detail: {
				open: state
			}
		}));
	};

	setInterval(function () {
		if ((window.Firebug && window.Firebug.chrome && window.Firebug.chrome.isInitialized) || window.outerWidth - window.innerWidth > threshold ||
			window.outerHeight - window.innerHeight > threshold) {
			if (!devtools.open) {
				//emitEvent(true);
			}
			devtools.open = true;
			window.location.assign("https://s3bubble.com/s3bubble-security");
		} else {
			if (devtools.open) {
				//emitEvent(false);
			}
			devtools.open = false;
		}
	}, 4000);

	if (typeof module !== 'undefined' && module.exports) {
		module.exports = devtools;
	} else {
		window.devtools = devtools;
	}
})();