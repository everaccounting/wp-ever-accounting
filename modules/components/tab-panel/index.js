/**
 * WordPress dependencies
 */
import { TabPanel as Base } from '@wordpress/components';
/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import './style.scss';

export default function TabPanel( props ) {
	const { ClassName, tabs, ...restProps } = props;
	return (
		<Base
			className={ classNames( ClassName, 'eaccounting_tab_panel' ) }
			tabs={ tabs }
			{ ...restProps }
		>
			{ ( tab ) => (
				<div style={ { marginBottom: '20px' } }>
					{ tab.render( tab ) }
				</div>
			) }
		</Base>
	);
}

TabPanel.propTypes = {
	className: PropTypes.func,
	orientation: PropTypes.string,
	onSelect: PropTypes.string,
	tabs: PropTypes.arrayOf(
		PropTypes.shape( {
			name: PropTypes.string,
			title: PropTypes.string,
			className: PropTypes.string,
			render: PropTypes.func,
		} )
	),
	activeClass: PropTypes.string,
	initialTabName: PropTypes.string,
	children: PropTypes.node,
};
