import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
import {getApi, eAccountingApi} from "lib/api";
import {AsyncSelect} from '@eaccounting/components'
import PropTypes from "prop-types";

export default class ContactControl extends Component {
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

		selected && this.getContacts({include: selected}, (options) => {
			this.setState({
				value: options
			})
		});

		this.getContacts({}, (options) => {
			this.setState({
				defaultOptions: options
			})
		});
	}

	getContacts = (params, callback) => {
		getApi(eAccountingApi.contacts.list(params)).then((res) => {
			callback(res.items.map(item => {
				return {
					label: `${item.first_name} ${item.last_name}`,
					value: item.id,
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
					placeholder={__('Select Contacts')}
					defaultOptions={defaultOptions}
					value={value}
					onChange={this.onChange}
					noOptionsMessage={() => {
						__('No items')
					}}
					loadOptions={(search, callback) => {
						this.getContacts({search}, callback);
					}}
					{...this.props}
				/>
			</Fragment>
		)
	}
}
