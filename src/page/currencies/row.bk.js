import {Component, Fragment} from "react";
import PropTypes from "prop-types";

import {setSelected, setBulkAction, setUpdateItem} from 'state/currencies/action';
import {STATUS_SAVING, STATUS_IN_PROGRESS} from 'lib/status';
import {Column, Spinner, RowAction} from "@eaccounting/components";
import {translate as __} from 'lib/locale';
import EditCurrency from "component/edit-currency";
import {connect} from "react-redux";

class Row extends Component {
	static propTypes = {
		item: PropTypes.object.isRequired,
	};

	constructor(props) {
		super(props);
		this.state = {
			editing: false
		};
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

	onDisable = ev => {
		ev.preventDefault();
		this.props.onTableAction('disable', this.props.item.id);
	};

	onEnable = ev => {
		ev.preventDefault();
		this.props.onTableAction('enable', this.props.item.id);
	};

	onSelected = () => {
		this.props.onSetSelected([this.props.item.id]);
	};

	onClose = () => {
		this.setState({editing: !this.state.editing});
	};

	render() {
		const {id, name, code, rate} = this.props.item;
		const {editing} = this.state;
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

					{editing && <EditCurrency
						item={this.props.item}
						onClose={this.onClose}
						buttonTittle={__('Update')}
						tittle={__('Update Currency')}/>}

				</th>

				<Column className="column-primary column-name">
					<strong><a href="#" onClick={this.onEdit}>{name}</a></strong>
					{this.renderActions(isSaving)}
				</Column>

				<Column className="column-code">
					{code}
				</Column>

				<Column className="column-type">
					{rate}
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
		onSaveCurrency: (id, item) => {
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
