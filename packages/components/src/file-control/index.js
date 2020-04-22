/**
 * WordPress dependencies
 */
import { Component, Fragment } from '@wordpress/element';
/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { BaseControl, FormFileUpload, Dashicon } from '@wordpress/components';
import classnames from 'classnames';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { isEmpty } from 'lodash';

export default class FileControl extends Component {
	handleFileUpload = files => {
		const file = files[0];
		const data = new window.FormData();

		data.append('file', file, file.name || file.type.replace('/', '.'));
		apiFetch({
			path: '/ea/v1/files',
			body: data,
			method: 'POST',
		})
			.then(res => {
				this.props.onChange(res);
			})
			.catch(error => alert(error.message));
	};

	removeFile = file => {
		if (confirm(__('Do you really want to delete the file')) === true) {
			apiFetch({ path: `/ea/v1/files/${file.id}`, method: 'DELETE' })
				.then(res => {
					this.props.onChange({});
				})
				.catch(error => alert(error.message));
		}
	};

	render() {
		const { label, help, className, required, value, preview = true, accept = 'image/*', ...props } = this.props;
		const classes = classnames('ea-form-group', 'ea-file-field', className, {
			required: !!required,
		});

		return (
			<BaseControl label={label} help={help} className={classes}>
				<div className="ea-input-group">
					{!isEmpty(value.url) && (
						<Fragment>
							{preview ? (
								<Fragment>
									<a href={value.url} target="_blank" className="ea-file-link">
										<div className="ea-file-image-preview" style={{ backgroundImage: `url("${value.url}")` }} />
									</a>
									<span onClick={() => this.removeFile(value)} className="ea-file-remove">
										{__(`Remove ${label}`)}
									</span>
								</Fragment>
							) : (
								<span className="ea-file-preview">
									<a href={value.url} target="_blank" className="ea-file-link">
										{value.name}
									</a>
									<a
										href="#"
										title={__('Delete')}
										className="ea-file-delete"
										onClick={e => {
											e.preventDefault();
											this.removeFile(value);
										}}
									>
										<Dashicon icon={'no-alt'} />
									</a>
								</span>
							)}
						</Fragment>
					)}

					{isEmpty(value.url) && (
						<FormFileUpload
							className="ea-file-upload"
							accept={accept}
							onChange={e => {
								this.handleFileUpload(e.target.files);
							}}
						>
							{__('Upload')}
						</FormFileUpload>
					)}
				</div>
			</BaseControl>
		);
	}
}

FileControl.propTypes = {
	label: PropTypes.string,
	help: PropTypes.string,
	value: PropTypes.any,
	className: PropTypes.string,
	onChange: PropTypes.func,
	required: PropTypes.bool,
	preview: PropTypes.bool,
};
