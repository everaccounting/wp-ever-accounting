(function ($) {
	'use strict';
	/**
	 * EAccounting Form plugin
	 *
	 * @param {object} options
	 */
	$.fn.ea_form = function (options) {
		return this.each(function () {
			(new $.ea_form(this, options));
		});
	};

	// here we go!
	$.ea_form = function (element, options) {
		var form = this,
			$element = $(element);
		form.defaults = {};
		form.data = {};
		form.method = $element.attr('method').toLowerCase() || 'post';

		form.init = function () {
			form.collect_data();
		}

		form.collect_data = function () {
			$element.find("input").each(function (index, field) {
				var $field = $(field);
				var name = $field.attr('name');
				var type = $field.attr('type');

				switch (type) {
					case 'radio':
						if (!form.data[name]) {
							form.data[name] = ($field.attr('value') ? 1 : 0) || 0;
						}
						break;
					case 'checkbox':
						if (!form.data[name]) {
							form.data[name] = [];
						}

						if ($field.is(':checked')) {
							form.data[name].push($field.attr('value'));
						}
						break;

					default:
						form.data[name] = $field.attr('value').trim();
				}

				$field.on('change', form.collect_data);
			});

			console.log(form);
		}


		var errors = function () {
			this.errors = {};

			this.has = function (field) {
				// if this.errors contains as "field" property.
				return handler.errors.hasOwnProperty(field);
			}

			this.any = function () {
				return Object.keys(this.errors).length > 0;
			}

			this.set = function (key, field) {
				return this.errors[key] = field;
			}

			this.get = function (field) {
				if (this.errors[field]) {
					return this.errors[field][0];
				}
			}

			this.record = function (errors) {
				this.errors = errors;
			}

			this.clear = function (field) {
				if (field) {
					return delete this.errors[field];
				}

				this.errors = {};
			}
		}

		form.init();
	};


}(jQuery));
