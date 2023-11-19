/**
 * External dependencies
 */
import { SectionHeader, Drawer, getPortal, Button } from '@eac/components';
/**
 * WordPress dependencies
 */
import { createPortal, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Animate, Notice, Modal } from '@wordpress/components';

const MyModal = () => {
	return (
		<>
			<Modal title="This is my modal">
				Modal 2
			</Modal>
		</>
	);
};

function Help() {
	const [ isOpen, setOpen ] = useState( false );
	const openModal = () => setOpen( true );
	const closeModal = () => setOpen( false );
	return (
		<>
			<SectionHeader title={ __( 'Help', 'wp-ever-accounting' ) } />
			<Animate type="loading" options={ { origin: 'top' } }>
				{ ( { className } ) => (
					<Notice className={ className } status="success">
						<p>Animation finished.</p>
					</Notice>
				) }
			</Animate>
			<Modal title="This is my modal" onRequestClose={ closeModal }>
				{ isOpen && <MyModal /> }
				My modal 1
				<Button variant="secondary" onClick={ openModal }>
					My custom close button
				</Button>
			</Modal>
		</>
	);
}

export default Help;
