import {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {RowActions} from '@eaccounting/components';
import {__} from '@wordpress/i18n';
import EditCategory from "./edit-category";

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
		const {isLoading, item} = this.props;
		const {id, name, type, color} = item;

		return (
			<Fragment>
				<tr className={isLoading ? 'disabled' : ''}>
					<th scope="row" className="column-primary column-name">
						{name}
						{this.state.editing && <EditCategory
							onSubmit={(data) => this.props.handleSubmit(data, (res)=> {
								this.setState({editing: false})
							})}
							onClose={() => this.setState({editing: false})}
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
									onClick: () => this.setState({editing: !this.state.editing}),
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
