import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
import {apiRequest, accountingApi} from "lib/api";
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
		type: PropTypes.string
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
		const {type=''} = this.props;
		apiRequest(accountingApi.contacts.list({...params, type})).then((res) => {
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
						__('No Items')
					}}
					getOptionLabel={option => Object.keys(option) ? `${option.first_name} ${option.last_name}` : ''}
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
