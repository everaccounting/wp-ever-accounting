/**
 * WordPress dependencies
 */
import {Component} from '@wordpress/element';
import {__} from '@wordpress/i18n';
import {createHigherOrderComponent, compose} from '@wordpress/compose';
import {withSelect, withDispatch} from '@wordpress/data';
import {get, pickBy, isObject} from "lodash";
import apiFetch from "@wordpress/api-fetch";
import {NotificationManager} from 'react-notifications';
import withSettings from "./withSettings";
import withPreloader from "./withPreloader";

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
			 * @param {string} resetAllStore
			 * @returns {Promise<void>}
			 */
			async handleSubmit(data, after = (res) => {}, resetAllStore = false) {
				data = pickBy(data, value => !isObject(value));
				await apiFetch({path: `ea/v1/${resourceName}`, method: 'POST', data}).then(res => {
					after(res);
					!resetAllStore && data && data.id  && this.props.replaceEntity(resourceName, res);
					!resetAllStore && data && !data.id && autoUpdateStore && this.props.resetForSelectorAndResource('getCollection', resourceName);
					resetAllStore && this.props.resetAllState();
				}).catch(error => {
					NotificationManager.error(error.message);
				});
			}

			/**
			 * This handles delete of the table items
			 * callback after() can be used do after ward processing
			 * @param id
			 * @param after
			 * @param resetAllStore
			 * @returns {Promise<void>}
			 */
			async handleDelete(id, after = (res) => {}, resetAllStore = false) {
				if (true === confirm(__('Are you sure you want to delete this item?'))) {
					await apiFetch({path: `ea/v1/${resourceName}/${id}`, method: 'DELETE'}).then(res => {
						after(res);
						!resetAllStore  && this.props.resetForSelectorAndResource('getCollection', resourceName);
						resetAllStore && this.props.resetAllState();
					}).catch(error => {
						NotificationManager.error(error.message);
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
			withSelect((select, ownProps) => {
				const id = get(ownProps, ['match', 'params', 'id'], null);
				const action = get(ownProps, ['match', 'params', 'action'], 'add');
				const {getEntityById, isRequestingGetEntityById} = select('ea/collection');
				return {
					item: id ? getEntityById(resourceName, id, null) : {},
					isRequesting: id ? isRequestingGetEntityById(resourceName, id, null) : false,
					isAdd: !id,
					action: action
				}
			}),
			withDispatch((dispatch => {
				const {replaceEntity, deleteEntityById, resetForSelectorAndResource, resetAllState} = dispatch('ea/collection');
				return {
					replaceEntity,
					deleteEntityById,
					resetForSelectorAndResource,
					resetAllState
				}
			})),
			withSettings(),
			withPreloader(),
		)(Hoc);
	}, 'withEntity');
};

export default withEntity;
