import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
import {
	Card,
	CompactCard,
	Icon,
	TextareaControl,
	TextControl,
	DateControl,
	PriceControl,
	Spinner
} from "@eaccounting/components";
import AccountControl from "../account-control";
import CategoryControl from "../category-control";
import ContactControl from "../contact-control";
import {connect} from "react-redux";
import {loadRevenue} from "store/revenue";
import {STATUS_COMPLETE} from "../../status";

class EditRevenue extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	componentDidMount() {
		const {match} = this.props;
		const id = match.params.id || undefined;
		id && this.props.onMount(id);
	}


	render() {
		const {id, paid_at, amount, currency_code, currency, account_id, category_id, description, contact_id, reference, status} = this.props;

		return (
			<Fragment>
				<pre>
					{paid_at}
					{JSON.stringify(this.props)}
				</pre>
				{!id && <CompactCard tagName="h3">{__('Add Revenue')}</CompactCard>}
				{!!id && <CompactCard tagName="h3">{__('Update Revenue')}</CompactCard>}
				<Card>

					<div className="ea-row">

						<div className="ea-col-6">
							<DateControl
								label={__('Date')}
								before={<Icon icon={'calendar'}/>}
								value={paid_at}
								required
								onChange={(paid_at) => {
									this.setState({paid_at})
								}}/>
						</div>

						<div className="ea-col-6">
							<PriceControl
								label={__('Amount')}
								before={<Icon icon={'university'}/>}
								currency={currency}
								required
								selected={amount}
								onChange={(amount) => console.log(amount)}/>
						</div>
						<div className="ea-col-6">
							<AccountControl
								label={__('Account')}
								before={<Icon icon={'university'}/>}
								after={currency_code}
								required
								value={{}}
								onChange={(account) => console.log(account)}/>
						</div>

					</div>

				</Card>
			</Fragment>
		)
	}
}

function mapDispatchToProps(dispatch) {
	return {
		onMount: (id) => {
			dispatch(loadRevenue(id));
		}
	}
}

export default connect(
	(state) => ({...state.revenue}),
	mapDispatchToProps
)(EditRevenue);
