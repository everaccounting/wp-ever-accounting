import {Component} from '@wordpress/element';
import {FormFileUpload, BaseControl, Dashicon} from '@wordpress/components';
import {__} from "@wordpress/i18n";
import PropTypes from "prop-types";
import apiFetch from "@wordpress/api-fetch";
import isShallowEqual from '@wordpress/is-shallow-equal';
import {concat, remove, forEach} from "lodash";

export default class Files extends Component {
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
				this.props.onChange(concat(this.props.value, res))
			}).catch(error => alert(error.message));
		});
	};


	onRemoveFile = file => {
		if (true === confirm(__('Do you really want to delete the file'))) {
			apiFetch({path: `/ea/v1/files/${file.id}`, method: 'DELETE'}).then(res => {
				this.props.onChange(remove(this.props.value, item => {
					return item.id === file.id
				}))
			}).catch(error => alert(error.message));
		}
	};

	triggerChange = () => {
		const {files} = this.state;
		this.props.onChange(files.map(file => file.id).join(','))
	};


	render() {
		const {label, accept, value:files} = this.props;

		return (
			<BaseControl label={label} className='ea-form-group'>
				{files && <ul className="ea-file-list">
					{files.map((file, index) => {
						return (
							<li key={index}>
								<a href={file.url} className="ea-file-name" title={file.name}
								   target="_blank">{file.name}</a>
								<a href="#" title={__('Delete')} className='ea-file-delete'
								   onClick={(e) => {e.preventDefault(); this.onRemoveFile(file)}}><Dashicon icon={'no-alt'}/></a>
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

Files.propTypes = {
	value: PropTypes.any,
	label: PropTypes.string,
	accept: PropTypes.string,
	onChange: PropTypes.func,
};

Files.defaultProps = {
	label: __('Files'),
	accept: 'image/*, .pdf, .doc',
};
