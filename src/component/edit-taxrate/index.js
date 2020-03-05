import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { translate as __ } from 'lib/locale';
import { connect } from 'react-redux';
import notify from 'lib/notify';
import { find } from 'lodash';
import { setCreateItem, setUpdateItem } from 'state/taxrates/action';
import { Modal, TextControl, SelectControl, Icon, Button } from '@eaccounting/components';
import { taxTypes } from 'state/taxrates/initial';
import { getSelectedOption } from 'lib/table';
import { apiRequest, accountingApi } from 'lib/api';

const initial = {
	id: undefined,
	name: '',
	rate: '',
	type: 'normal',
};

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
			isSaving: false,
		};
	}

	componentWillUnmount() {
		this.reset();
	}

	reset = () => {
		this.setState({
			...initial,
		});
	};

	onChangeType = type => {
		this.setState({ type: type.value });
	};

	onSubmit = ev => {
		ev.preventDefault();
		const { id, name, rate, type } = this.state;
		this.setState({ isSaving: true });

		const item = {
			id: parseInt(id, 10),
			name,
			rate,
			type,
		};

		if (item.id) {
			this.props.onSave(item.id, item);
			return this.props.onClose(ev);
		}
		apiRequest(accountingApi.taxrates.create(item))
			.then(res => {
				notify(__('Tax Rate created successfully'));
				this.props.onCreate(res.data);
				this.setState({ isSaving: false });
				this.props.onClose(ev);
			})
			.catch(error => {
				this.setState({ isSaving: false });
				notify(error.message, 'error');
			});
	};

	render() {
		const { tittle = __('Add Tax Rate'), buttonTittle = __('Submit'), onClose } = this.props;
		const { name, rate, type, enabled, isSaving } = this.state;

		return (
			<Modal title={tittle} onRequestClose={onClose}>
				<form onSubmit={this.onSubmit}>
					<TextControl
						label={__('Name')}
						value={name}
						before={<Icon icon="id-card-o" />}
						placeholder={__('Enter Name')}
						required
						onChange={name => {
							this.setState({ name });
						}}
					/>

					<TextControl
						label={__('Rate')}
						value={rate}
						before={<Icon icon="percent" />}
						placeholder={__('Enter Rate')}
						required
						onChange={rate => {
							this.setState({ rate: rate.replace(/[^\d.]+/g, '') });
						}}
					/>

					<SelectControl
						label={__('Type')}
						options={taxTypes}
						value={getSelectedOption(taxTypes, type, 'normal')}
						before={<Icon icon="bars" />}
						required
						onChange={this.onChangeType}
					/>
					<Button isPrimary isBusy={isSaving} onClick={this.onSubmit}>
						{buttonTittle}
					</Button>
				</form>
			</Modal>
		);
	}
}

function mapDispatchToProps(dispatch) {
	return {
		onSave: (id, item) => {
			dispatch(setUpdateItem(id, item));
		},
		onCreate: item => {
			dispatch(setCreateItem(item));
		},
	};
}

export default connect(null, mapDispatchToProps)(EditTaxRate);
