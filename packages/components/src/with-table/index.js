import {Component} from '@wordpress/element';
import {createHigherOrderComponent} from '@wordpress/compose';
import PropTypes from 'prop-types';
import {debounce} from 'lodash';
import {withDispatch, withSelect, select} from '@wordpress/data';
import {compose} from '@wordpress/compose';
import {COLLECTIONS_STORE_KEY, QUERY_STATE_STORE_KEY} from '@eaccounting/data';

const withTable = createHigherOrderComponent(OriginalComponent => {
	class WrappedComponent extends Component {
		constructor(props) {
			super(props);
			this.state = {
				total: 0
			};
		}

		onOrderBy = (orderby, order) => {
			this.props.setQuery(this.props.resourceName, orderby, order);
		};

		onPageChange = page => {
			this.props.setQuery(this.props.resourceName, 'page', page || 1);
		};

		onSearch = search => {
			this.props.setQuery(this.props.resourceName, {search});
		};

		onSelected = ids => {
		};

		onAllSelected = onoff => {
		};

		onAction = (action, ids) => {
		};

		onDelete = item => {
			this.props.resetCollection('/ea/v1', this.props.resourceName, {}, [item.id], true);
			// this.props.setQuery(this.props.resourceName, {repalce});
		};

		render() {
			const {items, query, total, isLoading, selected, page, per_page = 20} = this.props;

			console.log(total);
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
					selected={selected}
					per_page={per_page}
					items={items}
					isLoading={isLoading}
					query={query}
					page={page}
				/>
			);
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
				...query,
				...select(QUERY_STATE_STORE_KEY).getValueForQueryContext(resourceName),
			};

			const namespace = '/ea/v1';
			const store = select(COLLECTIONS_STORE_KEY);
			const args = [namespace, resourceName, currentQuery];
			const {items, headers} = select(COLLECTIONS_STORE_KEY).getCollection(namespace, resourceName, currentQuery);
			const isLoading = store.hasFinishedResolution('getCollection', args) === false;
			const total = parseInt(headers['x-wp-total'], 10) || 0;
			const {page = 1} = currentQuery;
			return {
				items,
				total,
				isLoading,
				page,
				query: currentQuery,
			};
		}),
		withDispatch(dispatch => {
			return {
				//resetCollection: dispatch(COLLECTIONS_STORE_KEY).setQueryValue,
				// resetCollection: dispatch(COLLECTIONS_STORE_KEY).getCollection,
				setQuery: dispatch(QUERY_STATE_STORE_KEY).setQueryValue,
				// setQueries: dispatch(QUERY_STATE_STORE_KEY).setValueForQueryContext,
			};
		})
	)(WrappedComponent);
}, 'withTable');
export default withTable;
