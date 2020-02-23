/**
 * External dependencies
 */

import {Component, Fragment} from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
/**
 * Internal dependencies
 */
import {setSelected, performTableAction, updateRevenue} from 'state/revenues/action';
import {STATUS_SAVING, STATUS_IN_PROGRESS} from 'lib/status';
import Spinner from 'component/spinner';
import Column from 'component/table/column';
import RowActions from 'component/table/row-action';
import EditRevenue from 'component/edit-account';
import { translate as __ } from 'lib/locale';
class RevenuesRow extends Component {
	static propTypes = {
		item: PropTypes.object.isRequired,
		selected: PropTypes.bool.isRequired,
		rowstatus: PropTypes.string.isRequired,
	};

	constructor(props) {
		super(props);

		this.state = {
			editing: false
		};
	}

	// onEdit = ev => {
	// 	ev.preventDefault();
	// 	this.setState({editing: !this.state.editing});
	// };
	//
	// onDelete = ev => {
	// 	ev.preventDefault();
	// 	this.props.onTableAction('delete', this.props.item.id);
	// };
	//
	// onDisable = ev => {
	// 	ev.preventDefault();
	// 	this.props.onTableAction('disable', this.props.item.id);
	// };
	//
	// onEnable = ev => {
	// 	ev.preventDefault();
	// 	this.props.onTableAction('enable', this.props.item.id);
	// };
	//
	// onSelected = () => {
	// 	this.props.onSetSelected([this.props.item.id]);
	// };
	//
	// onClose = () => {
	// 	this.setState({editing: !this.state.editing});
	// };

	// getActions() {
	// 	const {id, enabled} = this.props.item;
	// 	const actions = [];
	// 	actions.push([__('Edit'), this.onEdit]);
	// 	actions.push([__('Delete'), this.onDelete]);
	// 	if (enabled) {
	// 		actions.push([__('Disable'), this.onDisable]);
	// 	} else {
	// 		actions.push([__('Enable'), this.onEnable]);
	// 	}
	//
	// 	return actions
	// 		.map((item, pos) => <a key={pos} href={item[2] ? item[2] : '#'} onClick={item[1]}>{item[0]}</a>)
	// 		.reduce((prev, curr) => [prev, ' | ', curr]);
	// }
	//
	// renderActions(saving) {
	// 	return (
	// 		<RowActions disabled={saving}>
	// 			{this.getActions()}
	// 		</RowActions>
	// 	);
	// }

	render() {
		const {id, date, amount, customer, account, enabled} = this.props.item;
		const {selected, rowstatus, currentDisplaySelected} = this.props;
		const isLoading = rowstatus === STATUS_IN_PROGRESS;
		const isSaving = rowstatus === STATUS_SAVING;
		const hideRow = isLoading || isSaving;
		return (
			<Fragment>
				<tr className={hideRow ? 'disabled' : ''}>

					<th scope="row" className="check-item">
						{!isSaving &&
						<input type="checkbox" name="item[]" value={id} disabled={isLoading} checked={selected} onChange={this.onSelected}/>}
						{isSaving && <Spinner size="small"/>}
					</th>

					<Column enabled="date" className="column-primary column-date">
						<strong><a href="#" onClick={this.onEdit}>{date}</a></strong>
						{/*{this.renderActions(isSaving)}*/}
					</Column>

					<Column enabled="amount" className="column-amount">
						{amount}
					</Column>

					<Column enabled="customer" className="column-customer">
						{customer || '-'}
					</Column>

					<Column enabled="account" className="column-bank-name">
						{account || '-'}
					</Column>

					<Column enabled="status" className="column-status">
						{enabled ? <span className='ea-item-status enabled'>{__('Enabled')}</span> :
							<span className='ea-item-status disabled'>{__('Disabled')}</span>}
						{this.state.editing && <EditRevenue item={this.props.item} tittle={__('Update Revenue')} buttonTittle={__('Update')} onClose={this.onClose}/>}
					</Column>
				</tr>
			</Fragment>

		)
	}
}


function mapDispatchToProps(dispatch) {
	return {
		onSetSelected: items => {
			dispatch(setSelected(items));
		},
		onSaveRevenue: (id, item) => {
			dispatch(updateRevenue(id, item));
		},
		onTableAction: (action, ids) => {
			dispatch(performTableAction(action, ids));
		},
	};
}

function mapStateToProps(state) {
	const {revenues} = state;

	return {
		revenues,
	};
}

export default connect(
	mapStateToProps,
	mapDispatchToProps,
)(RevenuesRow);
