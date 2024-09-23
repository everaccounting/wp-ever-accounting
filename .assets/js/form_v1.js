(function ($) {
    $.fn.eac_form = function (form) {
        this.form = form;
        this.$form = $(form);
        // Bail if the form element does not exist.
        if (!this.$form.length) {
            return this;
        }

        this.method = this.$form.attr('method');
        this.action = this.$form.attr('action');

		/**
		 * Get the form element.
		 *
		 * @param selector
		 * @returns {*|jQuery}
		 */
		this.$ = function (selector) {
			return this.$form.find(selector);
		}

		/**
		 * Submit the form.
		 * @param event
		 * @param selector
		 * @param callback
		 */
		this.on = function (event, selector, callback) {
			console.log('on', event, selector, callback);
			this.$form.on(event, selector, callback);
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
        };

        /**
         * Blocks the UI by applying a transparent overlay to the view's element.
         *
         * This can be used to prevent user interaction while processing.
         *
         * @return void
         */
        this.blockUI = function () {
            this.$el.block({
                message: null,
                overlayCSS: {
                    backgroundColor: 'transparent',
                },
            });
        };

        /**
         * Unblocks the UI by removing the overlay that was applied.
         *
         * This allows user interaction to resume.
         *
         * @return void
         */
        this.unblockUI = function () {
            this.$el.unblock();
        }

        /**
         * Reset the form.
         *
         * @return void
         */
        this.reset = function () {
        }

        return this;
    };

})(jQuery);
