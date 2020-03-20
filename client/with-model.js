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
import {each} from "lodash"
import {createEntityFactory} from "model";
const extractModel = (schema) => {
	if (schema && schema.properties) {
		let model = {};

		each(schema.properties, (prop, key) => {
			model[key] = prop.default
		});

		return model;
	}
	return {};
};

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
				const {schema} = this.props;
				return (
					<WrappedComponent
						{...this.props}
						model={extractModel(schema)}
					/>
				);
			}
		}

		return withSelect((select) => {
			const {getSchema, hasFinishedResolution} = select(SCHEMA_STORE_KEY);
			const isLoading = hasFinishedResolution('getSchema', [resourceName]) !== true;
			const schema = getSchema(resourceName);
			if(!isLoading){
				const model = createEntityFactory('payment', schema);
				console.log(model.createNew({}));
			}

			return {
				isLoading,
				schema
			}

		})(Wrapper);
	}, 'withModel');
}

export default withModel;
