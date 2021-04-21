/**
 * Internal dependencies
 */
import AsyncSelect from './async';
import Modal from '../modal';

/**
 * WordPress dependencies
 */
import {__} from '@wordpress/i18n';
import {addQueryArgs} from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import {useState, createRef} from '@wordpress/element';
import {useSelect} from '@wordpress/data';
/**
 * External dependencies
 */
import {STORE_NAME} from '@eaccounting/data';
import {Icon } from '@wordpress/components';
import {components} from 'react-select';
import PropTypes from "prop-types";
import CategoryForm from "../forms/category-form";

function EntitySelect(props) {
	const [isModalOpen, setModalOpen] = useState(false)
	const {entity, query = {} } = props;
	const selectInputRef = createRef();
	const {endpoint, name} = useSelect((select) =>
		select(STORE_NAME).getEntity(entity)
	);
	const items = useSelect((select) =>
		select(STORE_NAME).getEntityRecords(name, query)
	);

	const fetchAPI = async (params) => {
		return await apiFetch({
			path: addQueryArgs(endpoint, {...params, ...query}),
		});
	};

	const onClick = (e) => {
		e.preventDefault();
		e.stopPropagation();
		toggleModal();
	};

	const toggleModal = () => {
		setModalOpen( !isModalOpen );
	}

	const onSubmitForm = (value) => {
		selectInputRef.current.select.select.setValue(value);
		toggleModal();
	}

	const DropdownIndicator = (props) => {
		const style = {cursor: 'pointer'};

		return (
			<>
				<components.DropdownIndicator {...props}>
					<Icon icon="arrow-down-alt2"/>
				</components.DropdownIndicator>
				<span onMouseDown={onClick} style={style}>
					<Icon icon="plus"/>
				</span>
			</>
		);
	};

	return (
		<>
			{ isModalOpen && (
				<Modal title={`Add ${name}`} onClose={ toggleModal }>
					<CategoryForm onSave={onSubmitForm} item={{type:'income'}}/>
				</Modal>
			) }
			<AsyncSelect
				innerRef={selectInputRef}
				defaultOptions={items}
				components={{DropdownIndicator}}
				loadOptions={(search) => fetchAPI({search})}
				getOptionLabel={(option) =>
					option && option.name && option.name
				}
				getOptionValue={(option) => option && option.id && option.id}
				noOptionsMessage={(input) => {
					return input.inputValue
						? __('No Results', 'wp-ever-accounting')
						: __('Type to search', 'wp-ever-accounting');
				}}
				onButtonClick={onClick}
				{...props}
			/>
		</>
	);
}

EntitySelect.propTypes = {
	entity: PropTypes.string,
	query: PropTypes.object,
};
export default EntitySelect;
