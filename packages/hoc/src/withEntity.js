/**
 * WordPress dependencies
 */
import {Component} from '@wordpress/element';
import {createHigherOrderComponent, compose} from '@wordpress/compose';
import {withSelect, withDispatch} from '@wordpress/data';
import {get} from "lodash";
import withSettings from "./withSettings";
import apiFetch from "@wordpress/api-fetch";

const withEntity = (resourceName) => {
	return createHigherOrderComponent(WrappedComponent => {
		class Hoc extends Component {
			constructor(props) {
				super(props);
				this.handleSubmit = this.handleSubmit.bind(this);
				this.handleDelete = this.handleDelete.bind(this);
			}
			/**
			 * This is a helper function for updating and creating entities
			 * callback after() can be used do after ward processing
			 *
			 * @param {Object} data
			 * @param {Function} after
			 * @param {Boolean} autoUpdateStore
			 * @param {string} resource
			 * @returns {Promise<void>}
			 */
			async handleSubmit(data, after = (res) => {}, autoUpdateStore = true, resource = resourceName) {
				await apiFetch({path: `ea/v1/${resource}`, method: 'POST', data}).then(res => {
					after(res);
					data && data.id && autoUpdateStore && this.props.replaceEntity(resource, res);
					data && !data.id && autoUpdateStore && this.props.resetForSelectorAndResource('getCollection', resource);
				}).catch(error => {
					alert(error.message);
				});
			}

			/**
			 * This handles delete of the table items
			 * callback after() can be used do after ward processing
			 * @param id
			 * @param after
			 * @param autoUpdateStore
			 * @returns {Promise<void>}
			 */
			async handleDelete(id, after = (res) => {}, autoUpdateStore = true) {
				if(true === confirm(__('Are you sure you want to delete this item?'))){
					await apiFetch({path: `ea/v1/${resourceName}/${id}`, method: 'DELETE'}).then(res => {
						after(res);
						autoUpdateStore && this.props.resetForSelectorAndResource('getCollection', resourceName);
					}).catch(error => {
						alert(error.message);
					});
				}
			}

			render() {
				return <WrappedComponent
					{...this.props}
					handleSubmit={this.handleSubmit}
					handleDelete={this.handleDelete}/>
			};
		}

		return compose(
			withSettings(),
			withSelect((select, ownProps) => {
				const id = get(ownProps, ['match', 'params', 'id'], null);
				const {getEntityById, isRequestingGetEntityById} = select('ea/collection');

				return {
					item: id ? getEntityById(resourceName, id, null) : {},
					isLoading: id ? isRequestingGetEntityById(resourceName, id, null) : false,
					isNew: !id
				}
			}),
			withDispatch((dispatch => {
				const {replaceEntity, deleteEntityById, resetForSelectorAndResource} = dispatch('ea/collection');
				return {
					replaceEntity,
					deleteEntityById,
					resetForSelectorAndResource,
				}
			}))
		)(Hoc);
	}, 'withEntity');
};

export default withEntity;
