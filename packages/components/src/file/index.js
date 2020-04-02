import {Dashicon} from "@wordpress/components";
import {Component} from '@wordpress/element';
import {__} from "@wordpress/i18n";
import PropTypes from "prop-types";

export default class FileList extends Component {

	OnDelete = (file) => {
		if (true === confirm(__('Do you really want to delete the file'))) {
			props.onDelete && props.onDelete(file.id);
		}
	};

	renderList = (files) => {
		return (
			<ul className="ea-file-list">
				{files.map(file => {
					return (
						<li key={file.id}>
							<a href={file.url} className="ea-file-name" title={file.name}
							   target="_blank">{file.name}</a>
							<a href="#" title={__('Delete')} className='ea-file-delete'
							   onClick={() => this.OnDelete(file)}><Dashicon icon={'no-alt'}/></a>
						</li>
					)
				})}
			</ul>
		)
	};

	render() {
		const {files = [], additionalFiles = []} = this.props;
		const allfiles = files.concat(additionalFiles);
		return allfiles ? this.renderList(allfiles) : null;
	}
}
FileList.propTypes = {
	files: PropTypes.array,
	additionalFiles: PropTypes.array,
	onDelete: PropTypes.func,
};
