/**
 * External dependencies
 */
import classnames from 'classnames';
/**
 * WordPress dependencies
 */
import { decodeEntities } from '@wordpress/html-entities';
/**
 * Internal dependencies
 */
const Tag = ( { id, remove, label } ) => {
	return (
		<div className="woocommerce-experimental-select-control__selected-item-tag">
			<span className="woocommerce-experimental-select-control__selected-item-tag-label">
				{ label }
			</span>
			<button
				type="button"
				className="woocommerce-experimental-select-control__selected-item-tag-remove"
				onClick={ remove( id ) }
			></button>
		</div>
	);
};
export const SelectedItems = ( {
	isReadOnly,
	items,
	getItemLabel,
	getItemValue,
	getSelectedItemProps,
	onRemove,
} ) => {
	const classes = classnames( 'woocommerce-experimental-select-control__selected-items', {
		'is-read-only': isReadOnly,
	} );
	if ( isReadOnly ) {
		return (
			<div className={ classes }>
				{ items
					.map( ( item ) => {
						return decodeEntities( getItemLabel( item ) );
					} )
					.join( ', ' ) }
			</div>
		);
	}
	return (
		<div className={ classes }>
			{ items.map( ( item, index ) => {
				return (
					// Disable reason: We prevent the default action to keep the input focused on click.
					// Keyboard users are unaffected by this change.
					/* eslint-disable jsx-a11y/no-static-element-interactions, jsx-a11y/click-events-have-key-events */
					<div
						key={ `selected-item-${ index }` }
						className="woocommerce-experimental-select-control__selected-item"
						{ ...getSelectedItemProps( {
							selectedItem: item,
							index,
						} ) }
						onMouseDown={ ( event ) => {
							event.preventDefault();
						} }
						onClick={ ( event ) => {
							event.preventDefault();
						} }
					>
						{ /* eslint-disable-next-line @typescript-eslint/ban-ts-comment */ }
						{ /* @ts-ignore Additional props are not required. */ }
						<Tag
							id={ getItemValue( item ) }
							remove={ () => () => onRemove( item ) }
							label={ getItemLabel( item ) }
						/>
					</div>
				);
			} ) }
		</div>
	);
};
