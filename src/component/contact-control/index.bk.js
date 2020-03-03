import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
import {apiRequest, accountingApi} from "lib/api";
import {AsyncSelect} from '@eaccounting/components'
import PropTypes from "prop-types";

export default class ContactControl extends Component {
	_isMounted = false;

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
		this._isMounted = true;
		//load some default options
		this.getContacts({}, (options) => {
			this._isMounted && this.setState({
				defaultOptions: options,
				value:this.props.value
			})
		});
	}

	componentDidUpdate(prevProps) {
		let {value} = this.props;
		console.log("OLD", prevProps.value);
		console.log("NEW", this.props.value);
		console.log("COMPARE", value && value.length && (prevProps.value !== this.props.value));
		if (value && value.length && (prevProps.value !== this.props.value)) {
			this.getContacts({include: value}, (options) => {
				this._isMounted && this.setState({
					value: options
				});
			});
		} else if (prevProps.value !== this.props.value) {
			this._isMounted && this.setState({
				value
			});
		}
	}

	componentWillUnmount() {
		this._isMounted = false;
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

		console.log('API CALL')
	};

	render() {
		const {value: sValue, defaultOptions} = this.state;
		const {value, ...restProps} = this.props;
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
