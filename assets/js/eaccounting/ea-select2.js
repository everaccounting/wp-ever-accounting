/**
 * Select2 wrapper for EverAccounting
 *
 * The plugin is created to handle ajax search
 * & create item on the fly
 *
 * @since 1.0.2
 */

jQuery(function ($) {
	$.fn.eaccounting_select2 = function (options) {
		return this.each(function () {
			new $.eaccounting_select2(this, options);
		});
	};
	$.eaccounting_select2 = function (el, options) {
		this.el = el;
		this.$el = $(el);
		this.placeholder = this.$el.attr('placeholder');
		this.template = this.$el.attr('data-template');
		this.nonce = this.$el.attr('data-nonce');
		this.type = this.$el.attr('data-type');
		this.creatable_text = this.$el.attr('data-text');
		this.creatable = this.$el.is('[data-creatable]');
		this.creatable = this.creatable === true;
		this.ajax = this.$el.is('[data-ajax]');
		this.ajax = this.ajax === true;
		this.id = this.$el.attr('id');

		if (this.ajax && (!this.type || !this.nonce)) {
			console.warn('ajax type defined without nonce and data type');
			this.ajax = false;
		}

		if (this.creatable && !this.template) {
			console.warn('modal type defined without template');
			this.creatable = false;
		}
		var self = this;
		var data = {};
		data.placeholder = this.placeholder;
		data.allowClear = false;
		if (this.ajax) {
			data.ajax = {
				cache: true,
				delay: 500,
				url: eaccounting_select_i10n.ajaxurl,
				method: 'POST',
				dataType: 'json',
				data: function (params) {
					return {
						action: 'eaccounting_dropdown_search',
						nonce: self.nonce,
						type: self.type,
						search: params.term,
						page: params.page,
					};
				},
				processResults: function (data, params) {
					params.page = params.page || 1;
					return {
						results: data.results,
						pagination: {
							more: data.pagination.more,
						},
					};
				},
			};
		}

		var settings = $.extend({}, data, options);

		this.$el.select2(settings);
		if (this.creatable && self.template) {
			this.$el.on('select2:open', function (e) {
				var $results = $('#select2-' + self.id + '-results').closest(
					'.select2-results'
				);
				if (!$results.children('.ea-select2-footer').length) {
					var $footer = $(
						'<a href="#" class="ea-select2-footer"><span class="dashicons dashicons-plus"></span>' +
						self.creatable_text +
						'</a>'
					).on('click', function (e) {
						e.preventDefault();
						self.$el.select2('close');
						console.log(self.template);
						$(document).trigger('ea_trigger_creatable', [
							self.$el,
							self.template,
						]);
					});
					$results.append($footer);
				}
			});
		}

		return this.$el;
	};

	$('.ea-select2').eaccounting_select2();
});
