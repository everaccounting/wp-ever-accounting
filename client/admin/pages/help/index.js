/**
 * External dependencies
 */
import { SectionHeader } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function Help() {
    return (
        <>
            <SectionHeader title={__('Help', 'wp-ever-accounting')} />
        </>
    );
}

export default Help;
