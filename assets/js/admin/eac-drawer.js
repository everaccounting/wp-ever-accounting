jQuery(function ($) {
	'use strict';
	const i18n = window.eac_drawer_i18n || {
		'close': 'Close',
		'loading': 'Loading...',
		'error': 'There was an error loading the drawer.',
		'submit': 'Submit',
	};

	const defaults = {
		appendTo: 'body',
		focus: true,
		autoResize: true,
		zIndex: 1000,
		escClose: true,
		overlayClose: true,
		overlay: true,
		onOpen: null,
		onReady: null,
		onClose: null,
		title: false,
		target: false,
		i18n: i18n,
	}
	$.eac_drawer = function (options) {
		const plugin = this;
		this.options = $.extend({}, defaults, options);

		/**
		 * Open the drawer.
		 *
		 * @return void
		 */
		this.open = function () {
			if (this.$drawer !== undefined) {
				return;
			}
			// Create the drawer.
			this.$appendto = $(this.options.appendTo);
			this.$backdrop = $('<div class="eac-drawer__backdrop"></div>');
			this.$container = $('<div class="eac-drawer__container"></div>');
			this.$inner = $('<div class="eac-drawer__inner"></div>');
			this.$wrapper = $('<div class="eac-drawer__wrapper"></div>');
			this.$body = $('<div class="eac-drawer__body"></div>');
			this.$loading = $('<div class="eac-drawer__loading">&nbsp;</div>');
			this.$close = $('<button class="eac-drawer__close">&nbsp;</button>');
			this.$title = $('<h3 class="eac-drawer__title"></h3>');
			this.$header = $('<div class="eac-drawer__header"></div>');
			this.$drawer = $('<div class="eac-drawer"></div>');
			this.$root = $('<div>');

			// Make the drawer.
			this.$wrapper.append(this.$header);
			this.$header.append(this.$title);
			this.$header.prepend(this.$close);
			this.$drawer.append(this.$backdrop);
			this.$drawer.append(this.$container);
			this.$container.append(this.$inner);
			this.$inner.append(this.$wrapper);
			this.$wrapper.append(this.$body);
			this.$body.append(this.$loading);

			// If overlay is false, hide the backdrop.
			if (!this.options.overlay) {
				this.$backdrop.remove();
			}

			// If we have a title, then add it.
			if (this.options.title) {
				this.$title.text(this.options.title);
			}

			// Add the drawer to the body.
			this.$root.append(this.$drawer);
			this.$appendto.append(this.$root);
			this.$drawer.addClass('is--open');
			this.$drawer.data('eac_drawer', this);
			// eac-drawer-open class to the body.
			if( ! $(document.body).hasClass('eac-drawer-open') ) {
				$(document.body).addClass('eac-drawer-open');
			}

			// Bind the events.
			this.bindEvents();

			$(document).trigger('eac_drawer_open', [this.$drawer, this]);
			// If onOpen is a function, then call it.
			if (typeof this.options.onOpen === 'function') {
				this.options.onOpen();
			}


			// If no target is set, return.
			if (!this.options.target) {
				console.error('No target was set for the drawer.');
				return;
			}

			const callback = function (response) {
				plugin.focus();
				$(document).trigger('eac-drawer-ready', [plugin.$drawer, plugin]);
				// If this is a form, then we will remove the submit button and add it to the drawer header.
				if (plugin.$body.find('form').length > 0) {
					const $form = plugin.$body.find('form');
					const text = $form.find('input[type="submit"]').val() || plugin.options.i18n.submit;
					// If the form does not have id, then add one.
					if (!$form.attr('id')) {
						$form.attr('id', 'eac-drawer-form-' + Math.floor(Math.random() * 100000));
					}
					// Now add the button to the header using the id of the form.
					const formId = $form.attr('id');
					plugin.$header.append('<button type="submit" form="' + formId + '" class="eac-drawer__submit button button-primary">' + text + '</button>');
				}

				if (typeof plugin.options.onReady === 'function') {
					plugin.options.onReady(plugin.$drawer, plugin);
				}
			}

			this.load(this.options.target, callback);
		}

		this.load = function (url, callback) {
			if (!url) {
				return;
			}
			if (url.indexOf('#') === 0) {
				// If the id is in the current page not found then return.
				if ($(url).length === 0) {
					console.error('The target id was not found in the current page.')
					return;
				}
				const content = $(url).html();
				this.$body.html(content);
				this.$loading.remove();
				if (typeof callback === 'function') {
					return callback(content);
				}
				return;
			}

			// try catch
			try {
				this.$body.load(url, function (response, status, xhr) {
					plugin.$loading.remove();
					if (status !== 'success') {
						plugin.$body.html(xhr.statusText);
					}
					if (typeof callback === 'function') {
						return callback(response);
					}
				});
			} catch (e) {
				console.error(e);
			}

		}

		/**
		 * Close the drawer.
		 *
		 * @return void
		 */
		this.bindEvents = function () {
			if (this.options.escClose) {
				$(document).on('keyup.eac_drawer', function (e) {
					if (e.keyCode === 27) {
						plugin.close();
					}
				});
			}

			// If overlayClose is true, then close the drawer when the backdrop is clicked.
			if (this.options.overlayClose && this.options.overlay) {
				this.$backdrop.on('click.eac_drawer', function (e) {
					plugin.close();
				});
			}

			// Close the drawer when the close button is clicked.
			this.$close.on('click.eac_drawer', function (e) {
				e.preventDefault();
				plugin.close();
			});

			// Watch resize events.
			if (this.options.autoResize) {
				$(window).on('resize.eac_drawer orientationchange.eac_drawer', function (e) {
					plugin.resize();
				});
			}
		}

		/**
		 * Unbind the drawer events.
		 *
		 * @return void
		 */
		this.unbindEvents = function () {
			$(document).off('keyup.eac_drawer');
			this.$backdrop.off('click.eac_drawer');
			this.$close.off('click.eac_drawer');
			$(window).off('resize.eac_drawer orientationchange.eac_drawer');
		}

		/**
		 * Close the drawer.
		 *
		 * @return void
		 */
		this.focus = function () {
			var $drawers = $('.eac-drawer.is--open');
			for (let i = $drawers.length - 1; i >= 0; i--) {
				const $drawer = $($drawers[i]);
				const zIndex = 100 + i;
				const translate = i === $drawers.length - 1 ? 0 : -100 * ($drawers.length - i - 1);
				$drawer.css('z-index', zIndex);
				$drawer.css('transform', 'translateX(' + translate + 'px)');
			}


			// Find the last drawer and focus it if exists.
			if ($drawers.length > 0) {
				const $drawer = $($drawers[$drawers.length - 1]);
				$drawer.focus();
				// for the visible drawer, focus the first input.
				$drawer.find('input').first().focus();
			}

		}

		/**
		 * Resize the drawer.
		 *
		 * @return void
		 */
		this.resize = function () {
			console.log('resize');
		}

		/**
		 * Update position of the drawer.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		this.updatePosition = function () {}

		/**
		 * Close the drawer.
		 *
		 * @return void
		 */
		this.close = function () {
			this.unbindEvents();
			plugin.$drawer.remove();
			this.$root.remove();
			this.focus();

			// If onClose is a function, then call it.
			if (typeof this.options.onClose === 'function') {
				this.options.onClose();
			}
			$(document).trigger('eac_drawer_close', [plugin]);

			// If there is no drawer left, then we have to free the body.
			if ($('.eac-drawer').length === 0) {
				// remove the class from the body.
				$('body').removeClass('eac-drawer-open');
			}
		}

		this.open();

		return plugin;
	};


	/**
	 * Create a drawer.
	 *
	 * @param options
	 * @returns {*}
	 */
	$.fn.eac_drawer = function (options) {
		return this.each(function () {
			new $.eac_drawer(options);
		});
	}


	$(document).on('click', '[rel~="eac_drawer"], a[href*="eac_get_html_response"]', function (e) {
		e.preventDefault();
		const $this = $(this);
		const target = $this.attr('href');
		const text = $this.text();
		const title = $this.attr('title') || text;
		const data = $this.data();
		const options = $.extend({}, data, {target: target, title: title});
		$(this).eac_drawer(options);
	});
});

