/**
 * Internal dependencies
 */
import SelectControl from './index';

/**
 * WordPress dependencies
 */
import {__} from '@wordpress/i18n';
import {addQueryArgs} from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import {useState, createRef, cloneElement} from '@wordpress/element';
import {useSelect} from '@wordpress/data';
/**
 * External dependencies
 */
import {STORE_NAME} from '@eaccounting/data';
import {Icon} from '@wordpress/components';
import PropTypes from 'prop-types';
import {get} from 'lodash';

function EntitySelect(props) {
	const {entity, query = {}, values,renderLabel, renderValue, valueKey, labelKey, creatable, modal, modalItem, modalTitle, ...restProps} = props;
	const [isModalOpen, setModalOpen] = useState(false);
	const selectInputRef = createRef();
	const route = useSelect((select) => select(STORE_NAME).getRoute(entity));
	const items = useSelect((select) => select(STORE_NAME).getEntities(entity, query));
	// const Modal = modal;

	const fetchAPI = async (params) => {
		return await apiFetch({
			path: addQueryArgs(route.endpoint, {...params, ...query}),
		});
	};

	const toggleModal = () => {
		setModalOpen(!isModalOpen);
	};

	const handleCreate = async (item) => {
		await selectInputRef.current.select.select.setValue(item);
		toggleModal();
	}

	const onClick = (e) => {
		e.preventDefault();
		e.stopPropagation();
		toggleModal();
	};

	const After = () => {
		const style = {cursor: 'pointer'};
		return <Icon icon="plus" style={style} onClick={onClick}/>
	}

	const getLabel = (option) =>{
		if( renderLabel ){
			return renderLabel(option);
		}

		return get(option, [labelKey]);
	}

	const getValue = (option) =>{
		if( renderValue ){
			return renderValue(option);
		}

		return get(option, [valueKey]);
	}

	return(
		<>
			<SelectControl
				{...restProps}
				async={true}
				defaultOptions={items}
				innerRef={selectInputRef}
				loadOptions={(search) => fetchAPI({search})}
				getOptionLabel={getLabel}
				getOptionValue={getValue}
				after={creatable && modal && <After/>}
				noOptionsMessage={(input) => {
					return input.inputValue
						? __('No Results', 'wp-ever-accounting')
						: __('Type to search', 'wp-ever-accounting');
				}}
			/>
			{isModalOpen && cloneElement(modal, {onClose:toggleModal, onSave:handleCreate})}
		</>
	)
}

EntitySelect.propTypes = {
	entity: PropTypes.string,
	renderLabel:PropTypes.func,
	renderValue:PropTypes.func,
	valueKey: PropTypes.string,
	labelKey: PropTypes.string,
	query: PropTypes.object,
	values: PropTypes.any,
	creatable: PropTypes.bool,
	modal: PropTypes.node,
};

EntitySelect.defaultProps = {
	valueKey: 'id',
	labelKey: 'name',
}
export default EntitySelect;
