import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
import {apiRequest, accountingApi} from "../../lib/api";
import {Card, CompactCard, DatePicker, Icon, TextareaControl, TextControl, DateControl} from "@eaccounting/components";
import AccountControl from "../account-control";
import CategoryControl from "../category-control";
import ContactControl from "../contact-control";

const initial = {
	id: undefined,
	account_id: undefined,
	account: {},
	paid_at: '',
	amount: '',
	contact_id: '',
	description: '',
	category_id: '',
	reference: '',
	payment_method: '',
	attachment_url: '',
	parent_id: '',
	reconciled: '',
};
export default class EditRevenue extends Component {
	constructor(props) {
		super(props);
		this.state = {
			...initial
		};
	}

	componentDidMount() {
		const {match} = this.props;
		const {params} = match;
		const {id} = params;
		if (id) {
			apiRequest(accountingApi.revenues.get(id)).then(res => {
				this.setState({
					...this.state,
					...res.data
				})
			});
		}
	}


	render() {
		const {account_id, category_id, description, reference, contact_id, paid_at} = this.state;
		return (
			<Fragment>

				<CompactCard tagName="h3">{__('Add Revenue')}</CompactCard>
				<Card>
					<div className="ea-row">
						{/*<div className="ea-col-6">*/}
						{/*	<DateControl*/}
						{/*		label={__('Date')}*/}
						{/*		before={<Icon icon={'calendar'}/>}*/}
						{/*		value={paid_at}*/}
						{/*		required*/}
						{/*		onChange={(paid_at)=> {this.setState({paid_at})}}/>*/}
						{/*</div>*/}

						{/*<div className="ea-col-6">*/}
						{/*	<AccountControl*/}
						{/*		label={__('Account')}*/}
						{/*		before={<Icon icon={'university'}/>}*/}
						{/*		required*/}
						{/*		selected={account_id}*/}
						{/*		onChange={(account) => console.log(account)}/>*/}
						{/*</div>*/}

						{/*<div className="ea-col-6">*/}
						{/*	<CategoryControl*/}
						{/*		label={__('Category')}*/}
						{/*		before={<Icon icon={'folder-open-o'}/>}*/}
						{/*		required*/}
						{/*		selected={category_id}*/}
						{/*		onChange={(category) => console.log(category)}/>*/}
						{/*</div>*/}

						<div className="ea-col-6">
							<ContactControl
								label={__('Customer')}
								before={<Icon icon={'user'}/>}
								value={contact_id}
								onChange={(contact_id) => this.setState({contact_id})}/>
						</div>

						<div className="ea-col-6">
							<ContactControl
								label={__('Customer')}
								before={<Icon icon={'user'}/>}
								value={[account_id]}
								isMulti
								onChange={(account_id) => this.setState({account_id})}/>
						</div>

						<div className="ea-col-6">
							<TextControl
								label={__('Reference')}
								before={<Icon icon={'file-text-o'}/>}
								selected={reference}
								onChange={(reference) => console.log(reference)}/>
						</div>

						<div className="ea-col-12">
							<TextareaControl
								label={__('Description')}
								onChange={description => console.log(description)}
								value={description}/>
						</div>


					</div>
				</Card>
			</Fragment>
		)
	}
}
