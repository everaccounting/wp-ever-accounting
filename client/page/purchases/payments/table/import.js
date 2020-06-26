import {Component, Fragment} from 'react';
import {Button, Placeholder, DropZoneProvider, DropZone, FormFileUpload, Spinner} from '@wordpress/components';
import {sprintf, __} from '@wordpress/i18n';
import Papa from 'papaparse';
import './importer.scss';

export default class Importer extends Component {

	constructor(props) {
		super(props);
		this.state = {
			status: '',
			total: 0,
			imported: 0,
			failed: 0,
			data: [],
		};
	}

	onChangeFiles = files => {
		const file = files[0];
		const data = Papa.parse(file, {
			skipEmptyLines: true,
			complete: results => {
				console.log(results);
				// const rows = getValidRows([...results.data.slice(1)], results.data[0], validCols);
				this.setState({
					status: "IN_PROGRESS",
					data: results.data,
					total: results.data.length
				});
				// this.import();
			},
		});
	};

	render() {
		const {status, total, imported, failed} = this.state;
		return (
			<div className="ea-importer ea-importer-wrap ">
				<div className="ea-importer-inner">
					{status !== "IN_PROGRESS" &&
					<p className="ea-importer-help">Allowed file types: XLS, XLSX. Please, <a href="#">download</a> the
						sample file.</p>}

					{status === "IN_PROGRESS" &&
					<p className="ea-importer-help">Importing in progress...</p>}

					{status !== "IN_PROGRESS" && <FormFileUpload
						accept=".csv"
						className="ea-importer-form"
						onChange={e => {
							this.onChangeFiles(e.target.files);
						}}
					>
						{__('Upload')}
					</FormFileUpload>
					}

					{status === "IN_PROGRESS" &&
					<div className="ea-importer-status">
						<table>
							<thead>
							<tr>
								<th>Total</th>
								<th>Failed</th>
								<th>Imported</th>
								<th>Processed</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td><span style={{color: ''}}>{total}</span></td>
								<td><span style={{color: 'red'}}>{failed}</span></td>
								<td><span style={{color: 'green'}}>{imported}</span></td>
								<td><span style={{color: '#0071a1'}}>{failed + imported}</span></td>
							</tr>
							</tbody>
						</table>
						<Spinner/>
					</div>
					}

				</div>
			</div>
		)
	}
}
