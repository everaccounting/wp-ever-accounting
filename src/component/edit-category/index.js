import React, {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {translate as __} from 'lib/locale';
import {connect} from 'react-redux';
import notify from "lib/notify";
import {
	Modal,
	TextControl,
	SelectControl,
	Icon,
	Button
} from '@eaccounting/components';
import {find} from 'lodash';
import {setCreateItem, setUpdateItem} from 'state/categories/action'
import {apiRequest, eAccountingApi} from "lib/api";
import {ColorPicker, Popover} from '@wordpress/components';

const categoryTypes = [
	{
		label: __('Expense'),
		value: 'expense',
	},
	{
		label: __('Income'),
		value: 'income',
	},
	{
		label: __('Item'),
		value: 'item',
	},
	{
		label: __('Other'),
		value: 'other',
	}
];

const initial = {
	id: 0,
	name: '',
	color: '',
	type: undefined
};

class EditCategory extends Component {
	static propTypes = {
		item: PropTypes.object,
		onClose: PropTypes.func,
		tittle: PropTypes.string,
		buttonTittle: PropTypes.string,
		childSave: PropTypes.func,
		callback: PropTypes.func,
	};

	static defaultProps = {
		item: {},
	};

	constructor(props) {
		super(props);

		this.state = {
			...initial,
			...props.item,
			type: find(categoryTypes, {value: (props.item.type || 'income')}),
			isSaving: false,
		};
	}

	componentWillUnmount() {
		this.reset();
	}

	reset = () => {
		this.setState({
			...initial,
			colorPickerOpen: false,
		});
	};


	onSubmit = ev => {
		ev.preventDefault();
		const {id, name, type = {}, color} = this.state;
		this.setState({isSaving: true});

		const item = {
			id: parseInt(id, 10),
			name,
			color,
			type: type.value
		};

		if (item.id) {
			this.props.onSave(item.id, item);
			return this.props.onClose(ev);
		}
		apiRequest(eAccountingApi.categories.create({...item, type: type.value})).then(res => {
			notify(__('Category created successfully'));
			this.props.onCreate(res.data);
			this.setState({isSaving: false});
			this.props.onClose(ev);
		}).catch(error => {
			this.setState({isSaving: false});
			notify(error.message, 'error');
		})
	};

	onSetColor = (color) => {
		this.setState({
			color: color.hex,
			colorPickerOpen: false,
		})
	};

	colorInput = () => {
		const {color, colorPickerOpen} = this.state;
		return (<Fragment>
			<span className="ea-color-preview" style={{backgroundColor: color}}/>
		</Fragment>)
	};

	render() {
		const {tittle = __('Add Category'), buttonTittle = __('Submit'), onClose} = this.props;
		const {name, type, color,colorPickerOpen, isSaving} = this.state;

		return (
			<Modal title={tittle} onRequestClose={onClose}>
				<form onSubmit={this.onSubmit}>
					<TextControl label={__('Category Name')}
								 before={<Icon icon='id-card-o'/>}
								 value={name}
								 required
								 onChange={(name) => {
									 this.setState({name})
								 }}/>

					<SelectControl label={__('Category Type')}
								   before={<Icon icon='bars'/>}
								   options={categoryTypes}
								   value={type}
								   required
								   onChange={(type) => {
									   this.setState({type})
								   }}/>

					<TextControl label={__('Color')}
								 value={color}
								 before={this.colorInput()}
								 className={'ea-color-picker'}
								 onClick={()=>{this.setState({colorPickerOpen: !this.state.colorPickerOpen})}}
								 onChange={()=>{}}/>
					{colorPickerOpen && <Popover
						poistion="middle-right"
						className="ea-modal-color-picker" style={{position:'initial'}}>
						<ColorPicker
							color={color}
							onChangeComplete={this.onSetColor}
							disableAlpha
						/>
					</Popover>}

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
			dispatch(setUpdateItem(id, item));
		},
		onCreate: item => {
			dispatch(setCreateItem(item));
		}
	};
}

export default connect(
	null,
	mapDispatchToProps,
)(EditCategory);

