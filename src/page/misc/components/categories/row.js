import {Component, Fragment} from "react";
import PropTypes from "prop-types";

import {setSelected, performTableAction, updateCategory} from 'state/categories/action';
import RowActions from 'component/table/row-action';
import {STATUS_SAVING, STATUS_IN_PROGRESS} from 'lib/status';
import Column from 'component/table/column';
import {translate as __} from 'lib/locale';
import EditCategory from "component/edit-category";
import {connect} from "react-redux";
import Spinner from "component/spinner";

class CategoriesRow extends Component {
	static propTypes = {
		item: PropTypes.object.isRequired,
	};

	constructor(props) {
		super(props);
		this.state = {};
	}

	renderActions(saving) {
		return (
			<RowActions disabled={saving}>
				{this.getActions()}
			</RowActions>
		);
	}


	getActions() {
		const {id, enabled} = this.props.item;
		const actions = [];
		actions.push([__('Edit'), this.onEdit]);
		actions.push([__('Delete'), this.onDelete]);
		if (enabled) {
			actions.push([__('Disable'), this.onDisable]);
		} else {
			actions.push([__('Enable'), this.onEnable]);
		}

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
		const {id, name, type, enabled} = this.props.item;
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
				</Column>

				<Column className="column-type">
					{type}
				</Column>

				<Column className="column-status">
					{enabled ? <span className='ea-item-status enabled'>{__('Enabled')}</span> :
						<span className='ea-item-status disabled'>{__('Disabled')}</span>}
					{this.state.editing &&
					<EditCategory item={this.props.item} tittle={__('Update Category')} buttonTittle={__('Update')}
								  onClose={this.onClose}/>}
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
		onSaveCategory: (id, item) => {
			dispatch(updateCategory(id, item));
		},
		onTableAction: (action, ids) => {
			dispatch(performTableAction(action, ids));
		},
	};
}

export default connect(
	null,
	mapDispatchToProps,
)(CategoriesRow);
