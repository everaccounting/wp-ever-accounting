/**
 * WordPress dependencies
 */
import { applyFilters } from '@wordpress/hooks';
import { lazy } from '@wordpress/element';

const Introduction = lazy(() => import('./introduction'));
const CompanySetup = lazy(() => import('./company-setup'));
const CurrencySetup = lazy(() => import('./currency-setup'));
const FinishSetup = lazy(() => import('./finish-setup'));

const routes = applyFilters('eaccounting_setup_steps', [
	{
		path: 'introduction',
		title: 'Introduction',
		container: Introduction,
	},
	{
		path: 'company_setup',
		title: 'Company Setup',
		container: CompanySetup,
	},
	{
		path: 'currency_setup',
		title: 'Currency Setup',
		container: CurrencySetup,
	},
	{
		path: 'finish_setup',
		title: 'Finish Setup',
		container: FinishSetup,
	},
]);

export default routes;
