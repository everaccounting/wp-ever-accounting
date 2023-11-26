/**
 * External dependencies
 */
import { SectionHeader, Drawer, AnimatePresence, Motion, Button, Input } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

function Tools() {
	const [ isOpen1, setOpen1 ] = useState( false );
	const [ isOpen2, setOpen2 ] = useState( false );
	return (
		<>
			<SectionHeader title={ __( 'Tools', 'wp-ever-accounting' ) } />
			<Button onClick={ () => setOpen1( ! isOpen1 ) }>Open 1</Button>

			{ isOpen1 && (
				<Drawer
					title={ __( 'Drawer 1', 'wp-ever-accounting' ) }
					onClose={ () => setOpen1( false ) }
				>
					DRAWER 1<Button onClick={ () => setOpen2( ! isOpen2 ) }>Open 2</Button>
				</Drawer>
			) }
			{ isOpen2 && (
				<Drawer
					title={ __( 'Drawer 2', 'wp-ever-accounting' ) }
					onClose={ () => setOpen2( false ) }
				>
					DRAWER 2
				</Drawer>
			) }
		</>
	);
}

export default Tools;
