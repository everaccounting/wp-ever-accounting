/**
 * External dependencies
 */
import classnames from 'classnames';
import { Component } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import PropTypes from 'prop-types';
import { SnackbarList } from '@wordpress/components';
import { withDispatch, withSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import './style.scss';

class Notices extends Component {
	render() {
		const { className, notices, onRemove } = this.props;
		const classes = classnames(
			'woocommerce-transient-notices',
			'components-notices__snackbar',
			className
		);
		console.log(notices);

		return (
			<SnackbarList
				notices={ notices }
				className={ classes }
				onRemove={ onRemove }
			/>
		);
	}
}

Notices.propTypes = {
	/**
	 * Additional class name to style the component.
	 */
	className: PropTypes.string,
	/**
	 * Array of notices to be displayed.
	 */
	notices: PropTypes.array,
};

export default compose(
	withSelect( ( select ) => {
		const notices = select( 'core/notices' ).getNotices();
		return { notices };
	} ),
	withDispatch( ( dispatch ) => ( {
		onRemove: dispatch( 'core/notices' ).removeNotice,
	} ) )
)( Notices );
