import {Component, Fragment} from "react";
import PropTypes from "prop-types";
import {Column, RowActions} from '@eaccounting/components';
import {translate as __} from 'lib/locale';
import {connect} from "react-redux";
import {BulkAction} from "store/categories";
import EditCurrency from "component/edit-currency";
class Row extends Component {
	static propTypes = {
		item: PropTypes.object.isRequired,
		disabled: PropTypes.bool.isRequired,
		isSelected: PropTypes.bool,
	};

	constructor(props) {
		super(props);

		this.state = {
			editing: false
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

	render() {
		const {isSelected, disabled, item} = this.props;
		const {id, name, code, rate} = item;
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
							onChange={() => this.props.onSetSelected(item.id)}/>

						{editing && <EditCurrency
							item={this.props.item}
							onClose={this.onClose}
							buttonTittle={__('Update')}
							tittle={__('Update Category')}/>}

					</th>


					<td className="column-primary column-name">
						{name}
					</td>

					<td className="column-code">
						{code}
					</td>

					<td className="column-rate">
						{rate}
					</td>

					<td className="column-actions">
						<RowActions controls={[
							{
								title: __('Edit'),
								onClick: this.onEdit,
								disabled: disabled,
							},
							{
								title: __('Delete'),
								onClick: this.onDelete,
								disabled: disabled,
							}
						]}/>
					</td>

				</tr>
			</Fragment>

		)
	}
}


function mapDispatchToProps(dispatch) {
	return {
		onSetSelected: ids => {
			dispatch({type: "CURRENCIES_SELECTED", ids: [ids]});
		},
		onTableAction: (action, ids) => {
			dispatch(BulkAction(action, ids));
		},
	};
}

export default connect(
	null,
	mapDispatchToProps
)(Row);