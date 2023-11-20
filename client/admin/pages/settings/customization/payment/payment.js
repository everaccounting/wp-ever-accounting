/**
 * External dependencies
 */
import { Card } from '@eac/components';
import { navigate, getQuery } from '@eac/navigation';
/**
 * WordPress dependencies
 */
import { lazy } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const Settings = lazy( () => import( './settings' ) );

function Payment () {
    return (
            <Settings />

    );
}

export default Payment;

