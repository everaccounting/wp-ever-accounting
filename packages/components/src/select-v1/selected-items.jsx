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
 * @param root0
 * @param root0.id
 * @param root0.remove
 * @param root0.label
 */
const Tag = ( { id, remove, label } ) => {
	return (
		<div className="woocommerce-tag">
			<span className="woocommerce-tag__text">{ label }</span>
			<button
				type="button"
				className="woocommerce-tag__remove"
				onClick={ remove( id ) }
			></button>
		</div>
	);
};
export const SelectedItems = ( {
	isReadOnly,
	items,
	getOptionLabel,
	getOptionValue,
	getSelectedItemProps,
	onRemove,
} ) => {
	const classes = classnames( 'eac-select-control__selected-items', {
		'is-read-only': isReadOnly,
	} );
	if ( isReadOnly ) {
		return (
			<div className={ classes }>
				{ items
					.map( ( item ) => {
						return decodeEntities( getOptionLabel( item ) );
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
						className="eac-select-control__selected-item"
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
							id={ getOptionValue( item ) }
							remove={ () => () => onRemove( item ) }
							label={ getOptionLabel( item ) }
						/>
					</div>
				);
			} ) }
		</div>
	);
};
