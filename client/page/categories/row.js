import {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {RowActions} from '@eaccounting/components';
import {__} from '@wordpress/i18n';
import EditCategory from "./edit-category";
import EditCurrency from "../currencies/edit-currency";

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
		const {isLoading, item} = this.props;
		const {id, name, type, color} = item;

		return (
			<Fragment>
				<tr className={isLoading ? 'disabled' : ''}>
					<th scope="row" className="column-primary column-name">
						{name}
						{this.state.editing && <EditCategory
							onSubmit={(data) => this.props.handleSubmit(data, this.closeModal)}
							onClose={this.closeModal}
							buttonTittle={__('Update')}
							tittle={__('Update Category')}
							item={item}/>}
					</th>

					<td className="column-type ea-capitalize">{type}</td>

					<td className="column-type">
						<span style={{color: color}} className="fa fa-2x fa-circle"/>
					</td>

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
