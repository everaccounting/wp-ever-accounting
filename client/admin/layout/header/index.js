/**
 * WordPress dependencies
 */
import { Icon, Slot } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useContext } from '@wordpress/element';
/**
 * External dependencies
 */
import { Link } from 'react-router-dom';
/**
 * Internal dependencies
 */
import logo from './logo';
import './style.scss';

const SubHeader = () => {
	return (
		<Slot name="sub-header">
			{ ( fills ) => {
				return fills.length > 0 ? (
					<div className="eac-sub-header">
						<div className="eac-layout__wrapper">{ fills }</div>
					</div>
				) : null;
			} }
		</Slot>
	);
};

function Header( props ) {
	return (
		<div className="eac-layout__header">
			<div className="eac-main-header">
				<div className="eac-layout__wrapper">
					<div className="header__logo">
						<Icon icon={ logo } size={ 32 } />
					</div>
					<div className="header__title">Ever Accounting</div>
					<ul className="header__menu">
						<li className="header__menu-item">
							<Link to="/">
								<Icon className="header__menu-icon" icon="dashboard" size={ 16 } />
								{ __( 'Dashboard', 'wp-ever-accounting' ) }
							</Link>
						</li>
						<li className="header__menu-item">
							<Link to="/settings">
								<Icon className="header__menu-icon" icon="admin-settings" size={ 16 } />
								{ __( 'Settings', 'wp-ever-accounting' ) }
							</Link>
						</li>
						<li className="header__menu-item">
							<Link to="/help">
								<Icon className="header__menu-icon" icon="editor-help" size={ 16 } />
								{ __( 'Help', 'wp-ever-accounting' ) }
							</Link>
						</li>
					</ul>
				</div>
			</div>
			<SubHeader />
		</div>
	);
}

export default Header;
