/**
 * WordPress dependencies
 */
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import { Button, Flex, FlexItem } from '@wordpress/components';
import { withState } from '@wordpress/compose';
/**
 * External dependencies
 */
import {
	Card,
	EntitySelect,
	Modal,
	Form,
	TextControl,
	PriceControl,
} from '@eaccounting/components';
import { useEntity, useSettings } from '@eaccounting/data';
import { __ } from '@wordpress/i18n';
/**
 * Internal dependencies
 */
import CurrencyModal from './forms/currency';

const CustomerModal = () =>
	withState({
		isOpen: false,
		item: {},
	})(({ isOpen, item, setState }) => {
		const { saveEntity } = useEntity({ name: 'customers' });
		const { defaultAccount, defaultCurrency } = useSettings();
		const toggleOpen = () => {
			setState((state) => ({ isOpen: !state.isOpen }));
		};

		const onSubmit = async (item) => {
			const res = await saveEntity(item);
			if (res && res.id) {
				toggleOpen();
			}
		};
		// console.log(defaultCurrency);
		// const currency = getSettings('default_currency');
		return (
			<>
				<Button isSecondary onClick={toggleOpen}>
					Create contact
				</Button>
				<PriceControl />
				{isOpen && (
					<Modal title={__('Add Customer')} onClose={toggleOpen}>
						<Form
							onSubmitCallback={onSubmit}
							initialValues={{
								...item,
								currency: defaultCurrency,
							}}
						>
							{({
								getInputProps,
								errors,
								values,
								handleSubmit,
							}) => (
								<>
									{console.log(values)}
									<TextControl
										label={__('Name')}
										{...getInputProps('name')}
									/>
									<TextControl
										label={__('Email')}
										type="email"
										{...getInputProps('email')}
									/>
									<EntitySelect
										label={__('Currency')}
										entity={'currencies'}
										{...getInputProps('currency')}
									/>
									<PriceControl
										code={
											values &&
											values.currency &&
											values.currency.code
										}
										label={'Amount'}
									/>
									<TextControl
										label={__('Street')}
										{...getInputProps('street')}
									/>
									<TextControl
										label={__('City')}
										{...getInputProps('city')}
									/>
									<TextControl
										label={__('State')}
										{...getInputProps('state')}
									/>
									<TextControl
										label={__('Postcode')}
										{...getInputProps('postcode')}
									/>
									<Button
										isPrimary
										onClick={handleSubmit}
										disabled={Object.keys(errors).length}
									>
										Submit
									</Button>
								</>
							)}
						</Form>
					</Modal>
				)}
			</>
		);
	});
/**
 * Internal dependencies
 */
function App() {
	return (
		<>
			<CurrencyModal />
		</>
	);
}

domReady(() => {
	const root = document.getElementById('ea-react');
	return render(<App />, root);
});
