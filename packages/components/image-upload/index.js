/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { Component, Fragment } from '@wordpress/element';
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import classNames from 'classnames';
import { Icon, upload } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import './style.scss';

class ImageUpload extends Component {
	constructor() {
		super(...arguments);
		this.state = {
			frame: false,
		};
		this.openModal = this.openModal.bind(this);
		this.handleImageSelect = this.handleImageSelect.bind(this);
		this.removeImage = this.removeImage.bind(this);
	}

	openModal() {
		if (this.state.frame) {
			this.state.frame.open();
			return;
		}

		const frame = wp.media({
			title: __('Select or upload image'),
			button: {
				text: __('Select'),
			},
			library: {
				type: 'image',
			},
			multiple: false,
			custom: 'custom',
		});

		frame.on('select', this.handleImageSelect);
		frame.on('ready', () => {
			frame.uploader.options.uploader.params = {
				type: 'eaccounting_file',
			};
		});
		frame.open();

		this.setState({ frame });
	}

	handleImageSelect() {
		const { onChange } = this.props;
		const attachment = this.state.frame
			.state()
			.get('selection')
			.first()
			.toJSON();
		onChange(attachment);
	}

	removeImage() {
		const { onChange } = this.props;
		onChange(null);
	}

	render() {
		const { className, image } = this.props;
		return (
			<Fragment>
				{!!image && (
					<div
						className={classNames(
							'eaccounting-image-upload',
							'has-image',
							className
						)}
					>
						<div className="eaccounting-image-upload__image-preview">
							<img src={image.url} alt="" />
						</div>
						<Button
							isSecondary
							className="eaccounting-image-upload__remove-image"
							onClick={this.removeImage}
						>
							{__('Remove image', 'woocommerce-admin')}
						</Button>
					</div>
				)}
				{!image && (
					<div
						className={classNames(
							'eaccounting-image-upload',
							'no-image',
							className
						)}
					>
						<Button
							className="eaccounting-image-upload__add-image"
							onClick={this.openModal}
							isSecondary
						>
							<Icon icon={upload} />
							{__('Add an image', 'woocommerce-admin')}
						</Button>
					</div>
				)}
			</Fragment>
		);
	}
}

export default ImageUpload;
