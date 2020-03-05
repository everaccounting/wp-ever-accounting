import { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';
import {
	Card,
	CompactCard,
	Icon,
	TextareaControl,
	TextControl,
	DateControl,
	PriceControl,
	Spinner,
} from '@eaccounting/components';
import AccountControl from '../account-control';
import CategoryControl from '../category-control';
import ContactControl from '../contact-control';
import { connect } from 'react-redux';
import { loadRevenue } from 'store/revenue';
import { STATUS_COMPLETE } from '../../status';

class EditRevenue extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	componentDidMount() {
		const { match } = this.props;
		const id = match.params.id || undefined;
		id && this.props.onMount(id);
	}

	render() {
		const { id, paid_at, amount, account_id, category_id, description, contact_id, reference, status } = this.props;

		return (
			<Fragment>
				{JSON.stringify(this.state.contact_id)}
				<CompactCard tagName="h3">{__('Add Revenue')}</CompactCard>
				<Card>
					{status === STATUS_COMPLETE ? (
						<div className="ea-row">
							<div className="ea-col-6">
								<DateControl
									label={__('Date')}
									before={<Icon icon={'calendar'} />}
									value={paid_at}
									required
									onChange={paid_at => {
										this.setState({ paid_at });
									}}
								/>
							</div>

							<div className="ea-col-6">
								<PriceControl
									label={__('Amount')}
									before={<Icon icon={'university'} />}
									required
									selected={amount}
									onChange={amount => console.log(amount)}
								/>
							</div>

							<div className="ea-col-6">
								<AccountControl
									label={__('Account')}
									before={<Icon icon={'university'} />}
									required
									value={{}}
									onChange={account => console.log(account)}
								/>
							</div>

							<div className="ea-col-6">
								{/*<CategoryControl*/}
								{/*	label={__('Category')}*/}
								{/*	before={<Icon icon={'folder-open-o'}/>}*/}
								{/*	required*/}
								{/*	selected={category_id}*/}
								{/*	onChange={(category) => console.log(category)}/>*/}
							</div>

							<div className="ea-col-6">
								{/*<ContactControl*/}
								{/*	label={__('Customer')}*/}
								{/*	before={<Icon icon={'user'}/>}*/}
								{/*	value={contact_id}*/}
								{/*	onChange={(contact_id) => this.setState({contact_id})}/>*/}
							</div>

							<div className="ea-col-6">
								<TextControl
									label={__('Reference')}
									before={<Icon icon={'file-text-o'} />}
									value={reference}
									onChange={reference => console.log(reference)}
								/>
							</div>

							<div className="ea-col-12">
								<TextareaControl
									label={__('Description')}
									onChange={description => console.log(description)}
									value={description}
								/>
							</div>
						</div>
					) : (
						<Spinner />
					)}
				</Card>
			</Fragment>
		);
	}
}

function mapDispatchToProps(dispatch) {
	return {
		onMount: id => {
			dispatch(loadRevenue(id));
		},
	};
}

export default connect(state => ({ ...state.revenue }), mapDispatchToProps)(EditRevenue);
