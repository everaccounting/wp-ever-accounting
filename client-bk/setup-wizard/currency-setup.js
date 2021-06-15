/**
 * External dependencies
 */
import { useEntity, useSettings } from '@eaccounting/data';
import { Table, Loading, CurrencyModal } from '@eaccounting/components';
/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';
import { Button, DropdownMenu, Icon } from '@wordpress/components';
/**
 * WordPress dependencies
 */

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const CurrencyRate = (currency) => {
	return currency.rate;
};

export default function Introduction() {
	const [isModalOpen, setModalOpen] = useState(false);
	const { entities, isLoading, deleteEntity } = useEntity({
		name: 'currencies',
	});
	const toggleModal = () => {
		setModalOpen(!isModalOpen);
	};

	return (
		<>
			<p>
				{__(
					'Default currency rate should be always 1 & additional currency rates should be equivalent of default currency. e.g. If USD is your default currency then USD rate is 1 & GBP rate will be 0.77',
					'wp-ever-accounting'
				)}
			</p>

			<div
				style={{
					float: 'right',
					marginBottom: '20px',
				}}
			>
				<Button isPrimary={true} onClick={toggleModal}>
					{__('Add Currency')}
				</Button>
			</div>

			{isModalOpen && (
				<CurrencyModal
					title={__('Add Currency')}
					onSave={() => setModalOpen(!isModalOpen)}
					onClose={() => setModalOpen(!isModalOpen)}
				/>
			)}

			<Loading loading={isLoading}>
				<Table
					columns={[
						{
							label: __('Name'),
							property: 'name',
						},
						{
							label: __('Code'),
							property: 'code',
						},
						{
							label: __('Rate'),
							property: 'rate',
							render: CurrencyRate,
						},
						{
							label: __('Action'),
							property: 'action',
							width: 60,
							render: (row) => {
								return (
									<>
										<Icon
											style={{ cursor: 'pointer' }}
											icon="edit-page"
										/>
										<Icon
											style={{ cursor: 'pointer' }}
											icon="trash"
											onClick={() =>
												deleteEntity(row.code)
											}
										/>
									</>
								);
							},
						},
					]}
					data={entities}
				/>
			</Loading>
		</>
	);
}
