import { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';
import { apiRequest, accountingApi } from 'lib/api';
import { AsyncSelect } from '@eaccounting/components';
import PropTypes from 'prop-types';

export default class AccountControl extends Component {
	_isMounted = false;

	static propTypes = {
		label: PropTypes.string,
		placeholder: PropTypes.string,
		isMulti: PropTypes.bool,
		onChange: PropTypes.func,
		before: PropTypes.node,
		after: PropTypes.node,
		value: PropTypes.any,
	};

	constructor(props) {
		super(props);
		this.state = {
			defaultOptions: [],
		};
	}

	componentDidMount() {
		this._isMounted = true;
		this.getAccounts({}, options => {
			this._isMounted && this.setState({
				defaultOptions: options,
			});
		});
	}

	componentWillUnmount() {
		this._isMounted = false;
	}

	getAccounts = (params, callback) => {
		apiRequest(accountingApi.accounts.list(params)).then(res => {
			this._isMounted && callback(res.data);
		});
	};

	render() {
		const { defaultOptions } = this.state;
		return (
			<Fragment>
				<AsyncSelect
					defaultOptions={defaultOptions}
					noOptionsMessage={() => {
						__('No items');
					}}
					getOptionLabel={option => option && option.name && option.name }
					getOptionValue={option => option && option.id && option.id}
					loadOptions={(search, callback) => {
						this.getAccounts({ search }, callback);
					}}
					{...this.props}
				/>
			</Fragment>
		);
	}
}
