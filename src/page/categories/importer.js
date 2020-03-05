import React, {Component, Fragment} from 'react';
import {translate as __} from 'lib/locale';
import {sprintf} from '@wordpress/i18n';
import {Button, Placeholder, DropZoneProvider, DropZone, FormFileUpload, Spinner} from '@wordpress/components';
import {STATUS_COMPLETE, STATUS_IN_PROGRESS, STATUS_FAILED} from 'status';
import Papa from 'papaparse';
import {validateFileExt, getValidRows} from 'lib/import'
import {accountingApi, getApi} from "lib/api";

const validCols = [
	'name',
	'type',
	'status',
];

export default class CategoryImporter extends Component {
	constructor(props, context) {
		super(props, context);
		this.state = {
			status: '',
			imported: 0,
			skipped: 0,
			processed: 0,
			data: [],
		}
	}

	import = () => {
		const {status, data} = this.state;
		if (!data.length) {
			notify(__('No valid data available to import.'), 'error');
			this.setState({
				imported: 0,
				skipped: 0,
				status: '',
			});
		}

		const item = this.getItem();

		if (false === item) {
			this.setState({
				status: STATUS_COMPLETE
			});
		}

		getApi(accountingApi.categories.create(item)).then(res => {
			this.setState({
				imported: this.state.imported + 1,
				processed: this.state.processed + 1,
			});
			this.import();
		}).catch(error => {
			this.setState({
				processed: this.state.processed + 1,
				skipped: this.state.skipped + 1,
			});
			this.import();
		});

	};

	getItem = () => {
		const {processed, data} = this.state;
		if (processed >= data.length) {
			return false;
		}
		return data[processed] || false;
	};

	onChangeFiles = files => {
		if (files.length) {
			this.setState({
				status: STATUS_IN_PROGRESS,
			});
			const file = files[0];
			if (validateFileExt(file)) {
				const data = Papa.parse(file, {
					skipEmptyLines: true,
					complete: results => {
						const rows = getValidRows([...results.data.slice(1)], results.data[0], validCols);
						this.setState({
							status: STATUS_IN_PROGRESS,
							data: rows
						});
						this.import();
					},
				});
			}
		}
	};

	render() {
		const {status, data, processed, skipped, imported} = this.state;
		let label, instructions;
		if (status === STATUS_COMPLETE) {
			label = __('Import Complete !!!');
			instructions = sprintf(__('Imported %d, skipped %d of total %d items'), imported, skipped, data.length);
		} else if (status === STATUS_IN_PROGRESS) {
			label = __('Importing categories...');
			instructions = sprintf(__('Processing %d, Imported %d, skipped %d of total %d items'),processed, imported, skipped, data.length);
		} else {
			label = __('Import a CSV file.');
			instructions = __("Click 'Add File' or drag and drop here");
		}


		return (
			<div className='ea-importer'>
				<h2>{__('Import')}</h2>

				<DropZoneProvider>
					{<DropZone label={__('Drop file to import')} onFilesDrop={this.onChangeFiles}/>}

					<div className="inline-notice notice-warning">
						<p>{__('Allowed file types: CSV, XLS, XLSX. Please,')} <a
							href="#">{__('download')}</a> {__('the sample file.')}</p>
					</div>

					<Placeholder
						label={label}
						instructions={instructions}>
						{status === '' && <FormFileUpload
							accept=".csv"
							onChange={e => {
								this.onChangeFiles(e.target.files)
							}}>{__('Upload')}
						</FormFileUpload>}
						{status === STATUS_IN_PROGRESS && <Spinner/>}
					</Placeholder>

				</DropZoneProvider>

			</div>
		)
	}
}
