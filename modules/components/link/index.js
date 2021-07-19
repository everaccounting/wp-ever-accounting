/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { partial } from 'lodash';
import { getHistory } from '@eaccounting/navigation';

/**
 * Use `Link` to create a link to another resource. It accepts a type to automatically
 * create eaccounting links, eaccounting links, and external links.
 */

function Link( { children, href, type, ...props } ) {
	// @todo Investigate further if we can use <Link /> directly.
	// With React Router 5+, <RouterLink /> cannot be used outside of the main <Router /> elements,
	// which seems to include components imported from @eaccounting/components. For now, we can use the history object directly.
	const LinkHandler = ( onClick, event ) => {
		// If cmd, ctrl, alt, or shift are used, use default behavior to allow opening in a new tab.
		if (
			event.ctrlKey ||
			event.metaKey ||
			event.altKey ||
			event.shiftKey
		) {
			return;
		}

		event.preventDefault();

		// If there is an onclick event, execute it.
		const onClickResult = onClick ? onClick( event ) : true;

		// Mimic browser behavior and only continue if onClickResult is not explicitly false.
		if ( onClickResult === false ) {
			return;
		}

		getHistory().push( event.target.closest( 'a' ).getAttribute( 'href' ) );
	};

	const passProps = {
		...props,
		'data-link-type': type,
	};

	if ( type === 'eaccounting' ) {
		passProps.onClick = partial( LinkHandler, passProps.onClick );
	}

	return (
		<a href={ href } { ...passProps }>
			{ children }
		</a>
	);
}

Link.propTypes = {
	/**
	 * The resource to link to.
	 */
	href: PropTypes.string.isRequired,
	/**
	 * Type of link. For eaccounting and eaccounting, the correct prefix is appended.
	 */
	type: PropTypes.oneOf( [ 'eaccounting', 'external' ] ).isRequired,
};

Link.defaultProps = {
	type: 'eaccounting',
};

Link.contextTypes = {
	router: PropTypes.object,
};

export default Link;
