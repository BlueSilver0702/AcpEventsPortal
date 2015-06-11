/*--- jQuery Custom Radio ---*/
;(function(){
	"use strict";

	function CustomRadio(thisDOMObj, config){
		this.dataItem = jQuery(thisDOMObj);
		if(typeof config !== 'string' && !this.dataItem.data(dataName)){ // init custom radio
			// default options
			this.options = jQuery.extend({
				radioStructure: '<div></div>', // HTML struct for custom radio
				radioDisabled: 'disabled', // disabled class name
				radioDefault: 'radioArea', // default class name
				radioChecked: 'radioAreaChecked', // checked class name
				hideClass: 'outtaHere', // hide class for radio
				onInit: null, // oninit callback
				onChange: null // onchage callback
			}, config);

			this.init();
		}
		return this;
	}

	CustomRadio.prototype = {
		// init function
		init: function(){
			if (this.dataItem.data(dataName)) {
				return;
			}
			// add api in data radio
			this.dataItem.data(dataName, this);

			this.createElements();
			this.createStructure();
			this.attachEvents();
			this.dataItem.addClass(this.options.hideClass);

			// init callback
			if(typeof this.options.onInit === 'function'){
				this.options.onInit(this.getUI());
			}
		},
		getUI: function(){
			return {
				radio: this.dataItem[0],
				fakeRadio: this.fakeRadio
			};
		},
		// attach events and listeners
		attachEvents: function(){
			if (this.dataItem.is(':disabled')) {
				return;
			}
			this.clickEvent = this.bindScope(function(event){
				if(event.target !== this.dataItem[0]){
					if (this.dataItem[0].checked) {
						this.dataItem.removeAttr('checked');
						this.dataItem[0].checked = false;
					}
				}
				this.toggleState();
				// change callback
				if(typeof this.options.onChange === 'function'){
					this.options.onChange(event, this.getUI());
				}
			});
			this.fakeRadio.on({'click': this.clickEvent});
			this.dataItem.on({'click': this.clickEvent});
		},
		// checked or disabled radio
		toggleState: function(){
			jQuery('input:radio[name=' + this.dataItem.attr("name") + ']').not(this.dataItem).each(function(){
				var cur = jQuery(this),
					curAPI = cur.data(dataName),
					curUI = null;
				cur.removeAttr('checked');
				this.checked = false;
				if(curAPI){
					curUI = curAPI.getUI();
					if(curUI.fakeRadio && !cur.is(':disabled')){
						curUI.fakeRadio.removeAttr('class').addClass(curAPI.options.radioDefault);
					}
				}
			});
			this.dataItem.attr('checked', 'checked');
			this.dataItem[0].checked = true;
			this.fakeRadio.removeAttr('class').addClass(this.options.radioChecked);
		},
		// create api elements
		createElements: function(){
			this.fakeRadio = jQuery(this.options.radioStructure);
		},
		// create custom radio struct
		createStructure: function(){
			if (this.dataItem.is(':disabled')) {
				this.fakeRadio.addClass(this.options.radioDisabled);
			} else if (this.dataItem.is(':checked')) {
				this.fakeRadio.addClass(this.options.radioChecked);
			} else {
				this.fakeRadio.addClass(this.options.radioDefault);
			}
			this.fakeRadio.insertBefore(this.dataItem);
		},
		// api update function
		update: function(){
			this.fakeRadio.detach();
			this.fakeRadio = jQuery(this.options.radioStructure);
			this.dataItem.off('click', this.clickEvent);
			this.createStructure();
			this.attachEvents();
			// init callback
			if(typeof this.options.onInit === 'function'){
				this.options.onInit(this.getUI(), true);
			}
		},
		// api destroy function
		destroy: function(){
			this.fakeRadio.detach();
			this.dataItem.removeClass(this.options.hideClass);
			this.dataItem.off('click', this.clickEvent || 0);
			this.dataItem.removeData(dataName);
		},
		bindScope: function(func, scope){
			return jQuery.proxy(func, scope || this);
		}
	};

	jQuery.fn.сustomRadio = function(config, param){
		var tempData = {};
		if (!this.length) {
			return this;
		} else if (typeof config === 'string') {
			for (var i = 0; i < arrNames.length; i++) {
				if (arrNames[i] === config) {
					tempData = true;
				}
			}
			if (tempData === true) {
				tempData = this.eq(0).data(dataName);
				if (typeof tempData[config] === 'function') {
					return tempData[config](param) || this;
				} else if (tempData[config]) {
					return tempData[config];
				} else {
					return this;
				}
			} else if (typeof CustomRadio.prototype[config] === 'function') {
				return this.each(function(){
					var curData = jQuery(this).data(dataName);
					if (curData) {
						curData[config](param);
					}
				});
			} else if (CustomRadio.prototype[config]) {
				return this.eq(0).data(dataName)[config];
			} else {
				return this;
			}
		} else {
			return this.each(function(){
				new CustomRadio(this, config);
			});
		}
	};

	var dataName = 'CustomRadio',
		arrNames = ['bindScope', 'getUI'];

}(jQuery));