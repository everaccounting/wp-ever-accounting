/**
 * WordPress dependencies
 */
import { applyFilters } from '@wordpress/hooks';
import { lazy } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const Introduction = lazy(() => import('./introduction'));
const CompanySetup = lazy(() => import('./company-setup'));
const CurrencySetup = lazy(() => import('./currency-setup'));
const FinishSetup = lazy(() => import('./finish-setup'));

const getSteps = () =>
	applyFilters('eaccounting_setup_steps', [
		{
			key: 'introduction',
			title: 'Introduction',
			container: Introduction,
		},
		{
			key: 'company_setup',
			title: __('Company Setup'),
			container: CompanySetup,
		},
		{
			key: 'currency_setup',
			title: __('Currency Setup'),
			container: CurrencySetup,
		},
		{
			key: 'finish_setup',
			title: __('Finish Setup'),
			container: FinishSetup,
		},
	]);

export default getSteps;
