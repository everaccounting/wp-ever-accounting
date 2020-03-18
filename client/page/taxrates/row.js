import {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {RowActions} from '@eaccounting/components';
import {__} from '@wordpress/i18n';
import EditTaxRate from "components/edit-taxrate";

export default class Row extends Component {
	static propTypes = {
		item: PropTypes.object.isRequired,
		isLoading: PropTypes.bool.isRequired,
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

	onSelected = () => {
		this.props.onSetSelected([this.props.item.id]);
	};

	onClose = () => {
		this.setState({editing: !this.state.editing});
	};

	OnSave = (item) => {
		this.onEdit();
		this.props.invalidateCollection();
	};

	render() {
		const {isSelected, isLoading, item} = this.props;
		const { id, name, rate, type } = item;
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
							onChange={() => this.props.onSelected(id)}/>

						{editing && (
							<EditTaxRate
								item={this.props.item}
								onCreate={this.OnSave}
								onClose={this.onClose}
								buttonTittle={__('Update')}
								tittle={__('Update Tax Rate')}/>
						)}

					</th>

					<td className="column-primary column-name">{name}</td>

					<td className="column-rates">{parseFloat(rate)}</td>

					<td className="column-type ea-capitalize">{type}</td>


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
									onClick: () => this.props.onRemove(id),
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
