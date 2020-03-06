import React, {Component} from 'react';
import PropTypes from 'prop-types';
import {translate as __} from 'lib/locale';
import notify from 'lib/notify';
import {Modal, TextControl, SelectControl, Icon, Button} from '@eaccounting/components';
import {apiRequest, accountingApi} from 'lib/api';

export default class EditCurrency extends Component {
	static propTypes = {
		item: PropTypes.object,
		onClose: PropTypes.func,
		onCreate: PropTypes.func,
		tittle: PropTypes.string,
		buttonTittle: PropTypes.string,
	};

	static defaultProps = {
		item: {},
	};

	constructor(props) {
		super(props);

		this.state = {
			id: undefined,
			name: '',
			code: {},
			rate: '',
			isSaving: false,
		};
	}

	componentDidMount() {
		const {item} = this.props;
		this.setState({
			...this.state,
			...item,
			code: eAccountingi10n.data.currencies[item.code] || {}
		})
	}

	onSubmit = ev => {
		ev.preventDefault();
		const {id, name, rate, code} = this.state;
		this.setState({isSaving: true});

		const data = {
			id,
			name,
			code: code.currency,
			rate
		};

		let endpoint = accountingApi.currencies.create(data);
		if (id) {
			endpoint = accountingApi.currencies.update(id, data);
		}

		apiRequest(endpoint).then(res => {
			notify(__('Currency saved successfully'));
			this.props.onCreate && this.props.onCreate(res.data);
			this.setState({isSaving: false});
			this.props.onClose(ev);
		}).catch(error => {
			this.setState({isSaving: false});
			notify(error.message, 'error');
		});
	};

	render() {
		const {tittle = __('Add Currency'), buttonTittle = __('Submit'), onClose} = this.props;
		const {name, rate, code, isSaving} = this.state;

		return (
			<Modal title={tittle} onRequestClose={onClose}>
				<form onSubmit={this.onSubmit}>
					<TextControl
						label={__('Name')}
						value={name}
						before={<Icon icon="id-card-o"/>}
						placeholder={__('Enter Name')}
						required
						onChange={name => {
							this.setState({name});
						}}
					/>
					<SelectControl
						label={__('Code')}
						getOptionLabel={option => option.currency}
						getOptionValue={option => option.currency}
						options={Object.values(eAccountingi10n.data.currencies)}
						value={code}
						before={<Icon icon="code"/>}
						required
						onChange={code => {
							this.setState({code});
						}}
					/>

					<TextControl
						label={__('Rate')}
						value={rate}
						before={<Icon icon="money"/>}
						placeholder={__('Enter Rate')}
						required
						help={__('Rate against default currency')}
						onChange={rate => {
							this.setState({rate: rate.replace(/[^\d.]+/g, '')});
						}}
					/>

					<Button isPrimary isBusy={isSaving} onClick={this.onSubmit}>
						{buttonTittle}
					</Button>
				</form>
			</Modal>
		);
	}
}
