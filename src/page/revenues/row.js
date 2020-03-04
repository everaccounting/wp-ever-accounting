import {Component, Fragment} from "react";
import PropTypes from "prop-types";
import {RowActions} from '@eaccounting/components';
import {translate as __} from 'lib/locale';
import {connect} from "react-redux";
import Moment from 'react-moment';
import Link from "component/link";

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
		console.log('edit');
		// this.setState({editing: !this.state.editing});
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

	goTo = (ev, route) => {
		ev.preventDefault();
		this.props.history.push(route);
	};

	render() {
		const {isSelected, disabled, item} = this.props;
		const {id, paid_at, amount, account, contact, category} = item;
		const {editing} = this.state;
		const {match} = this.props;
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
					</th>


					<td className="column-primary column-paid_at">
						<Link href={`${match.url}/${id}`}><Moment format={"DD-MM-YYYY"}>{paid_at}</Moment></Link>
						{/*<a href="#" onClick={(e)=> this.goTo( e, `/${id}`)}><Moment format={"DD-MM-YYYY"}>{paid_at}</Moment></a>*/}
					</td>


					<td className="column-amount">
						{amount}
					</td>

					<td className="column-category">

					</td>


					<td className="column-account">
					</td>


					<td className="column-customer">

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
								onClick: this.onEdit,
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
			dispatch({type: "REVENUES_SELECTED", ids: [ids]});
		}
	};
}

export default connect(
	null,
	mapDispatchToProps
)(Row);
