import {Component} from '@wordpress/element';
import {createHigherOrderComponent} from '@wordpress/compose';
import PropTypes from 'prop-types';
import {debounce} from 'lodash';
import {withDispatch, withSelect, select} from '@wordpress/data';
import {compose} from '@wordpress/compose';
import {COLLECTIONS_STORE_KEY, QUERY_STATE_STORE_KEY} from 'store';

const withTable = createHigherOrderComponent((OriginalComponent) => {
	class WrappedComponent extends Component {
		constructor(props) {
			super(props);
		}


		onOrderBy = (orderby, order) => {
			this.props.setQuery(this.props.resourceName, orderby, order);
		};

		onPageChange = (page) => {
			this.props.setQuery(this.props.resourceName, 'page', page || 1);
		};

		onSearch = (search) => {
			this.props.setQuery(this.props.resourceName, search);
		};

		onSelected = (ids) => {

		};

		onAllSelected = (onoff) => {

		};

		onAction = (action, ids) => {

		};

		onDelete = (item) => {
			this.props.resetCollection('/ea/v1', this.props.resourceName, {}, [item.id], true )
			// this.props.setQuery(this.props.resourceName, {repalce});
		};

		render() {
			const {items, query, total, isLoading} = this.props;
			const {page} = query;
			return (
				<OriginalComponent
					setOrderBy={this.onOrderBy}
					setPageChange={this.onPageChange}
					setSearch={this.onSearch}
					setSelected={this.onSelected}
					setAllSelected={this.onAllSelected}
					setAction={this.onAction}
					setDelete={this.onDelete}
					total={total}
					items={items}
					isLoading={isLoading}
					query={query}
					page={page}
				/>
			)
		}
	}

	WrappedComponent.propTypes = {
		selected: PropTypes.array,
		query: PropTypes.object,
	};
	WrappedComponent.defaultProps = {
		selected: [],
		query: {},
	};

	return compose(
		withSelect((select, ownProps) => {
			const {resourceName, query} = ownProps;
			const currentQuery = {
				...query, ...select(QUERY_STATE_STORE_KEY).getValueForQueryContext(resourceName)
			};
			console.log(currentQuery);
			const namespace = '/ea/v1';
			const headerKey = 'X-WP-Total';
			const currentResourceValues = [];
			const store = select(COLLECTIONS_STORE_KEY);
			const replace = true;
			const args = [
				namespace,
				resourceName,
				currentQuery,
				currentResourceValues,
				replace
			];

			//console.log(select(COLLECTIONS_STORE_KEY).getCollection(namespace, resourceName, currentQuery, currentResourceValues, ['headers']));

			return {
				items: select(COLLECTIONS_STORE_KEY).getCollection(namespace, resourceName, currentQuery, currentResourceValues, replace),
				total: select(COLLECTIONS_STORE_KEY).getCollectionHeader(headerKey, namespace, resourceName, currentQuery),
				isLoading: store.hasFinishedResolution('getCollection', args) !== true,
				query: currentQuery,
			}
		}),
		withDispatch(dispatch => {
			return {
				//resetCollection: dispatch(COLLECTIONS_STORE_KEY).setQueryValue,
				resetCollection: dispatch(COLLECTIONS_STORE_KEY).getCollection,
				setQuery: dispatch(QUERY_STATE_STORE_KEY).setQueryValue,
				setQueries: dispatch(QUERY_STATE_STORE_KEY).setValueForQueryContext,
			}
		})
	)(WrappedComponent);
}, 'withTable');
export default withTable;

