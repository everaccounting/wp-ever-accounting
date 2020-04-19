/**
 * External dependencies
 */
import {Component, Fragment, createRef} from '@wordpress/element';
import {withDispatch, withSelect} from '@wordpress/data';
import {compose} from '@wordpress/compose';
/**
 * WordPress dependencies
 */
import {__, sprintf} from '@wordpress/i18n';
/**
 * Internal dependencies
 */
import AsyncSelect from '../select-control/async';
import PropTypes from 'prop-types';
import {addQueryArgs} from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import Create from "./create";
import {NotificationManager} from "react-notifications";

class ContactSelect extends Component {

	constructor(props) {
		super(props);
		this.state = {
			isAdding: false,
		};
		this.ref = createRef();
		this.handleSubmit = this.handleSubmit.bind(this);
		this.fetchAPI = this.fetchAPI.bind(this);
	}

	fetchAPI(params, callback) {
		const {type} = this.props;
		apiFetch({path: addQueryArgs('/ea/v1/contacts', {...params, type})}).then(res => {
			callback(res);
		});
	}

	handleSubmit(data) {
		const {type} = this.props;
		apiFetch({path: '/ea/v1/contacts', method: 'POST', data: {...data, type}}).then(res => {
			NotificationManager.success(sprintf(__('"%s" %s created.'), res.name, type));
			this.setState({isAdding: false});
			this.ref.current.select.select.setValue(res);
			this.props.resetForSelectorAndResource('getCollection', 'contacts');
		}).catch(error => NotificationManager.error(error.message))
	}

	render() {
		return (
			<Fragment>
				{this.state.isAdding && <Create
					onSubmit={this.handleSubmit}
					onClose={() => this.setState({isAdding: !this.state.isAdding})}/>}

				<AsyncSelect
					defaultOptions={this.state.defaultOptions}
					getOptionLabel={option => option && option.name && option.name}
					getOptionValue={option => option && option.id && option.id}
					loadOptions={(search, callback) => {
						this.fetchAPI({search}, callback);
					}}
					innerRef={this.ref}
					//noOptionsMessage={() => __('No customers')}
					footer={this.props.create}
					onFooterClick={() => {
						this.ref.current.select.select.blur();
						this.setState({isAdding: !this.state.isAdding});
					}}
					{...this.props}
				/>
			</Fragment>
		)
	}

}

ContactSelect.propTypes = {
	label: PropTypes.string,
	placeholder: PropTypes.string,
	isMulti: PropTypes.bool,
	onChange: PropTypes.func,
	before: PropTypes.node,
	after: PropTypes.node,
	value: PropTypes.any,
	create: PropTypes.bool,
	type: PropTypes.string,
};

export default compose(
	withSelect((select, ownProps) => {
		const {include = [], type = 'customer'} = ownProps;
		const {getCollection} = select('ea/collection');
		const {items} = getCollection('contacts', {include, type});
		return {
			defaultOptions: items,
			type
		}
	}),
	withDispatch(dispatch => {
		const {resetForSelectorAndResource} = dispatch('ea/collection');
		return {
			resetForSelectorAndResource,
		}
	})
)(ContactSelect);
