import {render} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import {Fragment, Component} from '@wordpress/element';

__webpack_public_path__ = eaccounting_client_i10n.dist_url;
import {Table, Pagination, Card, Layout, DatePicker} from "@eaccounting/components";

import './stylesheets/syle.scss';


class App extends Component {
	constructor(props) {
		super(props);

		this.state = {
			data: [{
				date: '2016-05-03',
				name: 'Tom',
				address: 'No. 189, Grove St, Los Angeles'
			}, {
				date: '2016-05-02',
				name: 'Tom',
				address: 'No. 189, Grove St, Los Angeles'
			}, {
				date: '2016-05-04',
				name: 'Tom',
				address: 'No. 189, Grove St, Los Angeles'
			}, {
				date: '2016-05-01',
				name: 'Tom',
				address: 'No. 189, Grove St, Los Angeles'
			}]
		};
	}

	render() {
		return (
			<Fragment>
				<Card>
					<Layout.Row gutter="20">
						<Layout.Col span="6">

							<DatePicker
								placeholder="Pick a month"
								onChange={date => {
									console.debug('month DatePicker changed: ', date)
								}}/>

						</Layout.Col>
						<Layout.Col span="6">
							Lorem ipsum dolor sit amet, consectetur adipisicing elit. Distinctio, voluptates!
						</Layout.Col>
						<Layout.Col span="4">
							Lorem ipsum dolor sit amet, consectetur adipisicing elit. Distinctio, voluptates!
						</Layout.Col>
						<Layout.Col span="4">
							Lorem ipsum dolor sit amet, consectetur adipisicing elit. Distinctio, voluptates!
						</Layout.Col>
						<Layout.Col span="4">
							Lorem ipsum dolor sit amet, consectetur adipisicing elit. Distinctio, voluptates!
						</Layout.Col>
					</Layout.Row>
				</Card>

				<Table
					style={{width: '100%'}}
					className={'wp-list-table widefat fixed striped'}
					columns={[
						{
							label: "Date",
							prop: "date",
						},
						{
							label: "Name",
							prop: "name",
						},
						{
							label: "Address",
							prop: "address",
						}
					]}
					data={this.state.data}
					defaultSort={{order: 'ascending'}}
				/>
			</Fragment>
		);
	}
}

domReady(() => {
	const root = document.getElementById('eaccounting');
	return render(<App/>, root);
});
