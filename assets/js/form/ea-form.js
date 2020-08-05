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
	$.ea_form = function (form, options) {
		var plugin = this;
		var $form = $(form);
		var defaults = {};
		plugin.settings = {};
		plugin.errors = {};
		plugin.method = $form.attr('method').toLowerCase() || 'post';

		plugin.init = function () {
			plugin.settings = $.extend({}, defaults, options);

			for (let form_element of $form.find("input")) {

				if ($(form_element).attr('id') === 'global-search') {
					continue;
				}

				var name = $(form_element).attr('name');
				var type = $(form_element).attr('type');

				if (name === 'method') {
					continue;
				}

				if ($(form_element).attr('data-item')) {
					if (!plugin['items']) {
						var item = {};
						var row = {};

						item[0] = row;
						plugin['items'] = item;
					}

					if (!plugin['items'][0][$(form_element).attr('data-item')]) {
						plugin['items'][0][$(form_element).attr('data-item')] = '';
					}

					plugin['item_backup'] = plugin['items'];

					continue;
				}

				if ($(form_element).attr('data-field')) {
					if (!plugin[$(form_element).attr('data-field')]) {
						var field = {};

						plugin[$(form_element).attr('data-field')] = field;
					}

					if (!plugin[$(form_element).attr('data-field')][name]) {
						plugin[$(form_element).attr('data-field')][name] = '';
					}

					continue;
				}

				if (type === 'radio') {
					if (!plugin[name]) {
						plugin[name] = ($(form_element).attr('value') ? 1 : 0) || 0;
					}
				} else if (type === 'checkbox') {
					if (plugin[name]) {
						if (!plugin[name].push) {
							plugin[name] = [plugin[name]];
						}

						if ($(form_element).checked) {
							plugin[name].push(form_element.value);
						}
					} else {
						if ($(form_element).checked) {
							plugin[name] = $(form_element).value;
						} else {
							plugin[name] = [];
						}
					}
				} else {
					plugin[name] = $(form_element).attr('value') || '';
				}
			}


			for (let form_element of $form.find("textarea")) {
				var name = $(form_element).attr('name');

				if (name === 'method') {
					continue;
				}

				if ($(form_element).attr('data-item')) {
					if (!this['items']) {
						var item = {};
						var row = {};

						item[0] = row;
						this['items'] = item;
					}

					if (!this['items'][0][$(form_element).attr('data-item')]) {
						this['items'][0][$(form_element).attr('data-item')] = '';
					}

					this['item_backup'] = this['items'];

					continue;
				}

				if ($(form_element).attr('data-field')) {
					if (!this[$(form_element).attr('data-field')]) {
						var field = {};

						this[$(form_element).attr('data-field')] = field;
					}

					if (!this[$(form_element).attr('data-field')][name]) {
						this[$(form_element).attr('data-field')][name] = '';
					}

					continue;
				}

				if (this[name]) {
					if (!this[name].push) {
						this[name] = [this[name]];
					}

					this[name].push($(form_element).value || '');
				} else {
					this[name] = $(form_element).value || '';
				}
			}

			for (let form_element of $form.find("select")) {
				var name = $(form_element).attr('name');

				if (name === 'method') {
					continue;
				}

				if ($(form_element).attr('data-item')) {
					if (!this['items']) {
						var item = {};
						var row = {};

						item[0] = row;
						this['items'] = item;
					}

					if (!this['items'][0][$(form_element).attr('data-item')]) {
						this['items'][0][$(form_element).attr('data-item')] = '';
					}

					this['item_backup'] = this['items'];

					continue;
				}

				if ($(form_element).attr('data-field')) {
					if (!this[$(form_element).attr('data-field')]) {
						var field = {};

						this[$(form_element).attr('data-field')] = field;
					}

					if (!this[$(form_element).attr('data-field')][name]) {
						this[$(form_element).attr('data-field')][name] = '';
					}

					continue;
				}

				if (this[name]) {
					if (!this[name].push) {
						this[name] = [this[name]];
					}

					this[name].push($(form_element).attr('value') || '');
				} else {
					this[name] = $(form_element).attr('value') || '';
				}
			}

			plugin.loading = false;
			plugin.response = {};

			$form.bind('submit', plugin.submit)
		}

		plugin.data = function() {
			let data = Object.assign({}, plugin);
			delete data.method;
			delete data.action;
			delete data.errors;
			delete data.loading;
			delete data.response;

			return data;
		}

		plugin.submit = function (e) {
			e.preventDefault();

			// FormData.prototype.appendRecursive = function (data, wrapper = null) {
			// 	for (var name in data) {
			// 		if (wrapper) {
			// 			if ((typeof data[name] == 'object' || data[name].constructor === Array) && ((data[name] instanceof File != true) && (data[name] instanceof Blob != true))) {
			// 				this.appendRecursive(data[name], wrapper + '[' + name + ']');
			// 			} else {
			// 				this.append(wrapper + '[' + name + ']', data[name]);
			// 			}
			// 		} else {
			// 			if ((typeof data[name] == 'object' || data[name].constructor === Array) && ((data[name] instanceof File != true) && (data[name] instanceof Blob != true))) {
			// 				this.appendRecursive(data[name], name);
			// 			} else {
			// 				this.append(name, data[name]);
			// 			}
			// 		}
			// 	}
			// };

			this.loading = true;

			let data = plugin.data();

			let form_data = new FormData();
			console.log(data);
			// form_data.appendRecursive(data);
			// console.log(form_data);
			// window.axios({
			// 	method: this.method,
			// 	url: this.action,
			// 	data: form_data,
			// 	headers: {
			// 		'X-CSRF-TOKEN': window.Laravel.csrfToken,
			// 		'X-Requested-With': 'XMLHttpRequest',
			// 		'Content-Type': 'multipart/form-data'
			// 	}
			// })
			// 	.then(this.onSuccess.bind(this))
			// 	.catch(this.onFail.bind(this));
		}

		plugin.onSuccess = function (response) {
			plugin.errors.clearError();

			plugin.loading = false;

			if (response.data.redirect) {
				plugin.loading = true;

				window.location.href = response.data.redirect;
			}

			plugin.response = response.data;
		}

		// Form fields check validation issue
		plugin.onFail = function (error) {
			plugin.errors.record(error.response.data.errors);

			plugin.loading = false;
		}

		plugin.hasError = function(field) {
			// if this.errors contains as "field" property.
			return plugin.errors.hasOwnProperty(field);
		}

		plugin.anyError = function() {
			return Object.keys(plugin.errors).length > 0;
		}

		plugin.setError = function(key, field) {
			return plugin.errors[key] = field;
		}

		plugin.getError = function(field) {
			if (plugin.errors[field]) {
				return plugin.errors[field][0];
			}
		}

		plugin.recordError = function(errors) {
			this.errors = errors;
		}

		plugin.clearError = function(field) {
			if (field) {
				return delete plugin.errors[field];
			}

			this.errors = {};
		}

		// fire up the plugin!
		// call the "constructor" method
		plugin.init();
	}

}(jQuery));
