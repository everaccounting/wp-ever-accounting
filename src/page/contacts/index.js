/**
 * External dependencies
 */

import {Component, Fragment} from 'react';
import {translate as __} from 'lib/locale';
import {connect} from 'react-redux';

/**
 * Internal dependencies
 */
import './style.scss';
import Table from 'component/table';
import TableNav from 'component/table/navigation';
import SearchBox from 'component/search-box';
import BulkAction from 'component/table/bulk-action';
import TableDisplay from 'component/table/table-display';
import MultiOptionDropdown from 'component/multi-option-dropdown';
import ContactsRow from './row';
import {
	getContacts,
	createContact,
	setPage,
	performTableAction,
	setAllSelected,
	setOrderBy,
	setSearch,
	setFilter,
	setDisplay
} from 'state/contacts/action';
import {isEnabled} from 'component/table/utils';
import {STATUS_COMPLETE, STATUS_IN_PROGRESS, STATUS_SAVING} from 'lib/status';
import {
	getFilterOptions,
	getDisplayGroups,
	getDisplayOptions,
	getHeaders,
	getBulk,
	getSearchOptions
} from './constants';
import EditContact from 'component/edit-account';
import {initialContact} from 'state/accounts/selection';

class Contacts extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isAdding:false
		};
		window.addEventListener('popstate', this.onPageChanged);
	}

	componentDidCatch(error, info) {
		this.setState({error: true, stack: error, info});
	}

	componentWillUnmount() {
		window.removeEventListener('popstate', this.onPageChanged);
	}

	componentDidMount() {
		this.props.onLoadContacts();
	}

	onRenderRow = ( row, key, status, currentDisplayType, currentDisplaySelected ) => {
		const { saving } = this.props.accounts;
		const loadingStatus = status.isLoading ? STATUS_IN_PROGRESS : STATUS_COMPLETE;
		const rowStatus = saving.indexOf( row.id ) !== -1 ? STATUS_SAVING : loadingStatus;
		return (
			<ContactsRow
				item={ row }
				key={ row.id }
				selected={ status.isSelected }
				rowstatus={ rowStatus }
				currentDisplayType={ currentDisplayType }
				currentDisplaySelected={ currentDisplaySelected }
				setFilter={ this.setFilter }
				filters={ this.props.accounts.table.filterBy }
			/>
		);
	};

	validateDisplay( selected ) {
		// Ensure we have at least source or title
		if ( selected.indexOf( 'name' ) === -1 ) {
			return selected.concat( [ 'name' ] );
		}
		return selected;
	}

	setFilter = ( filterName, filterValue ) => {
		const { filterBy } = this.props.accounts.table;

		this.props.onFilter( { ...filterBy, [ filterName ]: filterValue ? filterValue : undefined } );
	};

	getHeaders( selected ) {
		return getHeaders().filter( header => isEnabled( selected, header.name ) || header.name === 'cb' || header.name === 'name' );
	}

	onAdd = ev =>{
		ev.preventDefault();
		this.setState({isAdding:!this.state.isAdding});
	};

	onClose = () =>{
		this.setState({isAdding:!this.state.isAdding});
	};

	render() {
		const {status, total, table, rows, saving} = this.props.accounts;
		const {isAdding} = this.state;
		const isSaving = saving.indexOf(0) !== -1;
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Contacts')}</h1>
				<a href="#" className="page-title-action" onClick={this.onAdd}>{__('Add New')}</a>
				<hr className="wp-header-end"/>
				{isAdding && <EditContact item={initialContact} onClose={this.onClose}/>}

				<div className="ea-table-display">
					<TableDisplay
						disable={ status === STATUS_IN_PROGRESS }
						options={ getDisplayOptions() }
						groups={ getDisplayGroups() }
						store="accounts"
						currentDisplayType={ table.displayType }
						currentDisplaySelected={ table.displaySelected }
						setDisplay={ this.props.onSetDisplay }
						validation={ this.validateDisplay }
					/>
					<SearchBox
						status={ status }
						table={ table }
						onSearch={ this.props.onSearch }
						selected={ table.filterBy }
						searchTypes={ getSearchOptions() }
					/>

				</div>

				<TableNav total={ total } selected={ table.selected } table={ table } onChangePage={ this.props.onChangePage } onAction={ this.props.onAction } status={ status } bulk={ getBulk() }>
					<BulkAction>
						<MultiOptionDropdown
							options={ getFilterOptions() }
							selected={ table.filterBy ? table.filterBy : {} }
							onApply={ this.props.onFilter }
							title={ __( 'Filters' ) }
							isEnabled={ status !== STATUS_IN_PROGRESS }
						/>
					</BulkAction>
				</TableNav>

				<Table
					headers={ this.getHeaders( table.displaySelected ) }
					rows={ rows }
					total={ total }
					row={ this.onRenderRow }
					table={ table }
					status={ status }
					onSetAllSelected={ this.props.onSetAllSelected }
					onSetOrderBy={ this.props.onSetOrderBy }
					currentDisplayType={ table.displayType }
					currentDisplaySelected={ table.displaySelected }
				/>


				<TableNav total={ total } selected={ table.selected } table={ table } onChangePage={ this.props.onChangePage } onAction={ this.props.onAction } status={ status } />


			</Fragment>
		);
	}
}

function mapStateToProps(state) {
	const {accounts} = state;
	return {
		accounts,
	};
}

function mapDispatchToProps(dispatch) {
	return {
		onLoadContacts: () => {
			dispatch(getContacts());
		},
		onChangePage: page => {
			dispatch(setPage(page));
		},
		onAction: action => {
			dispatch(performTableAction(action));
		},
		onSetAllSelected: onoff => {
			dispatch(setAllSelected(onoff));
		},
		onSetOrderBy: (column, order) => {
			dispatch(setOrderBy(column, order));
		},
		onFilter: (filterBy) => {
			dispatch(setFilter(filterBy));
		},
		onSearch: (search) => {
			dispatch(setSearch(search));
		},
		onCreate: item => {
			dispatch(createContact(item));
		},
		onSetDisplay: (displayType, displaySelected) => {
			dispatch(setDisplay(displayType, displaySelected));
		},
	}
}

export default connect(
	mapStateToProps,
	mapDispatchToProps,
)(Contacts);
