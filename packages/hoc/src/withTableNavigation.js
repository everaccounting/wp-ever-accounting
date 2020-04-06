/**
 * WordPress dependencies
 */
import {Component} from '@wordpress/element';
import {createHigherOrderComponent, compose} from '@wordpress/compose';
import {withDispatch, withSelect} from '@wordpress/data';
import {PER_PAGE} from "@eaccounting/data";
import {addQueryArgs} from "@wordpress/url"
import isShallowEqual from '@wordpress/is-shallow-equal';
import qs from "querystring";
import {forOwn, clone, pickBy, isNumber, isEmpty} from "lodash";

const defaultQueries = {
	per_page: 20,
	page: 1,
	orderby: '',
	order: 'desc',
};

const parseNumber = (obj) => {
	const prepared = {};
	forOwn(obj, (value, key) => {
		prepared[key] = !isNaN(value) && '' !== value ? parseInt(value, 10) : value;
	});
	return prepared;
};

const withTableNavigation = (initQuery = {}) => {
	return createHigherOrderComponent(WrappedComponent => {
		class Hoc extends Component {
			constructor(props) {
				super(props);
				this.defaults = parseNumber({
					...defaultQueries,
					...initQuery,
				});
				const urlArgs = qs.parse(props.history.location.search.substring(1));
				const pageArgs = parseNumber(Object.assign({}, defaultQueries, initQuery, urlArgs));
				this.state = {
					...pageArgs
				};

				this.getPageQueries = this.getPageQueries.bind(this);
				this.setSearch = this.setSearch.bind(this);
				this.setPage = this.setPage.bind(this);
				this.setOrderBy = this.setOrderBy.bind(this);
				this.setSelected = this.setSelected.bind(this);
				this.setAllSelected = this.setAllSelected.bind(this);
				this.setFilter = this.setFilter.bind(this);
				this.hasFilter = this.hasFilter.bind(this);
				this.resetFilter = this.resetFilter.bind(this);
				this.setDelete = this.setDelete.bind(this);
			}

			shouldComponentUpdate(nextProps, nextState, nextContext) {
				return !isShallowEqual(nextState, this.state)
			}

			componentDidUpdate() {
				const {history, location} = this.props;
				history.push(decodeURIComponent(addQueryArgs(location.pathname, this.getPageQueries())));
			}

			getPageQueries() {
				const queries = pickBy(clone(this.state), value => isNumber(value) || !isEmpty(value));
				forOwn(queries, (value, key) => {
					if (this.defaults[key] && this.defaults[key] === value) {
						delete queries[key];
					}
				});

				return queries;
			}

			setSearch(search) {
				this.setState({...this.state, search})
			}

			setPage(page) {
				this.setState({...this.state, page})
			}

			setOrderBy(orderby, order) {
				this.setState({...this.state, orderby, order})
			}

			setSelected(id) {

			}

			setAllSelected(onoff) {

			}

			setFilter(filter) {
				this.setState({...{...this.state, page: 1}, ...filter});
			}

			hasFilter(){
				const {page, per_page, order, orderby, ...filter} = this.getPageQueries();
				return !isEmpty({...filter});
			}

			resetFilter(){
				forOwn(this.state, (value, key) => {
					if(['page', 'per_page', 'order', 'orderby'].indexOf(key) === -1){
						delete this.state.key
					}
				});

				// console.log(this.state);
			}

			setDelete(id) {

			}


			render() {
				const query = this.getPageQueries(false);
				const {per_page, page, orderby, order} = this.state;
				return <WrappedComponent
					{...this.props}
					queries={this.getPageQueries()}
					per_page={per_page}
					page={page}
					orderby={orderby}
					order={order}
					resetFilter={this.resetFilter}
					onSearch={this.setSearch}
					onPageChange={this.setPage}
					onOrderBy={this.setOrderBy}
					onSelected={this.setSelected}
					onAllSelected={this.setAllSelected}
					onFilter={this.setFilter}
					onDelete={this.setDelete}/>;
			}

		}

		return Hoc;
	}, 'withTableNavigation');
};


export default withTableNavigation;
