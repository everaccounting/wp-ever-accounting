import {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {RowActions} from '@eaccounting/components';
import {__} from '@wordpress/i18n';
import EditAccount from "./edit-account";

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
		this.openModal = this.openModal.bind(this);
		this.closeModal = this.closeModal.bind(this);
	}

	openModal() {
		this.setState({editing: true});
	};

	closeModal() {
		this.setState({editing: false});
	};

	render() {
		const {isLoading, item, isSelected} = this.props;
		const { id, name, balance, number } = item;

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
					</th>

					<td scope="row" className="column-primary column-name">
						{name}
						{this.state.editing && <EditAccount
							onSubmit={(data) => this.props.handleSubmit(data, this.closeModal)}
							onClose={this.closeModal}
							buttonTittle={__('Update')}
							tittle={__('Update Account')}
							item={item}/>}
					</td>

					<td className="column-number">{number || '-'}</td>
					<td className="column-money">{balance}</td>
					<td className="column-actions">
						<RowActions
							controls={[
								{
									title: __('Edit'),
									onClick: () => this.openModal(),
									disabled: isLoading,
								},
								{
									title: __('Delete'),
									onClick: () => this.props.handleDelete(id),
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
