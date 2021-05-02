/**
 * Internal dependencies
 */
import Selec from './async';
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
import {Icon} from '@wordpress/components';
import {components} from 'react-select';
import PropTypes from 'prop-types';

function EntitySelect(props) {
	const [isModalOpen, setModalOpen] = useState(false);
	const {entity, query = {}} = props;

	const selectInputRef = createRef();
	const route = useSelect((select) => select(STORE_NAME).getRoute(entity));
	const items = useSelect((select) => select(STORE_NAME).getEntities(entity, query));

	const fetchAPI = async (params) => {
		return await apiFetch({
			path: addQueryArgs(route.endpoint, {...params, ...query}),
		});
	};

	const onClick = (e) => {
		e.preventDefault();
		e.stopPropagation();
		toggleModal();
	};

	const toggleModal = () => {
		setModalOpen(!isModalOpen);
	};

	const onSubmitForm = (value) => {
		selectInputRef.current.select.select.setValue(value);
		toggleModal();
	};

	const SelectContainer = ({ children, ...props }) => {
		const style = {cursor: 'pointer'};
		return (
			<components.SelectContainer {...props}>
				{children}
				<span onMouseDown={onClick} style={style} className={'ea-react-select'}>
					<Icon icon="plus"/>
				</span>
			</components.SelectContainer>
		);
	};

	return (
		<>
			{isModalOpen && (
				<Modal title={`Add ${route.name}`} onClose={toggleModal}>
					<CategoryForm
						onSave={onSubmitForm}
						item={{type: 'income'}}
					/>
				</Modal>
			)}
			{route && (
				<AsyncSelect
					innerRef={selectInputRef}
					defaultOptions={items}
					components={{SelectContainer}}
					loadOptions={(search) => fetchAPI({search})}
					getOptionLabel={(option) =>
						option && option.name && option.name
					}
					getOptionValue={(option) =>
						option && option.id && option.id
					}
					noOptionsMessage={(input) => {
						return input.inputValue
							? __('No Results', 'wp-ever-accounting')
							: __('Type to search', 'wp-ever-accounting');
					}}
					onButtonClick={onClick}
					{...props}
				/>
			)}
		</>
	);
}

EntitySelect.propTypes = {
	entity: PropTypes.string,
	query: PropTypes.object,
};
export default EntitySelect;
