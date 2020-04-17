/**
 * External dependencies
 */
import {Fragment, Component} from "@wordpress/element";
import {__} from '@wordpress/i18n';
import General from "./sections/general";
import Defaults from "./sections/defaults";
import Company from "./sections/company";
import {withSelect, withDispatch} from "@wordpress/data";
import {compose} from '@wordpress/compose';
import apiFetch from "@wordpress/api-fetch";

class Settings extends Component {
	constructor(props) {
		super(props);
	}

	onSubmit = (form) => {
		form.default_account = form.default_account && form.default_account.id && form.default_account.id;
		form.default_currency = form.default_currency && form.default_currency.code && form.default_currency.code;
		apiFetch({path: '/ea/v1/settings', method: 'POST', data: form}).then(res => {
			this.props.resetSettings('fetchAPI', 'settings');
		}).catch(error => {
			alert(error.message)
		});
	};

	render() {
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Settings')}</h1>
				<hr className="wp-header-end"/>
				<div style={{height: '30px'}}/>
				<Company {...this.props} onSubmit={this.onSubmit}/>
				<Defaults {...this.props} onSubmit={this.onSubmit}/>

			</Fragment>
		)
	}
}

export default compose([
	withSelect((select) => {
		const {fetchAPI, isRequestingFetchAPI} = select('ea/collection');
		return {
			settings: fetchAPI('settings', {}),
			isLoading: isRequestingFetchAPI('settings', {}),
		}
	}),
	withDispatch((dispatch) => {
		const {resetForSelectorAndIdentifier} = dispatch('ea/collection');
		return {
			resetSettings: resetForSelectorAndIdentifier
		}
	})
])(Settings);
