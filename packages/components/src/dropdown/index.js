/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import classnames from 'classnames';
/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';
import { createElement, useState } from '@wordpress/element';
import { decodeEntities } from '@wordpress/html-entities';

/**
 * Internal dependencies
 */
import { useMergedState } from '../hooks';

function Dropdown( { className, ...props } ) {
	const [ isOpen, setIsOpen ] = useMergedState( props.isOpen || false );
	const classes = classnames( 'eac-dropdown', props.className, {
		'eac-dropdown--open': isOpen,
	} );
	const dispatchEvent = ( name, ...args ) => {
		const fn = props[ name ];
		if ( fn ) {
			fn( ...args );
		}
	};
}

export default Dropdown;
