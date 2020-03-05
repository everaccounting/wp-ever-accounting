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
		this.getAccounts({}, (options) => {
			this.setState({
				defaultOptions: options
			})
		});
	}

	getAccounts = (params, callback) => {
		apiRequest(accountingApi.accounts.list(params)).then((res) => {
			callback(res.data);
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
					getOptionLabel={option => option.name}
					getOptionValue={option => option.id}
					loadOptions={(search, callback) => {
						this.getAccounts({search}, callback);
					}}
					{...this.props}
				/>
			</Fragment>
		)
	}
}
