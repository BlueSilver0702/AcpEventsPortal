/*--- jQuery Lightbox ---*/
;(function(){
	"use strict";

	function Lightbox(thisDOMObj, config){
		this.dataItem = jQuery(thisDOMObj);
		if (typeof config !== 'string' && !this.dataItem.data(dataName)) { // init clone items
			// default options
			this.options = jQuery.extend({
				openLinks: '.open-lightbox', // links open lightbox 
				closeLinks: '.close-lightbox', // links close lightbox
				ajaxClass: 'ajax-load', // class name for links with ajax
				ajaxLightbox: 'ajax-lightbox', // class name for lightbox with ajax
				ajaxAttr: 'data-url', // attribute name for ajax url in lightbox
				attr: 'href', // attribute name for ajax url in links
				animSpeed: 300, // animation speed
				layout: '<div class="lightbox-layout"></div>', // layout html structure
				layoutParent: jQuery(document.body), // layout parent for append
				activeClass: 'active', // class name for opened state of lightbox
				zIndex: 1000, // z-index for layout
				ajaxRemove: false, // remove ajax lightbox after close
				onBeforeLoad: null,
				onAfterLoad: null,
				onBeforeOpen: null,
				onAfterOpen: null,
				onBeforeClose: null,
				onAfterClose: null,
				onBeforeAppend: null,
				onAfterAppend: null,
				parseRespond: function (r) {
					return r;
				}, // function for parse ajax answer
				ajaxDataType: '', // ajax data type
				layoutStyles: {} // layout css styles
			}, config);

			this.options.layoutStyles = jQuery.extend({ // default layout css styles
				opacity: '.8',
				backgroundColor: '#000',
				position: 'fixed',
				overflow: 'hidden',
				display: 'none',
				top: 0,
				left: 0,
				width: '100%',
				minHeight: '100%',
				zIndex: this.options.zIndex
			}, jQuery.isPlainObject(this.options.layoutStyles) ? this.options.layoutStyles : {});

			this.layout = this.options.layoutParent.find(this.layout);
			if (!this.layout.length) {
				this.layout = jQuery(this.options.layout).css(this.options.layoutStyles).appendTo(this.options.layoutParent);
			}

			this.init();
		}
		return this;
	}

	Lightbox.prototype = {
		// init function
		init: function(){
			if (this.dataItem.data(dataName)) {
				return;
			}
			// add api in data item
			this.dataItem.data(dataName, this);

			this.createElements();
			this.attachEvents();

			// init callback
			if (typeof this.options.onInit === 'function') {
				this.bindScope(this.options.onInit)(this.getUI());
			}
		},
		isCurrentOpen: function(){
			return this.currentLightbox ? this.currentLightbox.hasClass(this.options.activeClass) : false;
		},
		closeCurrent: function(handler){
			if (this.isCurrentOpen()) {
				this.toggleState('hide', true, handler);
			} else if (typeof handler === 'function') {
				handler();
			}
		},
		toggleState: function(state, justLightbox, handler){
			var self = this, temp;
			justLightbox = justLightbox && this.layout.is(':visible');
			if (justLightbox) {
				if (state === 'hide') {
					if (typeof this.options.onBeforeClose === 'function') {
						this.options.onBeforeClose(this.currentLightbox);
					}
					if (!this.currentLightbox.length) {
						return;
					}
					this.currentLightbox.removeClass(this.options.activeClass).stop().fadeOut(this.options.animSpeed, jQuery.proxy(function(){
						if (this.currentLightbox.hasClass(this.options.ajaxLightbox)) {
							this.currentLightbox.detach();
						} else {
							this.setPosition(true);
						}
						if (typeof this.options.onAfterClose === 'function') {
							this.options.onAfterClose(this.currentLightbox);
						}
						this.currentLightbox = false;
						if (typeof handler === 'function') {
							handler();
						}
						
					}, this));
				} else {
					if (typeof this.options.onBeforeOpen === 'function') {
						this.options.onBeforeOpen(this.currentLightbox);
					}
					this.setPosition();
					this.currentLightbox.addClass(this.options.activeClass).hide().stop().fadeIn(this.options.animSpeed, function(){
						if (typeof this.options.onAfterOpen === 'function') {
							this.options.onAfterOpen(this.currentLightbox);
						}
						if (typeof handler === 'function') {
							handler();
						}
					});
				}
			} else {
				if (!this.currentLightbox.length) {
					return;
				}
				if (state === 'hide') {
					if (typeof this.options.onBeforeClose === 'function') {
						this.options.onBeforeClose(this.currentLightbox);
					}
					this.currentLightbox.stop().fadeOut(this.options.animSpeed, jQuery.proxy(function(){
						this.currentLightbox.removeClass(this.options.activeClass);
						if (this.currentLightbox.hasClass(this.options.ajaxLightbox)) {
							this.currentLightbox.detach();
						} else {
							this.setPosition(true);
						}
						temp = this.currentLightbox;
						this.currentLightbox = false;
						this.layout.stop().fadeOut(this.options.animSpeed, jQuery.proxy(function(){
							if (typeof this.options.onAfterClose === 'function') {
								this.options.onAfterClose(temp);
							}
							if (typeof handler === 'function') {
								handler();
							}
						}, this));
					}, this));
				} else {
					if (typeof this.options.onBeforeOpen === 'function') {
						this.options.onBeforeOpen(this.currentLightbox);
					}
					this.setPosition();
					this.currentLightbox.hide();
					this.layout.stop().fadeIn(this.options.animSpeed, function(){
						self.currentLightbox.addClass(self.options.activeClass).stop().fadeIn(self.options.animSpeed, function(){
							if (typeof self.options.onAfterOpen === 'function') {
								self.options.onAfterOpen(self.currentLightbox);
							}
						});
					});
				}
			}
		},
		setPosition: function(hide){
			if (hide) {
				this.currentLightbox.css({
					position: 'absolute',
					right: 'auto',
					left: '-9999px',
					top: '-9999px',
					bottom: 'auto',
					width: '',
					display: 'block'
				});
			} else {
				this.currentLightbox.css({
					position: 'absolute',
					width: '',
					zIndex: parseInt(this.options.zIndex) + 1
				});

				var windowHeight = this.win.height(),
					lightboxWidth = this.currentLightbox.outerWidth(),
					lightboxHeight = this.currentLightbox.outerHeight(),
					pageWidth = this.page.width(),
					winScrollTop = parseInt(this.win.scrollTop());

				// vertical position
				if (windowHeight > lightboxHeight) {
					this.currentLightbox.css({
						position: 'fixed',
						top: (windowHeight - lightboxHeight) / 2
					});
				} else {
					this.currentLightbox.css({
						top: winScrollTop
					});
				}

				// horizontal position
				if (pageWidth > lightboxWidth) {
					this.currentLightbox.css({
						left: (pageWidth - lightboxWidth) / 2
					});
				} else {
					this.currentLightbox.css({
						left: 0,
						width: '100%'
					});
				}
			}
		},
		open: function(obj){
			if (!obj) {
				return;
			}
			obj.type = obj.type || 'none';
			if (obj.type.toLowerCase() === 'ajax') {
				this.closeCurrent(jQuery.proxy(function(){
					this.sendRequest({
						url: obj.string
					});
				}, this));
			} else {
				this.closeCurrent(jQuery.proxy(function(){
					this.currentLightbox = jQuery(obj.string);
					this.toggleState('show');
				}, this));
			}
		},
		close: function(){
			this.toggleState('hide');
		},
		sendRequest: function(settings){
			if (typeof this.options.onBeforeLoad === 'function') {
				this.options.onBeforeLoad();
			}
			if (jQuery('[' + this.options.ajaxAttr + '="' + settings.url + '"]').length) {
				this.currentLightbox = jQuery('[' + this.options.ajaxAttr + '="' + settings.url + '"]');
				if (typeof this.options.onAfterLoad === 'function') {
					this.options.onAfterLoad(this.currentLightbox);
				}
				this.toggleState('show');
			} else {
				jQuery.ajax(jQuery.extend({
					dataType: this.options.ajaxDataType || 'html',
					success: jQuery.proxy(function(ajaxData){
						var images, imgLength = 0, tempImg = jQuery(), self;
						if (typeof this.options.parseRespond === 'function') {
							ajaxData = this.options.parseRespond(ajaxData);
						}
						if (typeof ajaxData !== 'string') {
							return;
						}
						this.currentLightbox = jQuery(jQuery.trim(ajaxData)).filter('*').eq(0);
						if (typeof this.options.onBeforeAppend === 'function') {
							this.options.onBeforeAppend(this.currentLightbox);
						}
						if (this.options.ajaxRemove) {
							this.currentLightbox.addClass(this.options.ajaxLightbox);
						} else {
							this.currentLightbox.attr(this.options.ajaxAttr, settings.url);
						}
						this.currentLightbox.css({
							position: 'absolute',
							top: '-9999px',
							left: '-9999px'
						}).appendTo(this.options.layoutParent);
						this.toggleState('show');
						images = this.currentLightbox.find('img');
						if (typeof this.options.onAfterAppend === 'function') {
							this.options.onAfterAppend(this.currentLightbox);
						}
						self = this;
						if (images.length) {
							images.each(function(){
								var cur = jQuery(this);
								tempImg = tempImg.add(jQuery('<img>', {
									load: function() {
										imgLength++;
										if (imgLength === images.length) {
											tempImg.detach();
											if (typeof self.options.onAfterLoad === 'function') {
												self.options.onAfterLoad(self.currentLightbox);
											}
										}
									},
									error: function() {
										imgLength++;
										if (imgLength === images.length) {
											tempImg.detach();
											if (typeof self.options.onAfterLoad === 'function') {
												self.options.onAfterLoad(self.currentLightbox);
											}
										}
									},
									src: cur.attr('src')
								}).css({
									'position': 'absolute',
									'top': '-9999px',
									'left': '-9999px'
								}).appendTo(jQuery('body')));
							});
						} else {
							if (typeof self.options.onAfterLoad === 'function') {
								self.options.onAfterLoad(self.currentLightbox);
							}
						}
					}, this),
					error: function(){
						console.log('ajax error');
					}
				}, jQuery.isPlainObject(settings) ? settings : {}));
			}
		},
		// attach events and listeners
		attachEvents: function(){
			var self = this;

			this.openHandler = jQuery.proxy(function(event){
				var curLink = jQuery(event.currentTarget);
				event.preventDefault();
				this.closeCurrent(jQuery.proxy(function(){
					if (curLink.hasClass(this.options.ajaxClass)) {
						this.sendRequest({
							url: curLink.attr(this.options.attr)
						});
					} else {
						this.currentLightbox = jQuery(curLink.attr(this.options.attr));
						this.toggleState('show');
					}
				}, this));
			}, this);

			this.closeHandler = function(event){
				event.preventDefault();
				self.toggleState('hide');
			};
			this.keyCloseHandler = function(event){
				event = event || window.event;
				if (event.keyCode === 27) {
					self.toggleState('hide');
				}
			};
			this.onResizeHandler = function(){
				if (self.isCurrentOpen()) {
					self.setPosition();
				}
			};

			this.layout.on('click', this.closeHandler);
			this.win.on('resize orientationchange', this.onResizeHandler);
			this.dataItem.on('click', this.options.openLinks, this.openHandler);
			this.dataItem.on('click', this.options.closeLinks, this.closeHandler);
			this.page.on('keydown', this.keyCloseHandler);
		},
		// create api elements
		createElements: function(){
			this.currentLightbox = false;
			this.win = jQuery(window);
			this.page = jQuery(document);
		},
		// api destroy function
		destroy: function(){
			this.layout.off('click', this.closeHandler);
			this.win.off('resize orientationchange', this.onResizeHandler);
			this.dataItem.off('click', this.options.openLinks, this.openHandler);
			this.dataItem.off('click', this.options.closeLinks, this.closeHandler);
			this.page.off('keydown', this.keyCloseHandler);
			this.dataItem.removeData(dataName);
		}
	};

	jQuery.extend({
		lightbox: function(config, item, param){
			var tempData = {};
			if (typeof config === 'string' && item) {
				if (typeof item === 'string' || !item.jquery) {
					item = jQuery(item);
				}
				if (!item.length) {
					return jQuery();
				}
				tempData = item.eq(0).data(dataName);
				if (tempData) {
					if (typeof tempData[config] === 'function') {
						return tempData[config](param) || item;
					} else if (tempData[config]) {
						return tempData[config];
					} else {
						return item;
					}
				} else {
					return jQuery();
				}
			} else if (typeof config === 'string' && !item && Lightbox.prototype[config]) {
				item = document;
				tempData = item.eq(0).data(dataName);
				if (tempData) {
					if (typeof tempData[config] === 'function') {
						return tempData[config](param) || item;
					} else if (tempData[config]) {
						return tempData[config];
					} else {
						return item;
					}
				} else {
					return jQuery();
				}
			} else if (config && !jQuery.isPlainObject(config) && !item) {
				item = config;
				config = undefined;
				if (typeof item === 'string' || !item.jquery) {
					item = jQuery(item);
				}
				return item.each(function(){
					new Lightbox(this, config);
				});
			} else if (jQuery.isPlainObject(config) || !config) {
				item = item || document;
				if (typeof item === 'string' || !item.jquery) {
					item = jQuery(item);
				}
				return item.each(function(){
					new Lightbox(this, config);
				});
			} else {
				return jQuery();
			}
		}
	});

	jQuery.fn.lightbox = function(config, param){
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
			} else if (typeof Lightbox.prototype[config] === 'function') {
				return this.each(function(){
					var curData = jQuery(this).data(dataName);
					if (curData) {
						curData[config](param);
					}
				});
			} else if (Lightbox.prototype[config]) {
				return this.eq(0).data(dataName)[config];
			} else {
				return this;
			}
		} else {
			return this.each(function(){
				new Lightbox(this, config);
			});
		}
	};

	var dataName = 'Lightbox',
		arrNames = [];

}(jQuery));