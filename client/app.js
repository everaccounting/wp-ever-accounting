import {Fragment, Component} from '@wordpress/element';
import {ITEMS_STORE_NAME} from './store';
import {withSelect} from '@wordpress/data';
import {compose} from '@wordpress/compose';
import {toArray} from 'lodash';
import {Table, Loading, DateRangePicker, Pagination, Select, Layout, Button, Popover} from '@eaccounting/components';

class App extends Component {
	constructor(props) {
		super(props);

		this.state = {
			columns: [
				{
					type: 'selection',
				},
				{
					label: "Date",
					prop: "date",
					sortable: true
				},
				{
					label: "Name",
					prop: "name",
				},
				{
					label: "Address",
					prop: "address"
				}
			],
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
			}, {
				date: '2016-05-08',
				name: 'Tom',
				address: 'No. 189, Grove St, Los Angeles'
			}, {
				date: '2016-05-06',
				name: 'Tom',
				address: 'No. 189, Grove St, Los Angeles'
			}, {
				date: '2016-05-07',
				name: 'Tom',
				address: 'No. 189, Grove St, Los Angeles'
			}],
			options: [{
				value: 'Option1',
				label: 'Option1'
			}, {
				value: 'Option2',
				label: 'Option2'
			}, {
				value: 'Option3',
				label: 'Option3'
			}, {
				value: 'Option4',
				label: 'Option4'
			}, {
				value: 'Option5',
				label: 'Option5'
			}],
			value: '',
			value1: null, value2: null
		}
	}


	render() {
		const {posts, isRequesting} = this.props;
		const {value1, value2} = this.state
		return (
			<div>


					<Layout.Row justify="start" type="flex" style={{marginBottom:'20px'}}>
						<Layout.Col>
							<Select
								allowCreate={true}
								filterable={true}
								value={this.state.value}>
								{
									this.state.options.map(el => {
										return <Select.Option key={el.value} label={el.label} value={el.value} />
									})
								}
							</Select>
							</Layout.Col>

							<Layout.Col>
							<DateRangePicker
								value={value2}
								placeholder="Pick a range"
								align="right"
								size="small"
								ref={e=>this.daterangepicker2 = e}
								onChange={date=>{
									console.debug('DateRangePicker2 changed: ', date)
									this.setState({value2: date})
								}}
								shortcuts={[{
									text: 'Last week',
									onClick: ()=> {
										const end = new Date();
										const start = new Date();
										start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);

										this.setState({value2: [start, end]})
										this.daterangepicker2.togglePickerVisible()
									}
								}, {
									text: 'Last month',
									onClick: ()=> {
										const end = new Date();
										const start = new Date();
										start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);

										this.setState({value2: [start, end]})
										this.daterangepicker2.togglePickerVisible()
									}
								}, {
									text: 'Last 3 months',
									onClick: ()=> {
										const end = new Date();
										const start = new Date();
										start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
										this.setState({value2: [start, end]})
										this.daterangepicker2.togglePickerVisible()
									}
								}]}
							/>

						</Layout.Col>
						<Layout.Col>
							Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium, magni!
						</Layout.Col>
					</Layout.Row>

					<Loading loading={isRequesting}>
						<Table
							style={{width: '100%'}}
							columns={this.state.columns}
							data={this.state.data}
							border={false}
							onSelectChange={(selection) => { console.log(selection) }}
							onSelectAll={(selection) => { console.log(selection) }}
						/>

						<Pagination style={{marginTop:'20px'}} layout="total, prev, pager, next, jumper" total={50}/>
					</Loading>

			</div>
		)
	}
}


export default compose(
	withSelect((select, props) => {
		const {getItems, getItemsError, isResolving} = select(
			ITEMS_STORE_NAME
		);
		const tableQuery = {};

		const posts = getItems('posts', tableQuery);
		const isCategoriesError = Boolean(
			getItemsError('posts', tableQuery)
		);
		const isCategoriesRequesting = isResolving('getItems', [
			'posts',
			tableQuery,
		]);

		return {
			posts,
			isError: isCategoriesError,
			isRequesting: isCategoriesRequesting,
		};
	})
)(App);
