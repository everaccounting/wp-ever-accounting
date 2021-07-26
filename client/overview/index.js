/**
 * External dependencies
 */
// eslint-disable-next-line no-unused-vars
/**
 * Internal dependencies
 */
import { EntitySelect } from '@eaccounting/components';
import { getQuery } from '@eaccounting/navigation';
/**
 * WordPress dependencies
 */
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
/**
 * WordPress dependencies
 */

const autoConfigs = [
	{
		name: 'Autocomplete',
		// The prefix that triggers this completer
		triggerPrefix: '',
		// The option data
		options: [
			{ value: '🍎', label: 'Apple', id: 1 },
			{ value: '🍊', label: 'Orange', id: 2 },
			{ value: '🍇', label: 'Grapes', id: 3 },
		],
		// Returns a label for an option like "🍊 Orange"
		getOptionLabel: ( option ) => (
			<span>
				<span className="icon">{ option.value }</span> { option.label }
			</span>
		),
		// Declares that options should be matched by their name or value
		getOptionKeywords: ( option ) => [ option.label, option.value ],
		// Declares that the Grapes option is disabled
		getOptionCompletion: ( option ) => (
			<abbr title={ option.label }>{ option.value }</abbr>
		),
	},
];

export default function Overview() {
	const { account_id } = getQuery();
	console.log( account_id );
	return (
		<>
			<EntitySelect
				entityName={ 'items' }
				label={ 'Items' }
				creatable={ true }
			/>
			<RichText
				autocompleters={ autoConfigs }
				aria-label={ __( 'Button text' ) }
				placeholder={ __( 'Add button text…' ) }
				withoutInteractiveFormatting
			/>
		</>
	);
}
