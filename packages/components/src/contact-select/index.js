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
import Modal from "../modal";
import {Form, Field, FormSpy} from "react-final-form";
import TextControl from "../text-control";
import Button from "../button";
import {NotificationManager} from 'react-notifications';
import CurrencySelect from '../currency-select';
import TextAreaControl from '../textarea-control';


class ContactSelect extends Component {

	constructor(props) {
		super(props);
		this.state= {
			extra:[]
		}
	}

	render(){
		console.log(this.props);
		return(
			<Fragment>
			<AsyncSelect
		defaultOptions={this.state.defaultOptions}
		getOptionLabel={option => option && option.name && option.name}
		getOptionValue={option => option && option.id && option.id}
		loadOptions={(search, callback) => {
			this.fetchAPI({search}, callback);
		}}
		innerRef={this.ref}
		noOptionsMessage={() => __('No customers')}
		withFooter={this.props.enableCreate}
		onAddClick={() => {
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
};

export default compose(
	withSelect((select, ownProps) => {
		const {include = []} = ownProps;
		const {getCollection} = select('ea/collection');
		const {items} = getCollection('contacts', {include});
		return {
			defaultOptions: items
		}
	}),
	withDispatch(dispatch => {
		const {resetForSelectorAndResource} = dispatch('ea/collection');
		return {
			resetForSelectorAndResource,
		}
	})
)(ContactSelect);
