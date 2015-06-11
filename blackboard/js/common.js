function initPage() {
	"use strict";

	if (!window.application) {
		window.application = {};
	}

	// application initializaion starts
	var mainFuncName = 'init', subFuncArr = ['init'];
	window.application[mainFuncName] = function () {
		var i = null, j = null;
		for (i in this) {
			if (this.hasOwnProperty(i)) {
				if (i !== mainFuncName) {
					if (typeof this[i] === 'function') {
						try {
							this[i]();
						} catch (e) {}
					} else if (subFuncArr.length === 1 && typeof this[i][subFuncArr[0]] === 'function') {
						try {
							this[i][subFuncArr[0]]();
						} catch (e) {}
					} else {
						for (j = 0; j < subFuncArr.length; j = j + 1) {
							if (typeof this[i][subFuncArr[j]] === 'function') {
								try {
									this[i][subFuncArr[j]]();
								} catch (e) {}
							}
						}
					}
				}
			}
		}
	};

	// application initialization ends
	window.application[mainFuncName]();
}

if (document.addEventListener) {
	document.addEventListener('DOMContentLoaded', function () {
		"use strict";

		initPage();
	}, false);
} else if (document.attachEvent) {
	document.attachEvent('onreadystatechange', function () {
		"use strict";

		if (document.readyState === "complete") {
			initPage();
		}
	});
}

var reqSent = false;

function acpAjaxRespHandler(data) {
	if (data.match('CMD_REDIR') && data.match('CMD_MSG')) {
		var redirpos = data.indexOf('CMD_REDIR');
		var msgpos = data.indexOf('CMD_MSG');
		if (redirpos < msgpos) {
			var redir = data.substring(redirpos + 9, msgpos);
			var msg = data.substr(msgpos + 7);
		}
		else {
			var msg = data.substring(msgpos + 7, redirpos);
			var redir = data.substr(redirpos + 9);
		}
		jQuery('form.ajax').remove();
		jQuery('#form-msg').show();
		jQuery('#form-msg').html(msg);
		window.setTimeout(function () {
			window.location = redir;
		}, 2000);
	}
	else if (data.match('CMD_REDIR')) {
		window.location = data.substr(9);
	}
	else if (data.match('CMD_ERRORS')) {
		jQuery('#error-msg').show();
		jQuery('#error-msg').html(data.substr(10));
	}
	else if (data.match('CMD_MSG')) {
		jQuery('form.ajax').remove();
		jQuery('#form-msg').show();
		jQuery('#form-msg').html(data.substr(7));
	}
	reqSent = false;
}

jQuery(document).ready(function() {
	//jQuery('form.ajax').submit(function(e) {
	jQuery(document).on('submit', 'form.ajax', function(e) {
		e.preventDefault();
		e.stopPropagation();
		
		if (reqSent) {
			return false;
		}
		reqSent = true;
		
		jQuery('#form-msg').hide();
		jQuery('#error-msg').hide();
		
		var data = jQuery(this).serialize();
		jQuery.ajax({
			type: 'POST',
			url: wpAjaxHandler,
			data: data,
			success: acpAjaxRespHandler,
			error: function() { reqSent = false; }
		});
		return false;
	});
});