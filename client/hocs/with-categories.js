/**
 * External dependencies
 */
import {Component} from '@wordpress/element';
import {createHigherOrderComponent} from '@wordpress/compose';
import {addQueryArgs} from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import {xor} from "lodash";
import {__} from "@wordpress/i18n";
import {removeDefaultQueries} from "./utils";
import {pickBy, isEmpty, isNumber, debounce} from 'lodash';

const withCategories = createHigherOrderComponent((OriginalComponent) => {
	return class WrappedComponent extends Component {
		constructor() {
			super(...arguments);
			this.state = {
				status: "STATUS_IN_PROGRESS",
				items: [],
				selected: [],
				total: 0,
				query: {
					page: 1,
					orderby: 'name',
					order: 'desc'
				}
			};
			this.loadCategories = debounce( this.loadCategories.bind( this ), 200 );
		}

		componentDidMount() {
			this.loadCategories();
		}

		componentWillUnmount() {
			this.loadCategories.cancel();
		}


		loadCategories(params = {}) {
			const {query} = this.state;
			const mergedQuery = pickBy({...query, ...params}, value => isNumber(value) || !isEmpty(value));
			const args = removeDefaultQueries(mergedQuery, 'name');
			this.setState({status: "STATUS_IN_PROGRESS", query: {...mergedQuery}});

			apiFetch({
				path: addQueryArgs('/ea/v1/categories', ({...args})),
				parse: false
			}).then(response => {
				response.json().then(items => {
					const total = parseInt(response.headers.get('x-wp-total'), 10) || this.state.total;
					this.setState({
						...this.state,
						total,
						items,
						selected: [],
						status: "STATUS_COMPLETE"
					})
				})
			}).catch(error => {
				this.setState({
					status: "STATUS_FAILED"
				})
			})
		}

		setAction = (action, ids) => {
			const data = {
				items: ids ? [ids] : this.state.selected,
				action,
			};
			if (action === 'delete' && !confirm(__('Are you sure you want to delete the selected items?'))) {
				return false;
			}

			apiFetch({
				path: 'ea/v1/categories/bulk',
				method: 'POST',
				data,
				cache: 'no-store',
			}).then(this.loadCategories({})).catch(error => {
				alert(error.message);
				this.setState({
					selected: []
				})
			});
		};

		setSearch = (search) => {
			this.loadCategories({search, page: 1})
		};

		setChangePage = (page) => {
			this.loadCategories({page})
		};

		setOrderBy = (orderby, order) => {
			this.loadCategories({orderby, order})
		};

		setSelected = (id) => {
			this.setState({
				selected: xor(this.state.selected, [id])
			})
		};
		setAllSelected = (onoff) => {
			this.setState({
				selected: onoff ? this.state.items.map(item => item.id) : []
			})
		};

		setFilter = (filter) => {
			this.loadCategories(filter)
		};

		render() {
			const {
				status,
				items,
				selected,
				total,
				query
			} = this.state;
			return (
				<OriginalComponent
					status={status}
					items={items}
					selected={selected}
					total={total}
					query={query}
					onSearch={this.setSearch}
					onFilter={this.setFilter}
					onChangePage={this.setChangePage}
					onSetOrderBy={this.setOrderBy}
					onAction={this.setAction}
					onSetSelected={this.setSelected}
					onSetAllSelected={this.setAllSelected}
				/>
			)
		}

	}
}, 'withCategories');

export default withCategories;
