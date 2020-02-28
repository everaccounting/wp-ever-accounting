import {Component} from "react";
import PropTypes from "prop-types";
import {connect} from "react-redux";

import {RowAction, Column, Spinner} from '@eaccounting/components';
import {STATUS_SAVING, STATUS_IN_PROGRESS} from 'lib/status';
import {translate as __} from 'lib/locale';
import {setSelected, setBulkAction, setUpdateItem} from 'state/taxrates/action';
import EditTaxRate from 'component/edit-taxrate';

class Row extends Component {
	static propTypes = {
		item: PropTypes.object.isRequired,
	};

	constructor(props) {
		super(props);
		this.state = {};
	}

	renderActions(saving) {
		return (
			<RowAction disabled={saving}>
				{this.getActions()}
			</RowAction>
		);
	}

	getActions() {
		const {id} = this.props.item;
		const actions = [];
		actions.push([__('Edit'), this.onEdit]);
		actions.push([__('Delete'), this.onDelete]);

		return actions
			.map((item, pos) => <a key={pos} href={item[2] ? item[2] : '#'} onClick={item[1]}>{item[0]}</a>)
			.reduce((prev, curr) => [prev, ' | ', curr]);
	}

	onEdit = ev => {
		ev.preventDefault();
		this.setState({editing: !this.state.editing});
	};

	onDelete = ev => {
		ev.preventDefault();
		this.props.onTableAction('delete', this.props.item.id);
	};
	onSelected = () => {
		this.props.onSetSelected([this.props.item.id]);
	};

	onClose = () => {
		this.setState({editing: !this.state.editing});
	};

	render() {
		const {id, name, rate, type} = this.props.item;
		const {status, selected} = this.props;
		const isLoading = status === STATUS_IN_PROGRESS;
		const isSaving = status === STATUS_SAVING;
		const hideRow = isLoading || isSaving;


		return (
			<tr className={hideRow ? 'disabled' : ''}>
				<th scope="row" className="check-column">
					{!isSaving &&
					<input type="checkbox" name="item[]" value={id} disabled={isLoading} checked={selected}
						   onChange={this.onSelected}/>}
					{isSaving && <Spinner size="small"/>}
				</th>

				<Column className="column-primary column-name">
					<strong><a href="#" onClick={this.onEdit}>{name}</a></strong>
					{this.renderActions(isSaving)}

					{this.state.editing &&
					<EditTaxRate item={this.props.item} tittle={__('Update Item')} buttonTittle={__('Update')}
								 onClose={this.onClose}/>}
				</Column>

				<Column className="column-rate">
					{parseFloat(rate)}
				</Column>

				<Column className="column-type ea-capitalize">
					{type}
				</Column>
			</tr>
		)
	}

}

function mapDispatchToProps(dispatch) {
	return {
		onSetSelected: items => {
			dispatch(setSelected(items));
		},
		onSaveItem: (id, item) => {
			dispatch(setUpdateItem(id, item));
		},
		onTableAction: (action, ids) => {
			dispatch(setBulkAction(action, ids));
		},
	};
}

export default connect(
	null,
	mapDispatchToProps,
)(Row);
