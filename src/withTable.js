import {Component} from '@wordpress/element';
import {createHigherOrderComponent} from '@wordpress/compose';
import PropTypes from 'prop-types';
import {debounce} from 'lodash';
import {withDispatch, withSelect, select} from '@wordpress/data';
import {compose} from '@wordpress/compose';
import {STORE_KEY} from './data';

const withTable = createHigherOrderComponent((OriginalComponent) => {
	class WrappedComponent extends Component {
		constructor() {
			super(...arguments);
			const {orderby = "id", order = "DESC"} = this.props;
			this.state = {
				total: 0,
				orderby,
				order,
				perPage: parseInt(eAccountingi10n.per_page, 10),
				filters: {},
				page: 1,
				error: null,
				status: "IN_PROGRESS",
			};
		}

		componentDidUpdate(prevProps, prevState) {
			if (prevState.orderby !== this.state.orderby
				|| prevState.order !== this.state.order
				|| prevState.filters !== this.state.filters
				|| prevState.page !== this.state.page) {
				const {resourceName} = this.props;
				const {page} = this.state;
				console.log("should update");
				this.props.getItems('/ea/v1', resourceName, {page});
			}
		}

		onOrderBy = (orderby, order) => {
			this.setState({
				orderby, order
			})
		};

		onPageChange = (page) => {
			this.setState({
				page
			})
		};

		onSearch = (search) => {
			this.setState({
				search
			})
		};

		onSelected = (ids) => {

		};

		onAllSelected = (onoff) => {

		};

		onAction = (action, ids) => {

		};


		render() {
			// console.log(this.props);
			// const {query, error, loading, items} = this.state;
			// const { isResolving, hasFinishedResolution } = select(
			// 	STORE_KEY
			// );
			// console.log(isResolving( 'getCollection'));
			// console.log(isResolving( 'getCollection', '/ea/v1', 'contacts'));
			// console.log(hasFinishedResolution( 'getCollection', '/ea/v1', 'contacts'));
			// console.log(hasFinishedResolution( 'getCollection'));
			return (
				<OriginalComponent
					{...this.props}
					setOrderBy={this.onOrderBy}
					setPageChange={this.onPageChange}
					setSearch={this.onSearch}
					setSelected={this.onSelected}
					setAllSelected={this.onAllSelected}
					setonAction={this.onAction}
					// query={query}
					// error={error}
					// isLoading={loading}
					// items={items}
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
			const {resourceName = 'transactions'} = ownProps;
			return {
				items: select(STORE_KEY).getCollection('/ea/v1', resourceName)
			}
		}),
		withDispatch(dispatch => {
			return {
				getItems: dispatch(STORE_KEY).receiveCollection
			}
		})
	)(WrappedComponent);
}, 'withTable');
export default withTable;

