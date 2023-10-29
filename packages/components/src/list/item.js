/**
 * WordPress dependencies
 */
import { forwardRef, Children, useContext } from '@wordpress/element';
/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * Internal dependencies
 */

const Item = forwardRef( ( { className, actions, extra, style, children, ...props }, ref ) => {
	const actionsContent = actions && actions.length > 0 && (
		<ul className="eac-list__actions">
			{ actions.map( ( action, index ) => (
				<li key={ `list-item-action-${ index }` }>
					{ action }
					{ index !== actions.length - 1 && <em className="eac-list__actions-split" /> }
				</li>
			) ) }
		</ul>
	);

	const classes = classNames( 'eac-list__item', className );
	return (
		<div className={ classes } style={ style } ref={ ref } { ...props }>
			{ children }
			{ actionsContent }
			{ extra }
		</div>
	);
} );

function Meta( { className, avatar, title, description, ...props } ) {
	const classes = classNames( 'eac-list__meta', className );
	return (
		<div className={ classes } { ...props }>
			{ avatar && <div className="eac-list__meta-avatar">{ avatar }</div> }
			{ title || description ? (
				<div className="eac-list__meta-content">
					{ title && <h4 className="eac-list__meta-title">{ title }</h4> }
					{ description && <div className="eac-list__meta-description">{ description }</div> }
				</div>
			) : null }
		</div>
	);
}

Item.Meta = Meta;
export default Item;
