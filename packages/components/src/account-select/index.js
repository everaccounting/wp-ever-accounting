/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { Component, Fragment, createRef } from '@wordpress/element';
/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { withDispatch, withSelect } from '@wordpress/data';
import { compose } from '@wordpress/compose';
/**
 * Internal dependencies
 */
import AsyncSelect from '../select-control/async';
import PropTypes from 'prop-types';
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import { NotificationManager } from 'react-notifications';
import Create from './create';

class AccountSelect extends Component {
	static propTypes = {
		label: PropTypes.string,
		placeholder: PropTypes.string,
		isMulti: PropTypes.bool,
		onChange: PropTypes.func,
		before: PropTypes.node,
		after: PropTypes.node,
		value: PropTypes.any,
		enableCreate: PropTypes.bool,
	};

	constructor(props) {
		super(props);
		this.state = {
			isAdding: false,
		};
		this.ref = createRef();
		this.handleSubmit = this.handleSubmit.bind(this);
	}

	fetchAPI(params, callback) {
		apiFetch({ path: addQueryArgs('/ea/v1/accounts', params) }).then(res => {
			callback(res);
		});
	}

	handleSubmit(data) {
		apiFetch({ path: '/ea/v1/accounts', method: 'POST', data })
			.then(res => {
				NotificationManager.success(sprintf(__('"%s" account created.'), res.name));
				this.setState({ isAdding: !this.state.isAdding });
				this.ref.current.select.select.setValue(res);
				this.props.resetForSelectorAndResource('getCollection', 'accounts');
			})
			.catch(error => NotificationManager.error(error.message));
	}

	render() {
		return (
			<Fragment>
				{this.state.isAdding && (
					<Create onSubmit={this.handleSubmit} onClose={() => this.setState({ isAdding: !this.state.isAdding })} />
				)}
				<AsyncSelect
					defaultOptions={this.props.defaultOptions}
					getOptionLabel={option => option && option.name && option.name}
					getOptionValue={option => option && option.id && option.id}
					loadOptions={(search, callback) => {
						this.fetchAPI({ search }, callback);
					}}
					innerRef={this.ref}
					noOptionsMessage={() => __('No accounts')}
					footer={this.props.create}
					onFooterClick={() => {
						this.ref.current.select.select.blur();
						this.setState({ isAdding: !this.state.isAdding });
					}}
					{...this.props}
				/>
			</Fragment>
		);
	}
}

AccountSelect.propTypes = {
	label: PropTypes.string,
	placeholder: PropTypes.string,
	isMulti: PropTypes.bool,
	onChange: PropTypes.func,
	before: PropTypes.node,
	after: PropTypes.node,
	value: PropTypes.any,
	create: PropTypes.bool,
};

export default compose(
	withSelect((select, ownProps) => {
		const { include = [] } = ownProps;
		const { getCollection } = select('ea/collection');
		const { items } = getCollection('accounts', { include });
		return {
			defaultOptions: items,
		};
	}),
	withDispatch(dispatch => {
		const { resetForSelectorAndResource } = dispatch('ea/collection');
		return {
			resetForSelectorAndResource,
		};
	})
)(AccountSelect);
