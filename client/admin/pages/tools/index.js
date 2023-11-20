/**
 * External dependencies
 */
import { SectionHeader, Drawer, AnimatePresence, Motion, Button, Input } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

function AnotherDrawer() {
	return (
		<Drawer title={ __( 'Another Drawer', 'wp-ever-accounting' ) } onClose={ () => {} }>
			<p>Some content</p>
		</Drawer>
	);
}

function Tools() {
	const [ isOpen, setOpen ] = useState( true );
	return (
		<>
			<SectionHeader title={ __( 'Tools', 'wp-ever-accounting' ) } />
			<Button onClick={ () => setOpen( ! isOpen ) }>Open</Button>
			{ isOpen && (
				<Drawer
					title={ __( 'Drawer Title', 'wp-ever-accounting' ) }
					onClose={ () => setOpen( false ) }
				>
					<AnotherDrawer />
					<Input label={ __( 'Input Label', 'wp-ever-accounting' ) } />
				</Drawer>
			) }
		</>
	);
}

export default Tools;
