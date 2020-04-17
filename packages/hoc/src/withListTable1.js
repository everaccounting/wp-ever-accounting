import {createHigherOrderComponent, compose} from '@wordpress/compose';
import {withDispatch, withSelect} from '@wordpress/data';
import {Component, createRef} from '@wordpress/element';
import qs from "querystring";
import {PER_PAGE} from "@eaccounting/data";
import {addQueryArgs} from "@wordpress/url"
import {forOwn, clone, pickBy, isNumber, isEmpty} from "lodash";
import isShallowEqual from '@wordpress/is-shallow-equal';

export const withListTable = createHigherOrderComponent(WrappedComponent => {
	class Hoc extends Component {
		constructor(props) {
			super(props);
			this.setSearch = this.setSearch.bind(this);
			this.setPage = this.setPage.bind(this);
			this.setOrderBy = this.setOrderBy.bind(this);
			this.setFilter = this.setFilter.bind(this);
		}

		// shouldComponentUpdate(nextProps, nextState, nextContext) {
		// 	return !isShallowEqual(nextState, this.queries) || !isShallowEqual(nextProps.table, this.props.table)
		// }
		updatePageQuery(params = {}) {
			const {history, location} = this.props;
			history.push(decodeURIComponent(addQueryArgs(location.pathname, params)));
		}


		getQueries() {
			return pickBy(clone(this.queries), value => !isEmpty(value));
		}

		setSearch(search) {
			this.setState({...this.queries, search});
		}

		setPage(page) {
			this.setState({...this.queries, page})
		}

		setOrderBy(orderby, order) {
			this.setState({...this.queries, orderby, order})
		}

		setFilter(filter) {
			this.setState({...{...this.queries, page: 1}, ...filter});
		}


		render() {
			// const {per_page = PER_PAGE, page = 1, orderby = 'id', order = 'desc'} = this.queries;
			return null;
			// return (
			// 	<WrappedComponent
			// 		{...this.props}
			// 		getQueries={this.getQueries}
			// 		per_page={parseInt(per_page, 10)}
			// 		page={parseInt(page, 10)}
			// 		orderby={orderby}
			// 		order={order}
			// 		ref={this.child}
			// 		onSearch={this.setSearch}
			// 		onPageChange={this.setPage}
			// 		onOrderBy={this.setOrderBy}
			// 		onFilter={this.setFilter}/>
			// );
		}

	}

	return compose(
		withSelect(select => {
			const {getTable} = select('ea/store');
			return {
				table: getTable()
			}
		}),
		withDispatch((dispatch) => {
			const {setTable, setTableSelected, setTableAllSelected} = dispatch('ea/store');
			return {
				setTable,
				setTableSelected,
				setTableAllSelected
			}
		})
	)(Hoc);

}, 'withListTable');

export default withListTable;
