import {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {RowActions} from '@eaccounting/components';
import {__} from '@wordpress/i18n';

// import EditAccount from "component/edit-account";

export default class Row extends Component {
	static propTypes = {
		item: PropTypes.object.isRequired,
		isLoading: PropTypes.bool.isRequired,
		isSelected: PropTypes.bool,
		onAction: PropTypes.func.isRequired,
		onSetSelected: PropTypes.func.isRequired,
	};

	constructor(props) {
		super(props);
		this.state = {
			editing: false,
		};
	}

	onEdit = () => {
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
		const {isSelected, isLoading, item} = this.props;
		const {id, name, balance, number} = item;
		const {editing} = this.state;

		return (
			<Fragment>
				<tr className={isLoading ? 'disabled' : ''}>
					<th scope="row" className="check-column">
						<input
							type="checkbox"
							name="item[]"
							value={id}
							disabled={isLoading}
							checked={isSelected}
							onChange={() => this.props.onSetSelected(item.id)}
						/>

						{/*{editing && (*/}
						{/*	<EditAccount*/}
						{/*		item={this.props.item}*/}
						{/*		onCreate={this.props.onUpdate}*/}
						{/*		onClose={this.onClose}*/}
						{/*		buttonTittle={__('Update')}*/}
						{/*		tittle={__('Update Account')}*/}
						{/*	/>*/}
						{/*)}*/}

					</th>

					<td className="column-primary column-name">{name}</td>

					<td className="column-number">{number || '-'}</td>

					<td className="column-money">{balance}</td>

					<td className="column-actions">
						<RowActions
							controls={[
								{
									title: __('Edit'),
									onClick: this.onEdit,
									disabled: isLoading,
								},
								{
									title: __('Delete'),
									onClick: ()=> this.props.onAction('delete', item.id),
									disabled: isLoading,
								},
							]}
						/>
					</td>
				</tr>
			</Fragment>
		);
	}
}
