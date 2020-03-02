import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
import {apiRequest, accountingApi} from "lib/api";
import {AsyncSelect} from '@eaccounting/components'
import PropTypes from "prop-types";
import {map} from "lodash";

export default class ContactControl extends Component {
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
		// const {value} = this.props;
		// console.log(value);
		// selected && selected.length && this.getContacts({include: selected}, (options) => {
		// 	this.setState({
		// 		value: options
		// 	})
		// });

		this.getContacts({}, (options) => {
			this.setState({
				defaultOptions: options
			})
		});
	}

	componentDidUpdate(prevProps) {
		let {value} = this.props;
		const {oldVal} = prevProps;
		if (oldVal === value)
			return false;
		if ("object" !== typeof value) {
			value = value.split(",");
		}

		console.log(value);
		if(value && value.length && prevProps.value !== this.props.value){
			this.getContacts({include: value}, (options) => {
				this.setState({
					value: options
				});
			});
		}else if(prevProps.value !== this.props.value){
			this.setState({
				value
			});
		}



		console.log('componentDidUpdate UPDATE');
	}

	getContacts = (params, callback) => {
		apiRequest(accountingApi.contacts.list(params)).then((res) => {
			callback(res.data.map(item => {
				return {
					label: `${item.first_name} ${item.last_name}`,
					value: item.id,
				};
			}))
		});

		console.log('API CALL');
	};

	onChange = (value) => {
		const {isMulti = false } = this.props;
		if(value){
			value = map(Array.isArray(value) ? value: [value], 'value');
			value = !isMulti && value.length ? value.pop(): value;
		}

		this.props.onChange && this.props.onChange(value);
		console.log('CHANGED IN CONTACT', value);
	};

	render() {
		const {value:sValue, defaultOptions} = this.state;
		const {value, onChange, ...restProps} = this.props;
		return (
			<Fragment>
				<AsyncSelect
					placeholder={__('Select Contacts')}
					defaultOptions={defaultOptions}
					value={sValue}
					onChange={this.onChange}
					noOptionsMessage={() => {
						__('No items')
					}}
					loadOptions={(search, callback) => {
						this.getContacts({search}, callback);
					}}
					{...restProps}
				/>
			</Fragment>
		)
	}
}
