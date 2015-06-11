/*--- jQuery Image Stretch ---*/
;(function(){
	"use strict";

	jQuery.fn.imgStretch = function(){
		return this.each(function(){
			var cur = jQuery(this),
				curWidth = cur.css('width', '').width(),
				curHeight = cur.css('height', '').height(),
				parent = cur.parent(),
				parentWidth = parent.width(),
				parentHeight = parent.height(),
				diffCur = curHeight/curWidth,
				settings = {};
			if (curWidth/parentWidth < curHeight/parentHeight) {
				settings = {
					height: parentWidth * diffCur,
					marginLeft: 0,
					marginTop: -(parentWidth * diffCur - parentHeight)/2,
					width: parentWidth
				};
			} else {
				settings = {
					height: parentHeight,
					marginLeft: -(parentHeight/diffCur - parentWidth)/2,
					marginTop: 0,
					width: parentHeight/diffCur
				};
			}
			cur.css(settings);
		});
	};
}(jQuery));