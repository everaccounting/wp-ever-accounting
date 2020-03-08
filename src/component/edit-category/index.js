import React, {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {translate as __} from 'lib/locale';
import notify from 'lib/notify';
import {Modal, TextControl, Select, Icon, Button, SelectControl} from '@eaccounting/components';
import {apiRequest, accountingApi} from 'lib/api';
import {ColorPicker, Popover} from '@wordpress/components';
import "./style.scss";


export default class EditCategory extends Component {
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
			id: null,
			name: "",
			type: "income",
			color: "",
			isSaving: false,
		};
	}

	componentDidMount() {
		const {item} = this.props;
		item && this.setState({
			...this.state,
			...item
		})
	}


	onSubmit = ev => {
		ev.preventDefault();
		const {id, name, type = {}, color} = this.state;
		this.setState({isSaving: true});

		const data = {
			id,
			name,
			color,
			type:type
		};

		let endpoint = accountingApi.categories.create(data);
		if (id) {
			endpoint = accountingApi.categories.update(id, data);
		}

		apiRequest(endpoint).then(res => {
			notify(__('Category saved successfully'));
			this.props.onCreate && this.props.onCreate(res.data);
			this.setState({isSaving: false});
			this.props.onClose(ev);
		}).catch(error => {
			this.setState({isSaving: false});
			notify(error.message, 'error');
		});

	};

	onSetColor = color => {
		this.setState({
			color: color.hex,
			colorPickerOpen: false,
		});
	};

	colorInput = () => {
		const {color, colorPickerOpen} = this.state;
		return (
			<Fragment>
				<span className="ea-color-preview" style={{backgroundColor: color}}/>
			</Fragment>
		);
	};

	render() {
		const {tittle = __('Add Category'), buttonTittle = __('Submit'), onClose} = this.props;
		const {name, type, color, colorPickerOpen, isSaving} = this.state;

		return (
			<Modal title={tittle} onRequestClose={onClose}>
				<form onSubmit={this.onSubmit} className="ea-edit-category">
					<TextControl
						label={__('Category Name')}
						before={<Icon icon="id-card-o"/>}
						value={name}
						required
						onChange={name => {
							this.setState({name});
						}}
					/>

					<Select
						label={__('Category Type')}
						before={<Icon icon="bars"/>}
						options={Object.keys(eAccountingi10n.data.categoryTypes).map(key => {
							return {value: key, label: eAccountingi10n.data.categoryTypes[key]};
						})}
						value={type}
						required
						onChange={type => {
							this.setState({type});
						}}
					/>

					<TextControl
						label={__('Color')}
						value={color}
						before={this.colorInput()}
						className={'ea-color-picker'}
						onClick={() => {
							this.setState({colorPickerOpen: !this.state.colorPickerOpen});
						}}
						onChange={() => {
						}}
					/>
					{colorPickerOpen && (
						<Popover poistion="middle-right" className="ea-modal-color-picker"
								 style={{position: 'initial'}}>
							<ColorPicker color={color} onChangeComplete={this.onSetColor} disableAlpha/>
						</Popover>
					)}

					<Button isPrimary isBusy={isSaving} onClick={this.onSubmit}>
						{buttonTittle}
					</Button>
				</form>
			</Modal>
		);
	}
}
