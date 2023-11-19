/**
 * External dependencies
 */
import { SectionHeader, Drawer, AnimatePresence, Motion, Button } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

function Tools() {
	const [ isOpen, setOpen ] = useState( false );
	return (
		<>
			<SectionHeader title={ __( 'Tools', 'wp-ever-accounting' ) } />
			<Button onClick={ () => setOpen( ! isOpen ) }>Open</Button>
			{ isOpen && (
				<AnimatePresence>
					<Motion.div
						key="modal"
						initial={ { opacity: 0 } }
						animate={ { opacity: 1 } }
						exit={ { opacity: 0 } }
					>
						Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci, atque!
					</Motion.div>
				</AnimatePresence>
			) }
		</>
	);
}

export default Tools;
