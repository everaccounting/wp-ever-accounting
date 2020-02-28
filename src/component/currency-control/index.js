import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
import {apiRequest, eAccountingApi} from "lib/api";
import {AsyncSelect} from '@eaccounting/components'
import PropTypes from "prop-types";

export default class CurrencyControl extends Component {
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

		selected && selected.length && this.getCurrencies({include: selected}, (options) => {
			this.setState({
				value: options
			})
		});


		this.getCurrencies({}, (options) => {
			this.setState({
				defaultOptions: options
			})
		});
	}

	getCurrencies = (params, callback) => {
		apiRequest(eAccountingApi.currencies.list(params)).then((res) => {
			callback(res.data.map(item => {
				return {
					label: `${item.name}(${item.symbol})`,
					value: item.code,
				};
			}))
		});
	};

	onChange = (value) => {
		this.setState({
			value
		});
		this.props.onChange && this.props.onChange(value);
	};

	render() {
		const {value, defaultOptions} = this.state;
		return (
			<Fragment>
				<AsyncSelect
					placeholder={__('Select Currency')}
					defaultOptions={defaultOptions}
					value={value}
					onChange={this.onChange}
					noOptionsMessage={() => {
						__('No items')
					}}
					loadOptions={(search, callback) => {
						this.getCategory({search}, callback);
					}}
					{...this.props}
				/>
			</Fragment>
		)
	}
}
