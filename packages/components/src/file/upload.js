import {Component} from '@wordpress/element';
import {FormFileUpload} from '@wordpress/components';
import {__} from "@wordpress/i18n";
import PropTypes from "prop-types";
import apiFetch from "@wordpress/api-fetch";

export default class FileUpload extends Component {
	onChangeFiles = files => {
		const file = files[0];
		const data = new window.FormData();
		data.append('file', file, file.name || file.type.replace('/', '.'));
		apiFetch({
			path: '/ea/v1/files',
			body: data,
			method: 'POST',
		}).then(res => {
			this.props.onAdd && this.props.onAdd(res);
		}).catch(error => alert(error.message));
	};

	render() {
		const {accept} = this.props;
		return (
			<FormFileUpload
				className="ea-file-upload"
				accept={accept} onChange={e => {
				this.onChangeFiles(e.target.files);
			}}>
				{__('Upload')}
			</FormFileUpload>
		)
	}
}
FileUpload.propTypes = {
	accept: PropTypes.string.isRequired,
	onAdd: PropTypes.func,
};
