/*--- jQuery Custom Checkbox ---*/
;(function(){
	"use strict";

	function CustomCheckbox(thisDOMObj, config){
		this.dataItem = jQuery(thisDOMObj);
		if(typeof config !== 'string' && !this.dataItem.data(dataName)){ // init custom checkbox
			// default options
			this.options = jQuery.extend({
				checkboxStructure: '<div></div>', // HTML struct for custom checkbox
				checkboxDisabled: 'disabled', // disabled class name
				checkboxDefault: 'checkboxArea', // default class name
				checkboxChecked: 'checkboxAreaChecked', // checked class name
				hideClass: 'outtaHere', // hide class for checkbox
				onInit: null, // oninit callback
				onChange: null // onchage callback
			}, config);

			this.init();
		}
		return this;
	}

	CustomCheckbox.prototype = {
		// init function
		init: function(){
			if (this.dataItem.data(dataName)) {
				return;
			}
			// add api in data checkbox
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
				checkbox: this.dataItem[0],
				fakecheckbox: this.fakecheckbox
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
					} else {
						this.dataItem.attr('checked', 'checked');
						this.dataItem[0].checked = true;
					}
				}
				this.toggleState();
				// change callback
				if(typeof this.options.onChange === 'function'){
					this.options.onChange(event, this.getUI());
				}
			});
			this.fakeCheckbox.on({'click': this.clickEvent});
			this.dataItem.on({'click': this.clickEvent});
		},
		// checked or disabled checkbox
		toggleState: function(){
			this.fakeCheckbox.removeAttr('class').addClass(this.options[this.dataItem[0].checked ? 'checkboxChecked' : 'checkboxDefault']);
		},
		// create api elements
		createElements: function(){
			this.fakeCheckbox = jQuery(this.options.checkboxStructure);
		},
		// create custom checkbox struct
		createStructure: function(){
			if (this.dataItem.is(':disabled')) {
				this.fakeCheckbox.addClass(this.options.checkboxDisabled);
			} else if (this.dataItem.is(':checked')) {
				this.fakeCheckbox.addClass(this.options.checkboxChecked);
			} else {
				this.fakeCheckbox.addClass(this.options.checkboxDefault);
			}
			this.fakeCheckbox.insertBefore(this.dataItem);
		},
		// api update function
		update: function(){
			this.fakeCheckbox.detach();
			this.fakeCheckbox = jQuery(this.options.checkboxStructure);
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
			this.fakeCheckbox.detach();
			this.dataItem.removeClass(this.options.hideClass);
			this.dataItem.off('click', this.clickEvent || null);
			this.dataItem.removeData(dataName);
		},
		bindScope: function(func, scope){
			return jQuery.proxy(func, scope || this);
		}
	};

	jQuery.fn.customCheckbox = function(config, param){
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
			} else if (typeof CustomCheckbox.prototype[config] === 'function') {
				return this.each(function(){
					var curData = jQuery(this).data(dataName);
					if (curData) {
						curData[config](param);
					}
				});
			} else if (CustomCheckbox.prototype[config]) {
				return this.eq(0).data(dataName)[config];
			} else {
				return this;
			}
		} else {
			return this.each(function(){
				new CustomCheckbox(this, config);
			});
		}
	};

	var dataName = 'CustomCheckbox',
		arrNames = ['bindScope', 'getUI'];

}(jQuery));