import React, {Component} from 'react';
import PropTypes from 'prop-types';
import {translate as __} from 'lib/locale';
import {connect} from 'react-redux';
import notify from "lib/notify";
import {find} from 'lodash';
import {
	createItem,
	updateItem
} from 'state/taxrates/action';
import {
	Modal,
	TextControl,
	SelectControl,
	ToggleControl,
	Icon,
	Button
} from '@eaccounting/components';

const initial = {
	id: undefined,
	name: '',
	rate: '',
	type: undefined,
	enabled: true
};

const taxTypes = [
	{
		label: __('Normal'),
		value: 'normal',
	},
	{
		label: __('Inclusive'),
		value: 'inclusive',
	},
	{
		label: __('Compound'),
		value: 'compound',
	}
];

class EditTaxRate extends Component {
	static propTypes = {
		item: PropTypes.object,
		onClose: PropTypes.func,
		tittle: PropTypes.string,
		buttonTittle: PropTypes.string,
		childSave: PropTypes.func,
		callback: PropTypes.func,
	};

	constructor(props) {
		super(props);

		this.state = {
			...initial,
			...props.item,
			type:find(taxTypes, {value:(props.type||'normal')}),
			isSaving: false,
		};
	}

	reset = () => {
		this.setState({
			...initial,
		});
	};

	onSubmit = ev => {
		ev.preventDefault();
		const {id, name, rate, type = {}, enabled} = this.state;
		this.setState({isSaving: true});
		if (name.trim() === '' || rate.trim() === '' || !Object.keys(type).length) {
			this.setState({isSaving: false});
			notify(__('One or more required value missing, please correct & submit again'), 'error');
			return false;
		}


		const item = {
			id: parseInt(id, 10),
			name,
			rate,
			type: type.value,
			status: (enabled === true) ? 'active' : 'inactive'
		};

		if (item.id) {
			this.props.onSave(item.id, item);
		} else {
			this.props.onCreate(item);
		}

		this.props.onClose ? this.props.onClose(ev) : () => {
		};
	};


	render() {
		const {tittle = __('Add Tax Rate'), buttonTittle = __('Submit'), onClose} = this.props;
		const {name, rate, type, enabled, isSaving} = this.state;

		return (
			<Modal title={tittle} onRequestClose={onClose}>
				<form onSubmit={this.onSubmit}>
					<TextControl label={__('Name')}
								 value={name}
								 before={<Icon icon='id-card-o'/>}
								 placeholder={__('Enter Name')}
								 required
								 onChange={(name) => {
									 this.setState({name})
								 }}/>

					<TextControl label={__('Rate')}
								 value={rate}
								 before={<Icon icon='percent'/>}
								 placeholder={__('Enter Rate')}
								 required
								 onChange={(rate) => {
									 this.setState({rate:rate.replace(/[^\d.]+/g, '')})
								 }}/>

					<SelectControl label={__('Type')}
								   options={taxTypes}
								   value={type}
								   before={<Icon icon='bars'/>}
								   required
								   onChange={(type) => {
									   this.setState({type})
								   }}/>

					<ToggleControl label={__('Enabled')}
								   checked={enabled}
								   onChange={() => {
									   this.setState({enabled: !this.state.enabled})
								   }}/>

					<Button isPrimary
							isBusy={isSaving}
							onClick={this.onSubmit}>
						{buttonTittle}
					</Button>

				</form>
			</Modal>
		)
	}
}

function mapDispatchToProps(dispatch) {
	return {
		onSave: (id, item) => {
			dispatch(updateItem(id, item));
		},
		onCreate: item => {
			dispatch(createItem(item));
		}
	};
}

export default connect(
	null,
	mapDispatchToProps,
)(EditTaxRate);
