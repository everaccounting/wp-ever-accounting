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
		selected: PropTypes.any
	};

	constructor(props) {
		super(props);
		this.state = {
			defaultOptions: []
		};
	}

	componentDidMount() {
		const {selected} = this.props;

		// selected && selected.length && this.getAccounts({include: selected}, (options) => {
		// 	this.setState({
		// 		value: options
		// 	})
		// });

		this.getAccounts({}, (options) => {
			this.setState({
				defaultOptions: options
			})
		});
	}

	getAccounts = (params, callback) => {
		apiRequest(accountingApi.accounts.list(params)).then((res) => {
			callback(res.data.map(item => {
				return {
					label: `${item.name}`,
					value: item.id,
				};
			}))
		});
	};

	onChange = (value) => {
		this.props.onChange && this.props.onChange(value);
	};

	render() {
		const {value, defaultOptions} = this.state;
		return (
			<Fragment>
				<AsyncSelect
					placeholder={__('Select Account')}
					defaultOptions={defaultOptions}
					value={value}
					onChange={this.onChange}
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
