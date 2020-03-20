/**
 * WordPress dependencies
 */
import {Component} from '@wordpress/element';
import {createHigherOrderComponent} from '@wordpress/compose';
import {__} from "@wordpress/i18n";

/**
 * External dependencies
 */
import {SCHEMA_STORE_KEY} from '@eaccounting/data';
import {withSelect} from '@wordpress/data';

/**
 * Internal dependencies
 */

function withModel(resourceName) {
	return createHigherOrderComponent(WrappedComponent => {
		class Wrapper extends Component {
			constructor() {
				super(...arguments);
			}

			render() {
				return (
					<WrappedComponent
						{...this.props}
					/>
				);
			}
		}

		return withSelect((select) => {
			console.log(this);
			const {getSchema, hasFinishedResolution} = select(SCHEMA_STORE_KEY);
			const schema = getSchema(resourceName);
			const model = {};
			const isLoading = hasFinishedResolution('getSchema', [resourceName]);
			if (!isLoading && schema.properties) {
				const properties = schema.properties;
				for (let key in properties) {
					console.log(key);
					if (properties.hasOwnProperty(key)) {
						model.key = properties[key] && properties[key]['default'] && properties[key]['default'] || '';
					}
				}
				console.log(model);
			}

			return {
				isLoading,
				model
			}

		})(Wrapper);
	}, 'withModel');
}

export default withModel;
