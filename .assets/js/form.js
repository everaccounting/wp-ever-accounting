(function ($) {
	/**
	 * Initialize the form.
	 *
	 * @param {Object} options - Configuration options for the form.
	 * @returns {jQuery|*}
	 */
	$.fn.eac_form = function (options) {
		options = options || {};
		if (typeof options === 'object' || !options) {
			return this.each(function () {
				const instance = new $.eac_form($(this), options);
				$(this).data('eac_form', instance); // Store the instance in data
			});
		}

		var ret = this;
		var args = Array.prototype.slice.call(arguments, 1);
		this.each(function () {
			var instance = $(this).data('eac_form');
			if (instance && typeof instance?.view[options] === 'function') {
				ret = instance.view[options].apply(instance.view, args);
			}
		});

		return ret;
	}

	/**
	 * Form constructor.
	 *
	 * @param {jQuery} $el - The jQuery element representing the form.
	 * @param {Object} options - Configuration options for the form.
	 * @returns {$.eac_form}
	 * @constructor
	 */
	$.eac_form = function ($el, options) {
		this.$el = $el;
		this.options = $.extend({}, $.eac_form.defaults, options || {});
		this.events = this.options.events || {};
		this.$el.data('eac_form', this);


		/**
		 * Get the form element.
		 *
		 * @param selector
		 * @returns {*|jQuery}
		 */
		this.$ = function (selector) {
			return this.$el.find(selector);
		}

		/**
		 * Bind an event to the form.
		 *
		 * @param {string} event The event to bind.
		 * @param {string} selector The selector to bind the event to.
		 * @param {function} callback The callback to execute.
		 */
		this.on = function (event, selector, callback) {
			this.$el.on(event, selector, callback);
		}

		/**
		 * Get the current value of the associated form element.
		 *
		 * @param {string|null} name The name of the form element.
		 * @return {mixed} The value of the form element.
		 */
		this.data = function (name) {
			name = name || null;
			const data = {};
			this.$('input, select, textarea').each((_, element) => {
				const $element = $(element);
				const name = $element.attr('name');
				const type = $element.attr('type');

				if (name === 'method') return;

				if (type === 'radio') {
					data[name] = $element.is(':checked') ? $element.val() || 0 : data[name];
				} else if (type === 'checkbox') {
					data[name] = data[name] || [];
					if ($element.is(':checked')) {
						data[name].push($element.val());
					}
				} else {
					data[name] = $element.val() || '';
				}
			});
			return name ? data[name] : data;
		}


		/**
		 * Initialize the form.
		 *
		 * @returns {$.eac_form}
		 * @constructor
		 */
		this.init = function () {
			for (var key in this.events) {
				var method = this.events[key];
				if (typeof method !== 'function') method = this[method];
				if (!method) continue;
				var match = key.match(/^(\S+)\s*(.*)$/);
				this.on(match[1], match[2], method.bind(this));
			}
		}


		this.init();
		return this;
	};

})(jQuery);
