/**
 * WordPress dependencies
 */
import {Component, Fragment, createRef} from '@wordpress/element';
import {createHigherOrderComponent, compose} from '@wordpress/compose';
import {withDispatch, withSelect} from '@wordpress/data';
import {__} from "@wordpress/i18n";
import {addQueryArgs} from "@wordpress/url"
import isShallowEqual from '@wordpress/is-shallow-equal';
import {xor} from 'lodash';
import qs from "querystring";
import apiFetch from "@wordpress/api-fetch";
import {SearchBox, TableNav, Table, Button} from "@eaccounting/components"

const withListTable = (table = {}) => {
	return createHigherOrderComponent(WrappedComponent => {
		class Hoc extends Component {
			constructor(props) {
				super(props);
				this.state = {
					selected: [],
					total: 0,
				};
				this.child = createRef();
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
			async handleSubmit(data, after = (res) => {
			}, autoUpdateStore = true) {
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
			async handleDelete(id, after = (res) => {
			}, autoUpdateStore = true) {
				if (true === confirm(__('Are you sure you want to delete this item?'))) {
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
				const {items, status, resourceName} = this.props;
				const {page = 1, per_page = 50, orderby = '', order = ''} = this.props.query;
				return (
					<Fragment>
						<WrappedComponent
							{...this.props}
							selected={this.state.selected}
							total={this.state.total}
							page={page}
							per_page={per_page}
							orderby={orderby}
							order={order}
							ref={this.child}
							handleSubmit={this.handleSubmit}
							handleDelete={this.handleDelete}
							setSelected={this.setSelected}
							setAllSelected={this.setAllSelected}
							setPage={(page, removeSelected = true) => {
								this.props.setQuery(resourceName, 'page', page);
								removeSelected && this.setState({selected: []});
							}}
							setOrderBy={(orderby, order) => {
								this.props.setContextQuery(resourceName, {...this.props.query, orderby, order})
							}}
							setSearch={(search) => {
								this.props.setContextQuery(resourceName, {...this.props.query, search, page: 1})
							}}
						/>
					</Fragment>
				);
			}

		}

		return compose(
			withSelect((select, ownProp) => {
				const page = ownProp.location.pathname.split("/").pop() || '';
				const {resourceName = page, queryFilter = (x) => x} = table;
				const {getCollection, isRequestingGetCollection} = select('ea/collection');
				const {getQuery} = select('ea/query');
				const queries = queryFilter(getQuery(resourceName));
				const {items = [], total = NaN} = getCollection(resourceName, queries);
				return {
					items: items,
					total: total,
					resourceName,
					query: getQuery(resourceName),
					status: isRequestingGetCollection(resourceName, queries) === true ? "STATUS_IN_PROGRESS" : "STATUS_COMPLETE",
				}
			}),
			withDispatch((dispatch => {
				const {setQuery, setContextQuery} = dispatch('ea/query');
				const {replaceEntity, deleteEntityById, resetForSelectorAndResource} = dispatch('ea/collection');
				return {
					setQuery,
					setContextQuery,
					replaceEntity,
					deleteEntityById,
					resetForSelectorAndResource,
				}
			}))
		)(Hoc)
	}, 'withListTable');
};


export default withListTable;
