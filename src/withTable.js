import {Component} from '@wordpress/element';
import {createHigherOrderComponent} from '@wordpress/compose';
import PropTypes from 'prop-types';
import {debounce} from 'lodash';

const withTable = createHigherOrderComponent((OriginalComponent) => {
	class WrappedComponent extends Component {
		constructor() {
			super(...arguments);
			this.state = {
				endpoint:'',
				rows: [],
				saving: [],
				total: 0,
				perPage: parseInt(eAccountingi10n.per_page, 10),
				selected: [],
				filters: {},
				page: 1,
				error: null,
				status: "IN_PROGRESS",
			};
		}

		onMount = (endpoint, params) => {

		};

		onOrderBy = (orderby, order) => {

		};

		onPageChange = ({page}) => {

		};

		onSearch = ({search}) => {

		};

		onSelected = (ids) => {

		};

		onAllSelected = (onoff) => {

		};

		onAction = (action, ids) => {

		};


		render() {
			const {query, error, loading, items} = this.state;
			return (
				<OriginalComponent
					{...this.props}
					setMount={this.onMount}
					setOrderBy={this.onOrderBy}
					setPageChange={this.onPageChange}
					setSearch={this.onSearch}
					setSelected={this.onSelected}
					setAllSelected={this.onAllSelected}
					setonAction={this.onAction}
					query={query}
					error={error}
					isLoading={loading}
					items={items}
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
	return WrappedComponent;
}, 'withTable');
export default withTable;
