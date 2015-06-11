if (!window.application) {
	window.application = {};
}

window.application.initPlugins = function () {
	'use strict';

	var checkedClass = 'checked';

	function toogleChecked (element, flag) {
		if (flag) {
			element.addClass(checkedClass);
		} else {
			element.removeClass(checkedClass);
		}
	}

	// lightbox initialization
	if (typeof jQuery === 'function' && typeof jQuery.lightbox === 'function') {
		jQuery.lightbox({
			attr: 'data-href',
			closeLinks: '.close-lightbox, .lightbox .close',
			layoutStyles: {
				backgroundColor: '#4c4c4c'
			}
		});
	}

	// placeholder initialization
	if (typeof jQuery === 'function' && typeof jQuery.fn.placeholder === 'function') {
		jQuery('input, textarea').placeholder();
	}

	// custom radio initialization
	if (typeof jQuery == 'function' && typeof jQuery.fn.сustomRadio == 'function') {
		jQuery('.main-form input[type="radio"]').сustomRadio();
	}

	// custom select initialization
	if (typeof jQuery == 'function' && typeof jQuery.fn.customSelect == 'function') {
		jQuery('.main-form select').customSelect({
			defaultText: function(select){
				return select.getAttribute('data-placeholder');
			},
			maxHeight: 145,
			selectStructure: '<div class="selectArea"><div class="left"></div><div class="center"></div><a href="#" class="selectButton"><i class="ico">&nbsp;</i></a></div></div>'
		});
	}

	// image stretch initialization
	if (typeof jQuery == 'function' && typeof jQuery.fn.imgStretch == 'function') {
		var images = jQuery('.bg-holder img').imgStretch();
			jQuery(window).on({
				'resize orientationchange load': function(){
					images.imgStretch();
			}
		});
	}

	// custom checkbox initialization
	if (typeof jQuery == 'function' && typeof jQuery.fn.customCheckbox == 'function') {
		var checkbox = jQuery('.details-form input[type="checkbox"]');

		checkbox.customCheckbox({
			onInit: function (ui) {
				var cur = jQuery(ui.checkbox),
					curLi = cur.closest('li'),
					curDiv = jQuery(ui.checkbox.previousSibling),
					checkedFlag = curDiv.hasClass('checkboxArea') ? false : true;

				toogleChecked(curLi, checkedFlag);

			},
			onChange: function (event) {
				var curLi = $(event.currentTarget).closest('li'),
					checkedFlag = curLi.hasClass(checkedClass) ? false : true;

				toogleChecked(curLi, checkedFlag);
			}
		});
	}

	// open-close initialization
	if (typeof jQuery == 'function' && typeof jQuery.fn.openClose == 'function') {
		jQuery('.faq .list li').openClose({
			effect:'none'
		});
	}

	// custom input type file initialization
	if (typeof jQuery == 'function' && typeof jQuery.fn.nicefileinput == 'function') {
		jQuery("input[type=file]").nicefileinput({
			label: 'text here <div class="img-holder"><img src="images/content/img-photo-example.png" alt="img-description" width="57" height="57"></div>'
		});
	}
};