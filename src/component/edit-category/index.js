import React, {Component} from 'react';
import PropTypes from 'prop-types';
import {translate as __} from 'lib/locale';
import {connect} from 'react-redux';
import notify from "lib/notify";
import {
	Modal,
	TextControl,
	ReactSelect,
	ToggleControl,
	Icon
} from '@eaccounting/components';
import {includes} from 'lodash';
import {createCategory, updateCategory} from 'state/categories/action'
import {initialCategory} from 'state/categories/selection';
const Types = [
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


class EditCategory extends Component {
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
		const {name, type, enabled = true} = props.item;
		this.state = {
			name,
			type,
			enabled,
			isSaving: false,
		};
		this.ref = React.createRef();
	}

	reset = () => {
		this.setState( {
			... initialCategory,
		} );
	};

	onSubmit = ev => {
		ev.preventDefault();
		const {
			name,
			type,
			enabled
		} = this.state;

		if (name === '' || type === '') {
			this.setState({isSaving: false});
			notify(__('One or more required value missing, please correct & submit again'), 'error');
			return false;
		}

		const item = {
			id: parseInt(this.props.item.id, 10),
			name,
			type:type.value,
			status: (enabled === true) ? 'active' : 'inactive'
		};

		if (item.id) {
			this.props.onSave(item.id, item);
		} else {
			this.props.onCreate(item);
		}

		this.props.onClose ? this.props.onClose(ev) : () => {
		};

		if (this.props.childSave) {
			this.props.childSave();
		}

	};

	render() {
		const {tittle = __('Add Category'), buttonTittle = __('Save'), onClose} = this.props;
		const {
			name,
			type = {},
			enabled,
			isSaving
		} = this.state;

		const categoryType = Types.filter((filter, index) => {
			return includes(type, filter.value) === true;
		});

		return (
			<form onSubmit={this.onSave} ref={this.ref}>
				<Modal title={tittle} onRequestClose={onClose}>
					<form onSubmit={this.onSubmit}>

						<TextControl label={__('Category Name')}
									 value={name}
									 before={<Icon icon='id-card-o'/>}
									 required
									 onChange={(name) => {
										 this.setState({name})
									 }}/>

						<ReactSelect label={__('Category Type')}
									 value={categoryType}
									 before={<Icon icon='bars'/>}
									 options={Types}
									 required
									 onChange={(type) => {
										 this.setState({type})
									 }}/>
						<ToggleControl label={__('Enabled')}
									   checked={enabled}
									   onChange={() => {
										   this.setState({enabled: !this.state.enabled})
									   }}/>
						{this.props.children && this.props.children}
						<input className="button-primary" type="submit" name="add" value={buttonTittle}
							   disabled={isSaving || name === '' || !type.hasOwnProperty('value')}/>
					</form>
				</Modal>
			</form>
		)
	}
}

function mapDispatchToProps(dispatch) {
	return {
		onSave: (id, item) => {
			dispatch(updateCategory(id, item));
		},
		onCreate: item => {
			dispatch(createCategory(item));
		}
	};
}

export default connect(
	null,
	mapDispatchToProps,
)(EditCategory);

