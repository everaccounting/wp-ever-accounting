import {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {__} from '@wordpress/i18n';
import {RowAction} from '@eaccounting/components';
// import EditCategory from 'component/edit-category';

export default class Row extends Component {
	static propTypes = {
		item: PropTypes.object.isRequired,
		disabled: PropTypes.bool.isRequired,
		isSelected: PropTypes.bool,
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

	onDelete = () => {
		this.props.onTableAction('delete', this.props.item.id);
	};

	onSelected = () => {
		this.props.onSetSelected([this.props.item.id]);
	};

	onClose = () => {
		this.setState({editing: !this.state.editing});
	};

	OnSave = (item) => {
		this.props.onUpdate(item);
	};

	render() {
		const {isSelected, disabled, item} = this.props;
		const {id, name, type, color} = item;
		const {editing} = this.state;

		return (
			<Fragment>
				<tr className={disabled ? 'disabled' : ''}>
					<th scope="row" className="check-column">
						<input
							type="checkbox"
							name="item[]"
							value={id}
							disabled={disabled}
							checked={isSelected}
							onChange={() => this.props.onSetSelected(item.id)}
						/>

						{editing && (
							<EditCategory
								item={this.props.item}
								onCreate={this.OnSave}
								onClose={this.onClose}
								buttonTittle={__('Update')}
								tittle={__('Update Category')}
							/>
						)}
					</th>

					<td className="column-primary column-name">{name}</td>

					<td className="column-type ea-capitalize">{type}</td>

					<td className="column-type">
						<span style={{color: color}} className="fa fa-2x fa-circle"/>
					</td>

					<td className="column-actions">
						<RowAction
							controls={[
								{
									title: __('Edit'),
									onClick: this.onEdit,
									disabled: disabled,
								},
								{
									title: __('Delete'),
									onClick: this.onDelete,
									disabled: disabled,
								},
							]}
						/>
					</td>
				</tr>
			</Fragment>
		);
	}
}
