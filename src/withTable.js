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
			this.props.setQuery(this.props.resourceName, 'page', page);
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


		render() {
			const {items, query, isLoading} = this.props;
			return (
				<OriginalComponent
					setOrderBy={this.onOrderBy}
					setPageChange={this.onPageChange}
					setSearch={this.onSearch}
					setSelected={this.onSelected}
					setAllSelected={this.onAllSelected}
					setonAction={this.onAction}
					items={items}
					isLoading={isLoading}
					query={query}
				/>
			)
		}
	}

	WrappedComponent.propTypes = {
		selected: PropTypes.array,
	};
	WrappedComponent.defaultProps = {
		selected: [],
	};

	return compose(
		withSelect((select, ownProps) => {
			const {resourceName} = ownProps;

			if (!resourceName) {
				throw new Error('You must pass resource name');
			}
			const currentQuery = select(QUERY_STATE_STORE_KEY).getValueForQueryContext(resourceName);
			const namespace = '/ea/v1';
			const headerKey = 'X-WP-Total';
			const currentResourceValues = [];
			const store = select(COLLECTIONS_STORE_KEY);

			const args = [
				namespace,
				resourceName,
				currentQuery,
				currentResourceValues,
			];

			return {
				items: select(COLLECTIONS_STORE_KEY).getCollection(namespace, resourceName, currentQuery, currentResourceValues),
				total: select(COLLECTIONS_STORE_KEY).getCollectionHeader(headerKey, namespace, resourceName, currentQuery),
				isLoading: store.hasFinishedResolution('getCollection', args) !== true,
				query: currentQuery,
			}
		}),
		withDispatch(dispatch => {
			return {
				setQuery: dispatch(QUERY_STATE_STORE_KEY).setQueryValue,
			}
		})
	)(WrappedComponent);
}, 'withTable');
export default withTable;

