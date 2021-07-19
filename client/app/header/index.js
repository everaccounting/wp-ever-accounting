/**
 * External dependencies
 */
import { useIsScrolled, Text } from '@eaccounting/components';
import classnames from 'classnames';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Icon } from '@wordpress/components';
import { useRef, useLayoutEffect } from '@wordpress/element';
/**
 * Internal dependencies
 */
// import NavigationPanel from './navigation-panel';
import Navigation from './navigation';
import './style.scss';

// eslint-disable-next-line no-unused-vars
export default function Header( { isEmbedded = false, query } ) {
	const headerElement = useRef( null );
	const isScrolled = useIsScrolled();
	let debounceTimer = null;
	const className = classnames( 'eaccounting-layout__header', {
		'is-scrolled': isScrolled,
	} );

	/*eslint-disable @wordpress/no-global-event-listener, react-hooks/exhaustive-deps */
	useLayoutEffect( () => {
		updateBodyMargin();
		window.addEventListener( 'resize', updateBodyMargin );
		return () => {
			window.removeEventListener( 'resize', updateBodyMargin );
			const wpBody = document.querySelector( '#wpbody' );

			if ( ! wpBody ) {
				return;
			}

			wpBody.style.marginTop = null;
		};
	}, [] );
	/* eslint-enable @wordpress/no-global-event-listener, react-hooks/exhaustive-deps */

	const updateBodyMargin = () => {
		clearTimeout( debounceTimer );
		debounceTimer = setTimeout( function () {
			const wpBody = document.querySelector( '#wpbody' );

			if ( ! wpBody || ! headerElement.current ) {
				return;
			}

			wpBody.style.marginTop = `${ headerElement.current.offsetHeight }px`;
		}, 200 );
	};

	const getMenuItems = () => {
		const items = [];
		items.push( {
			id: 'new',
			title: __( 'New' ),
			parent: 'new',
			group: '',
			capability: '',
			url: '',
			order: '',
			icon: <Icon icon="admin-users" />,
		} );
		items.push( {
			id: 'invoice',
			title: __( 'Invoice' ),
			parent: 'new',
			group: 'Income',
			capability: '',
			url: '',
			order: '',
			icon: <Icon icon="admin-users" />,
		} );
		items.push( {
			id: 'customer',
			title: __( 'Customer' ),
			parent: 'new',
			group: 'Income',
			capability: 'manage_options',
			url: '',
			order: '',
			icon: <Icon icon="admin-users" />,
		} );
		items.push( {
			id: 'bill',
			title: __( 'Bill' ),
			parent: 'new',
			group: 'Expense',
			capability: '',
			url: '',
			order: '',
			icon: <Icon icon="admin-users" />,
		} );
		items.push( {
			id: 'vendor',
			title: __( 'Vendor' ),
			parent: 'new',
			group: 'Expense',
			capability: '',
			url: '',
			order: 1,
			icon: <Icon icon="admin-users" />,
		} );
		items.push( {
			id: 'payment',
			title: __( 'Payment' ),
			parent: 'new',
		} );

		items.push( {
			id: 'help',
			title: __( 'Payment' ),
			parent: 'help',
			icon: <Icon icon="admin-users" />,
		} );

		return items;
	};

	return (
		<div className={ className } ref={ headerElement }>
			<div className="eaccounting-layout__header-wrapper">
				<Text
					className={ `eaccounting-layout__header-heading` }
					as="h1"
					variant="subtitle.small"
				>
					{ __( 'Accounting' ) }
				</Text>
				<Navigation menuItems={ getMenuItems() } />
			</div>
		</div>
	);
}
