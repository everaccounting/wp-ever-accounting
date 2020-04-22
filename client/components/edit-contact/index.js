import { Component, Fragment } from 'react';
import { __ } from '@wordpress/i18n';
import { Icon, Modal, TextControl, SelectControl, TextareaControl, CurrencyControl } from '@eaccounting/components';
import { CONTACT_TYPES } from '@eaccounting/data';

export default class EditContact extends Component {
	constructor(props) {
		super(props);
		this.state = {
			id: null,
			user_id: null,
			first_name: '',
			last_name: '',
			email: '',
			phone: '',
			address: '',
			city: '',
			state: '',
			postcode: '',
			country: '',
			website: '',
			note: '',
			avatar_url: '',
			types: [],
			tax_number: '',
			currency: {},
			created_at: '',
			updated_at: '',
		};
	}

	onSubmit = () => {};

	render() {
		const { tittle = __('Add Contact'), buttonTittle = __('Submit'), onClose } = this.props;

		return (
			<Fragment>
				<Modal title={tittle} onRequestClose={onClose}>
					<form onSubmit={this.onSubmit}>
						<SelectControl
							label={__('Contact Types')}
							before={<Icon icon="id-card-o" />}
							value={this.state.types}
							isMulti
							required
							onChange={types => this.setState({ types })}
							options={CONTACT_TYPES}
						/>

						<TextControl
							label={__('First Name')}
							before={<Icon icon="id-card-o" />}
							value={this.state.first_name}
							onChange={first_name => this.setState({ first_name })}
							required
						/>

						<TextControl
							label={__('Last Name')}
							before={<Icon icon="id-card-o" />}
							value={this.state.last_name}
							onChange={last_name => this.setState({ last_name })}
							required
						/>

						<CurrencyControl
							label={__('Currency')}
							before={<Icon icon="id-card-o" />}
							value={this.state.currecny}
							required
						/>

						<TextControl
							label={__('Email')}
							before={<Icon icon="id-card-o" />}
							value={this.state.email}
							type={'email'}
							onChange={email => this.setState({ email })}
						/>

						<TextControl
							label={__('Phone')}
							before={<Icon icon="id-card-o" />}
							value={this.state.phone}
							onChange={phone => this.setState({ phone })}
						/>
						<TextControl
							label={__('Address')}
							before={<Icon icon="id-card-o" />}
							value={this.state.address}
							onChange={address => this.setState({ address })}
						/>
						<TextControl
							label={__('City')}
							before={<Icon icon="id-card-o" />}
							value={this.state.city}
							onChange={city => this.setState({ city })}
						/>
						<TextControl
							label={__('State')}
							before={<Icon icon="id-card-o" />}
							value={this.state.state}
							onChange={state => this.setState({ state })}
						/>
						<TextControl
							label={__('PostCode')}
							before={<Icon icon="id-card-o" />}
							value={this.state.postcode}
							onChange={postcode => this.setState({ postcode })}
						/>
						<TextControl
							label={__('Country')}
							before={<Icon icon="id-card-o" />}
							value={this.state.country}
							onChange={country => this.setState({ country })}
						/>
						<TextControl
							label={__('Website')}
							before={<Icon icon="id-card-o" />}
							value={this.state.website}
							onChange={website => this.setState({ website })}
						/>
						<TextControl
							label={__('Tax Number')}
							before={<Icon icon="id-card-o" />}
							value={this.state.tax_number}
							onChange={tax_number => this.setState({ tax_number })}
						/>
						<TextareaControl label={__('Note')} value={this.state.note} onChange={note => this.setState({ note })} />
					</form>
				</Modal>
			</Fragment>
		);
	}
}
