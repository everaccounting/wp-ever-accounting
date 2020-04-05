/**
 * WordPress dependencies
 */
import {Component} from '@wordpress/element';
import {createHigherOrderComponent, compose} from '@wordpress/compose';
import {withDispatch, withSelect} from '@wordpress/data';
import {__} from "@wordpress/i18n";
import withTableNavigation from "./withTableNavigation";
import {addQueryArgs} from "@wordpress/url"
import isShallowEqual from '@wordpress/is-shallow-equal';
import {xor} from 'lodash';
import qs from "querystring";
import apiFetch from "@wordpress/api-fetch";

/**
 * A HOC for table
 * @param resourceName
 * @param initQuery
 * @returns {withTable}
 */
const withTable = (resourceName, initQuery = {}) => {
	if (!resourceName)
		throw 'No resourceName in child component';
	return createHigherOrderComponent(WrappedComponent => {
		class Hoc extends Component {
			constructor(props) {
				super(props);
				this.state = {
					selected: [],
					total: 0,
				};

				this.handleSubmit = this.handleSubmit.bind(this);
				this.handleDelete = this.handleDelete.bind(this);
				this.setSelected = this.setSelected.bind(this);
				this.setAllSelected = this.setAllSelected.bind(this);
			}

			/**
			 * Since there is a problem handling total and navigation get disappared
			 * when making request so we are lazy loading total until request is complete
			 *
			 * @param prevProps
			 */
			componentDidUpdate(prevProps) {
				if (!isNaN(this.props.total) && !isShallowEqual(this.state.total, this.props.total)) {
					this.setState({
						total: this.props.total,
					});
				}
			}


			/**
			 * This is a helper function for updating and creating entities
			 * callback after() can be used do after ward processing
			 *
			 * @param data
			 * @param after
			 * @param autoUpdateStore
			 * @returns {Promise<void>}
			 */
			async handleSubmit(data, after = (res) => {}, autoUpdateStore = true) {
				await apiFetch({path: `ea/v1/${resourceName}`, method: 'POST', data}).then(res => {
					after(res);
					data && data.id && autoUpdateStore && this.props.replaceEntity(resourceName, res);
					data && !data.id && autoUpdateStore && this.props.resetForSelectorAndResource('getCollection', resourceName);
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

			/**
			 * Set selected item from table
			 *
			 * @param id
			 */
			setSelected(id) {
				this.setState({
					selected: xor(this.state.selected, [id]),
				});
			}

			/**
			 * Set all item toggle select
			 * @param onoff
			 */
			setAllSelected(onoff) {
				this.setState({
					selected: onoff ? this.props.items.map(item => item.id) : [],
				});
			}


			render() {
				return <WrappedComponent
					{...this.props}
					selected={this.state.selected}
					total={this.state.total}
					handleSubmit={this.handleSubmit}
					handleDelete={this.handleDelete}/>;
			}

		}

		return compose(
			withTableNavigation(initQuery),
			withSelect((select, ownProp) => {
				const {getCollection, isRequestingGetCollection} = select('ea/collection');
				const {queries = {}} = ownProp;
				const {items = [], total = NaN} = getCollection(resourceName, queries);
				return {
					items: items,
					total: total,
					status: isRequestingGetCollection(resourceName, queries) === true ? "STATUS_IN_PROGRESS" : "STATUS_COMPLETE",
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
		)(Hoc)
	}, 'withTable');
};


export default withTable;
