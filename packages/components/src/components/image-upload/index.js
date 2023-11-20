/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { createElement, Component, Fragment } from '@wordpress/element';
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import classNames from 'classnames';
// eslint-disable-next-line import/no-extraneous-dependencies
import { Icon, upload } from '@wordpress/icons';
const ImageUpload = ( props ) => {
	const [ frame, setFrame ] = useState( null );

	const openModal = () => {
		if ( frame ) {
			frame.open();
			return;
		}

		const newFrame = wp.media( {
			title: __( 'Select or upload image', 'woocommerce' ),
			button: {
				text: __( 'Select', 'woocommerce' ),
			},
			library: {
				type: 'image',
			},
			multiple: false,
		} );

		newFrame.on( 'select', handleImageSelect );
		newFrame.open();

		setFrame( newFrame );
	};

	const handleImageSelect = () => {
		const { onChange } = props;
		const attachment = frame.state().get( 'selection' ).first().toJSON();
		onChange( attachment );
	};

	const removeImage = () => {
		const { onChange } = props;
		onChange( null );
	};

	const { className, image } = props;

	return (
		<>
			{ !! image && (
				<div
					className={ classNames(
						'woocommerce-image-upload',
						'has-image',
						className
					) }
				>
					<div className="woocommerce-image-upload__image-preview">
						<img src={ image.url } alt="" />
					</div>
					<Button
						isSecondary
						className="woocommerce-image-upload__remove-image"
						onClick={ removeImage }
					>
						{ __( 'Remove image', 'woocommerce' ) }
					</Button>
				</div>
			) }
			{ ! image && (
				<div
					className={ classNames(
						'woocommerce-image-upload',
						'no-image',
						className
					) }
				>
					<Button
						className="woocommerce-image-upload__add-image"
						onClick={ openModal }
						isSecondary
					>
						<Icon icon={ upload } />
						{ __( 'Add an image', 'woocommerce' ) }
					</Button>
				</div>
			) }
		</>
	);
};

export default ImageUpload;
