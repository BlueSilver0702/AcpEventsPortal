/*--- jQuery Custom Select ---*/
;(function(){
	"use strict";

	function CustomSelect(thisDOMObj, config){
		this.dataItem = jQuery(thisDOMObj);
		if (typeof config !== 'string' && !this.dataItem.data(dataName)) { // init custom select
			// default options
			this.options = jQuery.extend({
				selectStructure: '<div class="selectArea"><div class="left"></div><div class="center"></div><a href="#" class="selectButton">&nbsp;</a><div class="disabled"></div></div>', // fake select structure
				optStructure: '<div class="selectOptions"><div class="select-top"></div><div class="select-list"><ul></ul></div><div class="select-bottom"></div></div>', // option list structure
				optItemStructure: '<li><a href="#"><span></span></a></li>', // option item structure
				optItemText: 'span', // selector of option item text
				selectDisabled: '.disabled', // selector of disable block when select has attr disabled
				selectBtn: '.selectButton', // selector of opener
				hideClass: 'outtaHere', // hide class for select
				selectText: '.center', // selector of select text
				activeSelectClass: 'selectAreaActive', // active class
				optionScrollBox: '.select-list', // selector of scroll box
				optList: 'ul', // selector of options list
				itemClassAttr: 'data-type', // attr for option item class
				appendIn: false, // options list will be append in this block
				withWindowScroll: false, // scoll options drop with window (for popups)
				defaultText: false, // placeholder text
				maxHeight: 99999, // max height for scroll block
				onChange: null, // onchage callback
				onShow: null, // onshow callback
				onHide: null, // onhide callback
				onInit: null, // oninit callback
				upend: false, // options list will be opened before select
				delay: 200, // delay before hide options drop
				updateOnResize: false, // update select on window resize
				touchDropDefault: false // default functionality in option list on touch devices
			}, config);
			
			this.options.touchDropDefault = this.options.touchDropDefault && isTouchDevice;

			if (this.options.touchDropDefault) {
				this.options.hideClass = this.options.hideClass + '-touch';
			}

			this.init();
		}
		return this;
	}

	CustomSelect.prototype = {
		// init function
		init: function(){
			if (this.dataItem.data(dataName)) {
				return;
			}
			// add api in data select
			this.dataItem.data(dataName, this);

			this.createElements();
			this.createStructure();
			this.attachEvents();
			this.dataItem.addClass(this.options.hideClass);

			// init callback
			if (typeof this.options.onInit === 'function') {
				this.options.onInit(this.getUI());
			}
		},
		getUI: function(){
			return {
				select: this.dataItem[0],
				fakeSelect: this.fakeSelect,
				optHolder: this.options.touchDropDefault ? jQuery() : this.optHolder
			};
		},
		// attach events and listeners
		attachEvents: function(){
			this.changeEvent = this.bindScope(function(event){
				if (this.options.touchDropDefault) {
					var curOpt = this.dataItem.find('option').eq(this.dataItem[0].selectedIndex);
					this.toggleClassSelect(curOpt[0]);
					this.selectText.html(curOpt.html());
				}
				// change callback
				if (typeof this.options.onChange === 'function') {
					this.options.onChange(event, this.getUI());
				}
			});
			this.dataItem.on({'change': this.changeEvent});
			if (!this.options.touchDropDefault) {
				// hover event
				this.optHolder.add(this.fakeSelect).on({
					'mouseenter': this.bindScope(function(){
						if (this.optTimer) {
							clearTimeout(this.optTimer);
						}
					}),
					'mouseleave': this.bindScope(function(){
						this.optTimer = setTimeout(this.bindScope(function(){
							this.toggleState(false);
						}), this.options.delay);
					})
				});
				// click on select opener event
				this.selectBtn.on({
					'click': this.bindScope(function(event){
						event.preventDefault();
						if (this.optHolder.is(':visible')) {
							this.toggleState(false);
						} else {
							if (this.optHolder.show().find(this.options.optionScrollBox).height() > this.options.maxHeight) {
								this.optHolder.hide().find(this.options.optionScrollBox).css({
									'height': this.options.maxHeight,
									'overflow': 'auto',
									'overflow-x': 'hidden'
								});
							}
							this.toggleState(true);
							this.updatePosition(true);
						}
					})
				});
				// on window scroll event when oprion list is opened
				if (this.options.withWindowScroll) {
					this.onWinScroll = this.bindScope(function(){
						if (this.optHolder && this.optHolder.length && this.optHolder.is(':visible')) {
							this.toggleState(true, true);
						}
					});
					this.win.on({'scroll': this.onWinScroll});
				}
				if (this.options.updateOnResize) {
					this.onResize = this.bindScope(function(){
						if (this.optHolder && this.optHolder.length && this.optHolder.is(':visible')) {
							this.toggleState(true, true);
						} else if (this.optHolder && this.optHolder.length && !this.optHolder.is(':visible')) {
							this.setWidth();
						}
					});
					this.win.on({'resize load ready orientationchange': this.onResize});
				}
			}
		},
		// set width on fake select and options list
		setWidth: function(show){
			var replacedWidth = this.dataItem.outerWidth();
			this.fakeSelect.width(replacedWidth);
			if (!this.options.touchDropDefault) {
				this.optHolder[show ? 'show' : 'hide']().css({
					width: replacedWidth,
					position: 'absolute'
				});
			}
		},
		updatePosition: function(show){
			if (show) {
				if (this.options.upend) {
					this.optHolder.css({
						bottom: this.document.height() - this.fakeSelect.offset().top,
						left: this.fakeSelect.offset().left,
						top: 'auto'
					});
				} else {
					this.optHolder.css({
						top: this.fakeSelect.offset().top + this.fakeSelect.outerHeight(),
						left: this.fakeSelect.offset().left
					});
				}
			}
		},
		// show or hide options list
		toggleState: function(show, scroll){
			if (this.options.touchDropDefault) {
				return;
			}
			if (show) {
				this.updatePosition(show);
				if (!scroll) {
					this.setWidth(true);
					this.optHolder.show(0, this.bindScope(function(){
						// show callback
						if (typeof this.options.onShow === 'function') {
							this.options.onShow(this.getUI());
						}
					}));
					this.fakeSelect.addClass(this.options.activeSelectClass);
				}
			} else {
				this.fakeSelect.removeClass(this.options.activeSelectClass);
				if (!this.optHolder.is(':visible')) {
					return;
				}
				this.optHolder.hide(0, this.bindScope(function(){
					// hide callback
					if (typeof this.options.onHide === 'function') {
						this.options.onHide(this.getUI());
					}
				}));
			}
		},
		// create api elements
		createElements: function(){
			this.optTimer = null;
			this.win = jQuery(window);
			this.document = jQuery(document);
			this.body = jQuery(document.body);
			this.fakeSelect = jQuery(this.options.selectStructure);
			this.selectText = this.fakeSelect.find(this.options.selectText);
			this.selectBtn = this.fakeSelect.find(this.options.selectBtn);
			this.selectDisabled = this.fakeSelect.find(this.options.selectDisabled)[this.dataItem.is(':disabled') ? 'show' : 'hide']();
			if (!this.options.touchDropDefault) {
				this.optHolder = jQuery(this.options.optStructure);
				this.optList = this.optHolder.find(this.options.optList);
			}
			this.prevSelectClass = '';
		},
		// create custom select struct
		createStructure: function(){
			if (!this.options.touchDropDefault) {
				this.optionItems = jQuery();
				this.optionLinks = jQuery();
				this.selectOptions = jQuery();
				this.optList.empty();

				this.dataItem.find('option').each(this.bindScope(function(index, thisDOMObj){
					var tempItem = jQuery(this.options.optItemStructure), thisObj = jQuery(thisDOMObj);
					tempItem.addClass(thisDOMObj.getAttribute(this.options.itemClassAttr) || '').find(this.options.optItemText).html(thisObj.html());
					if (!!thisObj.attr('selected')) {
						this.selectText.html(thisObj.html());
						tempItem.addClass('selected');
						this.toggleClassSelect(thisDOMObj);
					}
					if (!!thisObj.attr('disabled')) {
						tempItem.addClass('disabled');
					}
					// on click fake option
					tempItem.find('a').on({
						'click': this.bindScope(function(event){
							event.preventDefault();
							var newVal = thisDOMObj.value || thisObj.text();
							this.optionItems.removeClass('selected');
							this.selectOptions.removeAttr('selected');
							tempItem.addClass('selected');
							thisObj.attr('selected', 'selected');
							this.selectText.html(thisObj.html());
							this.dataItem.val(newVal).trigger('change');
							this.toggleClassSelect(thisDOMObj);
							this.toggleState(false);
						})
					});

					// add items in api varibles
					this.optionItems = this.optionItems.add(tempItem);
					this.optionLinks = this.optionLinks.add(tempItem.find('a'));
					this.selectOptions = this.selectOptions.add(thisObj);
					// add custom item in list
					this.optList.append(tempItem);
				}));

				// set class from select
				if (this.dataItem.attr('class') && this.dataItem.attr('class') !== 'outtaHere') {
					this.optHolder.addClass('drop-' + this.dataItem.attr('class').split(' ')[0]);
				}

				// append custom select structure in html document
				if (this.options.appendIn && this.options.appendIn.length) {
					this.options.appendIn.append(this.optHolder.addClass(this.dataItem.attr('id') || ''));
				} else {
					this.body.append(this.optHolder.addClass(this.dataItem.attr('id') || ''));
				}
			} else if (this.dataItem.find('option[selected]').length) {
				var curOpt = this.dataItem.find('option[selected]');
				this.toggleClassSelect(curOpt[0]);
				this.selectText.html(curOpt.html());
			}

			// set default text
			if (!this.dataItem.find('option[selected]').length && this.options.defaultText !== false) {
				var defaultText = '';
				switch (typeof this.options.defaultText) {
					case 'string':
						defaultText = this.options.defaultText;
					break;
					case 'function':
						defaultText = this.options.defaultText(this.dataItem[0]);
					break;
					default:
						defaultText = '';
				}
				this.selectText.html(defaultText);
			}
			this.fakeSelect.addClass(this.dataItem.attr('id') || '').insertBefore(this.dataItem);
			this.setWidth();
		},
		// api update function
		update: function(){
			this.fakeSelect.detach();
			if (!this.options.touchDropDefault) {
				this.optHolder.detach();
			}
			this.createStructure();
			this.fakeSelect.find(this.options.selectDisabled)[this.dataItem[0].disabled ? 'show' : 'hide']();
			this.setWidth();
			// init callback
			if (typeof this.options.onInit === 'function') {
				this.options.onInit(this.getUI(), true);
			}
		},
		toggleClassSelect: function(opt){
			opt = opt || this.dataItem.find('option:selected')[0];
			this.fakeSelect.removeClass(this.prevSelectClass);
			this.prevSelectClass = opt.getAttribute(this.options.itemClassAttr) || '';
			this.fakeSelect.addClass(this.prevSelectClass);
		},
		// api destroy function
		destroy: function(){
			this.dataItem.removeClass(this.options.hideClass);
			this.dataItem.off('change', this.changeEvent || null);
			this.fakeSelect.detach();
			if (!this.options.touchDropDefault) {
				this.optHolder.detach();
			}
			if (this.options.withWindowScroll) {
				this.win.off('scroll', this.onWinScroll || null);
			}
			if (this.options.updateOnResize) {
				this.win.off('resize', this.onResize || null);
			}
			this.dataItem.removeData(dataName);
		},
		bindScope: function(func, scope){
			return jQuery.proxy(func, scope || this);
		}
	};

	jQuery.fn.customSelect = function(config, param){
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
			} else if (typeof CustomSelect.prototype[config] === 'function') {
				return this.each(function(){
					var curData = jQuery(this).data(dataName);
					if (curData) {
						curData[config](param);
					}
				});
			} else if (CustomSelect.prototype[config]) {
				return this.eq(0).data(dataName)[config];
			} else {
				return this;
			}
		} else {
			return this.each(function(){
				new CustomSelect(this, config);
			});
		}
	};

	var dataName = 'CustomSelect',
		isTouchDevice = /MSIE 10.*Touch/.test(navigator.userAgent) || ('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch,
		arrNames = ['bindScope', 'getUI'];

}(jQuery));