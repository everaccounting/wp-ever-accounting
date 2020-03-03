import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
import {apiRequest, accountingApi} from "lib/api";
import {AsyncSelect} from '@eaccounting/components'
import PropTypes from "prop-types";

export default class AccountControl extends Component {
	static propTypes = {
		label: PropTypes.string,
		placeholder: PropTypes.string,
		isMulti: PropTypes.bool,
		onChange: PropTypes.func,
		before: PropTypes.node,
		after: PropTypes.node,
		value: PropTypes.any
	};

	constructor(props) {
		super(props);
		this.state = {
			defaultOptions: []
		};
	}

	componentDidMount() {
		this.getContacts({}, (options) => {
			this.setState({
				defaultOptions: options
			})
		});
	}


	// componentDidUpdate(prevProps) {
	// 	if (prevProps.value !== this.props.value && this.props.value) {
	// 		this.getContacts({include: this.props.value}, (options) => {
	// 			this.setState({
	// 				value: options
	// 			});
	// 		});
	// 	}
	// }

	getContacts = (params, callback) => {
		apiRequest(accountingApi.contacts.list(params)).then((res) => {
			callback(res.data.map(item => {
				return {
					label: `${item.first_name} ${item.last_name}`,
					value: item.id,
				};
			}))
		});
	};

	render() {
		const {defaultOptions} = this.state;
		return (
			<Fragment>
				<AsyncSelect
					defaultOptions={defaultOptions}
					noOptionsMessage={() => {
						__('No items')
					}}
					loadOptions={(search, callback) => {
						this.getAccounts({search}, callback);
					}}
					{...this.props}
				/>
			</Fragment>
		)
	}
}
