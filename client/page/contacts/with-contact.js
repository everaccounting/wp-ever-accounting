import {Component} from '@wordpress/element';
import {createHigherOrderComponent} from '@wordpress/compose';
import {xor, pickBy, isNumber, isEmpty} from "lodash";
import {__} from "@wordpress/i18n";
import {COLLECTIONS_STORE_KEY, QUERY_STATE_STORE_KEY} from "data";
import {withDispatch, withSelect} from '@wordpress/data';
import {compose} from '@wordpress/compose';
import {removeDefaultQueries} from "./utils";
import {getNewPath, updateQueryString} from '@eaccounting/navigation';
import isShallowEqual from '@wordpress/is-shallow-equal';

function withContacts(resourceName, defaultQuery = {}) {

	return createHigherOrderComponent((WrappedComponent) => {
		class Wrapper extends Component {
			constructor() {
				super(...arguments);
				this.state = {
					selected: [],
					total: 0,
				}
			}

			componentDidUpdate(prevProps) {
				if (!isNaN(this.props.total) && this.state.total !== this.props.total) {
					this.setState({
						total: this.props.total
					});
				}
			}

			setQuery = (queryKey, queryValue) => {
				this.props.setQueryValue(resourceName, queryKey, queryValue)
			};

			onSelected = id => {
				this.setState({
					selected: xor(this.state.selected, [id])
				})
			};

			onAllSelected = onoff => {
				this.setState({
					selected: onoff ? this.props.items.map(item => item.id) : []
				})
			};

			setBulkAction = (action) => {
				this.props.remove('/ea/v1', resourceName, this.props.selected);
			};


			render() {
				const {total, selected} = this.state;
				return <WrappedComponent
					{...this.props}
					total={total}
					selected={selected}
					onSelected={this.onSelected}
					onAllSelected={this.onAllSelected}
					onBulkAction={this.setBulkAction}
					setQuery={this.setQuery}/>;
			}
		}

		return compose([
			withSelect((select) => {
				const namespace = '/ea/v1';
				const {getCollection, getCollectionHeader, getCollectionError, hasFinishedResolution} = select(COLLECTIONS_STORE_KEY);
				const {getValueForQueryContext} = select(QUERY_STATE_STORE_KEY);
				const query = removeDefaultQueries(pickBy({...defaultQuery, ...getValueForQueryContext(resourceName)}, value => isNumber(value) || !isEmpty(value)), defaultQuery.orderby);
				const args = [namespace, resourceName, query];
				let status = hasFinishedResolution('getCollection', args) !== true ? "STATUS_IN_PROGRESS" : "STATUS_COMPLETE";
				if (getCollectionError('getCollection', namespace, resourceName, query)) {
					status = "STATUS_FAILED"
				}
				const {page = 1} = query;
				return {
					items: getCollection(namespace, resourceName, query),
					total: parseInt(getCollectionHeader('x-wp-total', namespace, resourceName, query), 10),
					status: status,
					page: page,
					query: query
				}
			}),

			withDispatch((dispatch) => {
				const {create, update, remove} = dispatch(COLLECTIONS_STORE_KEY);
				const {setQueryValue} = dispatch(QUERY_STATE_STORE_KEY);
				return {
					setQueryValue,
					create,
					update,
					remove
				};
			})
		])(Wrapper);
	}, 'withContacts');
}

export default withContacts;
