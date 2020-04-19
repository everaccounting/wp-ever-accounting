import {Component} from '@wordpress/element';
import {FormFileUpload, BaseControl, Dashicon} from '@wordpress/components';
import {__} from "@wordpress/i18n";
import PropTypes from "prop-types";
import apiFetch from "@wordpress/api-fetch";
import {concat, remove, forEach, merge} from "lodash";
import {removeObject} from "find-and";
export default class FileUpload extends Component {
	constructor(props) {
		super(props);
	}

	handleFileUpload = files => {
		forEach(files, file => {
			const data = new window.FormData();
			data.append('file', file, file.name || file.type.replace('/', '.'));
			apiFetch({
				path: '/ea/v1/files',
				body: data,
				method: 'POST',
			}).then(res => {
				const {value = []} = this.props;
				this.props.onChange(value.concat(res).filter(file => ("object" === typeof file)));
			}).catch(error => alert(error.message));
		});
	};


	onRemoveFile = file => {
		if (true === confirm(__('Do you really want to delete the file'))) {
			apiFetch({path: `/ea/v1/files/${file.id}`, method: 'DELETE'}).then(res => {
				this.props.onChange(removeObject(this.props.value, file))
			}).catch(error => alert(error.message));
		}
	};


	render() {
		const {label, accept, value: files} = this.props;

		return (
			<BaseControl label={label} className='ea-form-group'>
				{files && <ul className="ea-file-list">
					{files.map((file, index) => {
						return (
							<li key={index}>
								<a href={file.url} className="ea-file-name" title={file.name}
								   target="_blank">{file.name}</a>
								<a href="#" title={__('Delete')} className='ea-file-delete'
								   onClick={(e) => {
									   e.preventDefault();
									   this.onRemoveFile(file)
								   }}><Dashicon icon={'no-alt'}/></a>
							</li>
						)
					})}
				</ul>}

				<FormFileUpload
					className="ea-file-upload"
					accept={accept} onChange={e => {
					this.handleFileUpload(e.target.files);
				}}>
					{__('Upload')}
				</FormFileUpload>
			</BaseControl>
		)
	}

}

FileUpload.propTypes = {
	value: PropTypes.any,
	label: PropTypes.string,
	accept: PropTypes.string,
	onChange: PropTypes.func,
};

FileUpload.defaultProps = {
	label: __('Files'),
	accept: 'image/*, .pdf, .doc',
};
