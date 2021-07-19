/**
 * WordPress dependencies
 */
import { Fragment } from '@wordpress/element';
import { SelectControl } from '@wordpress/components';
/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import interpolateComponents from 'interpolate-components';

/**
 * External dependencies
 */
import classnames from 'classnames';
import { partial, castArray, find, isEmpty } from 'lodash';
/**
 * Internal dependencies
 */
import EntitySelect from '../entity-select';
import { textContent } from './utils';

function EntityFilter( props ) {
	const { className, config, filter, onFilterChange } = props;
	const { title, mixedString, rules = [], input } = config;
	const { transform = ( x ) => x.id, entityName, ...entityProps } = input;
	let entity_id = [];
	if ( ! isEmpty( filter.value ) && filter.value.length ) {
		entity_id = castArray( filter.value.split( ',' ) );
	}

	const onChange = ( values ) => {
		const idList = ( Array.isArray( values ) ? values : [ values ] )
			.map( transform )
			.join( ',' );
		onFilterChange( 'value', idList );
	};

	const getScreenReaderText = ( filter, config ) => {
		if ( entity_id.length === 0 ) {
			return '';
		}

		const rule = find( config.rules, { value: filter.rule } ) || {};
		// const filterStr = selected.map( ( item ) => item.label ).join( ', ' );
		const filterStr = '';

		return textContent(
			interpolateComponents( {
				mixedString: title,
				components: {
					filter: <Fragment>{ filterStr }</Fragment>,
					rule: <Fragment>{ rule.label }</Fragment>,
					title: <Fragment />,
				},
			} )
		);
	};

	const children = interpolateComponents( {
		mixedString,
		components: {
			title: (
				<span
					className={ classnames(
						className,
						'ea-advanced-filters__label'
					) }
				/>
			),
			rule: (
				<div
					className={ classnames(
						className,
						'ea-advanced-filters__rule',
						{
							'display--none': isEmpty( rules ),
						}
					) }
				>
					<SelectControl
						options={ rules }
						value={ filter.rule }
						onChange={ partial( onFilterChange, 'rule' ) }
						aria-label={ filter.rule }
					/>
				</div>
			),
			filter: (
				<div
					className={ classnames(
						className,
						'ea-advanced-filters__input'
					) }
				>
					<EntitySelect
						entityName={ entityName }
						onChange={ onChange }
						aria-label={ title }
						entity_id={ entity_id }
						{ ...entityProps }
					/>
				</div>
			),
		},
	} );

	const screenReaderText = getScreenReaderText( filter, config );

	return (
		<>
			<fieldset className="ea-advanced-filters__line-item" tabIndex="0">
				<legend className="screen-reader-text">{ title }</legend>
				<div className="ea-advanced-filters__fieldset">
					{ children }
				</div>
				{ screenReaderText && (
					<span className="screen-reader-text">
						{ screenReaderText }
					</span>
				) }
			</fieldset>
		</>
	);
}

EntityFilter.propTypes = {
	// The configuration object for the single filter to be rendered.
	config: PropTypes.shape( {
		labels: PropTypes.shape( {
			rule: PropTypes.string,
			title: PropTypes.string,
			filter: PropTypes.string,
		} ),
		rules: PropTypes.arrayOf( PropTypes.object ),
		input: PropTypes.object,
	} ).isRequired,
	// The activeFilter handed down by AdvancedFilters.
	filter: PropTypes.shape( {
		key: PropTypes.string,
		rule: PropTypes.string,
		value: PropTypes.string,
	} ).isRequired,
	// Function to be called on update.
	onFilterChange: PropTypes.func.isRequired,
};

export default EntityFilter;
