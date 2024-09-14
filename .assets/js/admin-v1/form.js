(function($) {
	$.fn.eacform = function() {
		return this.each(function() {
			const $form = $(this);
			const formHandler = new Form($form);

			// Attach formHandler methods to the jQuery object if needed.
			$form.data('formHandler', formHandler);
		});
	};

	class Form {
		constructor($form, options) {
			this.$form = $form;
			this.method = $form.attr('method').toLowerCase();
			this.action = $form.attr('action');
			// this.errors = new Errors();
			this.loading = false;
			this.response = {};
			this.items = null; // Initialize items as needed
			this.item_backup = null; // Initialize item backup as needed

			// Process inputs, textarea, and selects
			this.init();
		}

		init() {
			this.$form.find('input, textarea, select').each((_, element) => {
				const $element = $(element);
				const name = $element.attr('name');
				const type = $element.attr('type');
				const dataField = $element.data('field');
				const dataItem = $element.data('item');

				if (name === 'method') return;

				if (dataItem) {
					this.items = this.items || {};
					this.items[0] = this.items[0] || {};
					this.items[0][dataItem] = this.items[0][dataItem] || '';
					this.item_backup = this.items;
					return;
				}

				if (dataField) {
					this[dataField] = this[dataField] || {};
					this[dataField][name] = this[dataField][name] || '';
				}

				if (type === 'radio') {
					this[name] = $element.is(':checked') ? $element.val() || 0 : this[name];
				} else if (type === 'checkbox') {
					this[name] = this[name] || [];
					if ($element.is(':checked')) {
						this[name].push($element.val());
					}
				} else {
					this[name] = $element.val() || '';
				}
			});
		}

		data() {
			// const { method, action, errors, loading, response, ...data } = this;
			const { method, action, loading, response, ...data } = this;
			return data;
		}

		reset() {
			this.$form.find('input, textarea, select').each((_, element) => {
				const name = $(element).attr('name');
				if (this[name]) {
					this[name] = '';
				}
			});
		}

		submit() {
			FormData.prototype.appendRecursive = function(data, wrapper = null) {
				for (const name in data) {
					if (typeof data[name] === 'object' && !(data[name] instanceof File || data[name] instanceof Blob)) {
						this.appendRecursive(data[name], name);
					} else {
						this.append(name, data[name]);
					}
				}
			};

			this.loading = true;
			const data = this.data();
			const form_data = new FormData();
			form_data.appendRecursive(data);

			try {
				$.ajax({
					url: this.action,
					type: this.method,
					data: form_data,
					processData: false,
					contentType: false,
					success: this.onSuccess.bind(this),
					error: this.onFail.bind(this),
				});
			} catch (error) {
				this.onFail(error);
			}
		}

		onSuccess(response) {
			// this.errors.clear();
			this.loading = false;

			if (response.data.redirect) {
				window.location.hash = '';
				window.location.href = response.data.redirect;
				if (window.location.hash) location.reload();
			}

			this.response = response.data;
		}

		onFail(error) {
			if (error.request && error.request.status === 419) {
				window.location.href = '';
				return;
			}

			// if (this.errors) {
			// 	this.errors.record(error.response.data.errors);
			// }

			this.loading = false;
		}
	}
})(jQuery);
