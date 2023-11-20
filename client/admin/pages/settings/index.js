/**
 * External dependencies
 */
import { SectionHeader } from '@eac/components';
import { NavLink } from 'react-router-dom';
/**
 * WordPress dependencies
 */
import { Icon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './style.scss';
import { getRoutes, Routes } from './routes';
function Settings() {
	const routes = getRoutes();
	return (
		<>
			<SectionHeader
				title={ __( 'Settings', 'wp-ever-accounting' ) }
				style={ { marginBottom: '20px' } }
			/>

			<div className="eac-settings">
				<div className="eac-settings__nav">
					{ routes.map( ( route, index ) => {
						return (
							<NavLink
								key={ route.path || index }
								to={ route.path }
								exact={ route.exact }
							>
								<Icon
									icon={ route.icon || 'admin-generic' }
									className="eac-settings__nav__icon"
								/>
								<span>{ route.name }</span>
							</NavLink>
						);
					} ) }
				</div>
				<div className="eac-settings__content">
					<Routes />
				</div>
			</div>
		</>
	);
}

export default Settings;
