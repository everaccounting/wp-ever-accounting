import {Component} from '@wordpress/element';
import PropTypes from 'prop-types';
import {Icon, ReactSelect} from "@eaccounting/components";
import {find} from 'lodash';
import {translate as __, numberFormat} from 'lib/locale';
import {eAccountingApi, getApi} from "lib/api";
export default class CurrencyControl extends Component {
	constructor(props) {
		super(props);
		this.state = {
			currencies: []
		}
	}

	componentDidMount() {
		getApi(eAccountingApi.currencies.list({per_page: 100})).then(res => {
			const currencies = res.items;
			this.setState({
				currencies,
			});
		});
	}

	onChange = (currency) => {
		this.props.onChange(find(this.state.currencies, {code: currency.value}))
	};

	render() {
		const {label, value, help, className, onChange, before, after, type, required, ...props} = this.props;
		const {currencies} = this.state;
		const options = currencies.map((currency) => {
			return {
				label: currency.name,
				value: currency.code
			}
		});

		return (
			<ReactSelect label={__('Account Currency')}
						 value={{label: value.name, value: value.code}}
						 before={before}
						 after={after}
						 required
						 onChange={this.onChange}
						 options={options}
						 {...props}/>
		)
	}
}

CurrencyControl.defaultProps = {
	label: __('Currency'),
	before: <Icon icon='exchange'/>,
};

CurrencyControl.propTypes = {
	className: PropTypes.string,
	label: PropTypes.string,
	help: PropTypes.string,
	value: PropTypes.object,
	onChange: PropTypes.func,
	required: PropTypes.bool,
	before: PropTypes.node,
	after: PropTypes.node,
};


