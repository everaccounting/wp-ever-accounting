/**
 * External dependencies
 */
import {Component} from '@wordpress/element';
import {createHigherOrderComponent} from '@wordpress/compose';
import {addQueryArgs} from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import {xor, debounce} from "lodash";
import {__} from "@wordpress/i18n";

const withAccounts = createHigherOrderComponent((OriginalComponent) => {
	return class WrappedComponent extends Component {
		constructor() {
			super(...arguments);
			this.state = {
				status: "STATUS_IN_PROGRESS",
				items: [],
				selected: [],
				total: 0,
				query: {}
			};
			this.loadAccounts = debounce( this.loadAccounts.bind( this ), 200 );
		}

		componentDidMount() {
			this.loadAccounts();
		}

		componentWillUnmount() {
			this.loadAccounts.cancel();
		}


		loadAccounts(params = {}) {
			this.setState({status: "STATUS_IN_PROGRESS", query: {...this.state.query, ...params}});
			apiFetch({
				path: addQueryArgs('/ea/v1/accounts', ({...this.state.query, ...params})),
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

		setSearch = (search) => {
			this.loadAccounts({search, page: 1})
		};

		setChangePage = (page) => {
			this.loadAccounts({page})
		};

		setAction = (action, ids) => {
			const data = {
				items: ids ? [ids] : this.state.selected,
				action,
			};
			if (action === 'delete' && !confirm(__('Are you sure you want to delete the selected items?'))) {
				return false;
			}

			apiFetch({
				path: 'ea/v1/accounts/bulk',
				method: 'POST',
				data,
				cache: 'no-store',
			}).then(this.loadAccounts({})).catch(error => {
				alert(error.message);
				this.setState({
					selected: []
				})
			});
		};

		setOrderBy = (orderby, order) => {
			this.loadAccounts({orderby, order})
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
					onChangePage={this.setChangePage}
					onSetOrderBy={this.setOrderBy}
					onAction={this.setAction}
					onSetSelected={this.setSelected}
					onSetAllSelected={this.setAllSelected}
				/>
			)
		}

	}
}, 'withAccounts');

export default withAccounts;
