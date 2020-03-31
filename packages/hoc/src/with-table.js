/**
 * WordPress dependencies
 */
import {Component} from '@wordpress/element';
import {createHigherOrderComponent} from '@wordpress/compose';
import {__} from "@wordpress/i18n";

/**
 * External dependencies
 */
import {xor} from 'lodash';
import {withDispatch, withSelect} from '@wordpress/data';
import {compose} from '@wordpress/compose';
import isShallowEqual from '@wordpress/is-shallow-equal';


function withTable(resourceName, defaultQuery = {orderby: 'id'}) {
	return createHigherOrderComponent(WrappedComponent => {
		class Wrapper extends Component {
			static _Mounted = false;

			constructor() {
				super(...arguments);
				this.state = {
					selected: [],
					total: 0,
				};
			}

			componentDidMount() {
				this._Mounted = true;
			}

			componentDidUpdate(prevProps) {
				if (!isNaN(this.props.total) && !isShallowEqual(this.state.total, this.props.total)) {
					this._Mounted && this.setState({
						total: this.props.total,
					});
				}
			}

			componentWillUnmount() {
				this._Mounted = false;
			}

			onSelected = id => {
				this.setState({
					selected: xor(this.state.selected, [id]),
				});
			};

			onAllSelected = onoff => {
				this.setState({
					selected: onoff ? this.props.items.map(item => item.id) : [],
				});
			};

			setOrderByOrder = (orderby, order) => {
				this.props.setOrderBy(resourceName, orderby);
				this.props.setOrder(resourceName, order);
			};

			setDelete = (id) => {
				this.props.deleteEntityById(resourceName, id);
			};

			render() {
				return (
					<WrappedComponent
						{...this.props}
						selected={this.state.selected}
						total={this.state.total}
						onSearch={(search) => this.props.setSearch(resourceName, search)}
						onPageChange={(page) => this.props.setPage(resourceName, page)}
						onOrderBy={this.setOrderByOrder}
						onSelected={this.onSelected}
						onAllSelected={this.onAllSelected}
						onFilter={filter => this.props.setFilter(resourceName, filter)}
						onDelete={this.setDelete}
					/>
				)
			}
		}

		return compose([
			withSelect((select) => {
				const {getEntities, isRequestingEntities,} = select('ea/collection');
				const {getPage, getSearch, getOrder, getOrderBy, getQuery} = select('ea/query');
				const query = getQuery(resourceName, defaultQuery);
				const {items, total} = getEntities(resourceName, query);
				return {
					items: items,
					total: total,
					status: isRequestingEntities(resourceName, query) === true ? "STATUS_IN_PROGRESS" : "STATUS_COMPLETE",
					page: getPage(resourceName),
					per_page: 20,
					search: getSearch(resourceName),
					order: getOrder(resourceName),
					orderby: getOrderBy(resourceName),

				}
			}),
			withDispatch((dispatch) => {
				const {setPage, setSearch, setOrder, setOrderBy, setFilter, setQuery} = dispatch('ea/query');
				const {deleteEntityById} = dispatch('ea/collection');
				return {
					setPage,
					setSearch,
					setOrder,
					setOrderBy,
					setQuery,
					setFilter,
					deleteEntityById
				}
			}),
		])(Wrapper)
	}, 'withTable');
}

export default withTable;
