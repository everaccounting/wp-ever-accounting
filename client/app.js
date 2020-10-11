import {Fragment, Component} from '@wordpress/element';
import {Card, Table, Input, Button, Select, Dropdown, Pagination, DateRangePicker} from "@eaccounting/components";

export default class App extends Component {
	constructor(props) {
		super(props);
		this.state = {
			value: '',
			tags: [
				{key: 1, name: 'Tag One', type: ''},
				{key: 2, name: 'Tag Two', type: 'gray'},
				{key: 5, name: 'Tag Three', type: 'primary'},
				{key: 3, name: 'Tag Four', type: 'success'},
				{key: 4, name: 'Tag Five', type: 'warning'},
				{key: 6, name: 'Tag Six', type: 'danger'}
			]
		}
	}

	render() {
		return (
			<Fragment>
				<Card title="Store Performance"
					  header={
						  <div className="clearfix">
							  <Input
								  style={{width:'92%'}}
								  icon="search"
								  placeholder="Search by name"
							  />
							  <div style={{"float": "right"}}>
								  <Dropdown hideOnClick={false} menu={(
									  <Dropdown.Menu>
										  <Dropdown.Item>Date</Dropdown.Item>
										  <Dropdown.Item>Category</Dropdown.Item>
										  <Dropdown.Item>Account</Dropdown.Item>
									  </Dropdown.Menu>
								  )}>
									  <Button icon="plus">Filter</Button>
								  </Dropdown>
							  </div>
						  </div>
					  }
				>

					<Select value={this.state.value} multiple={true}>
						{
							[{
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
							}].map(el => {
								return <Select.Option key={el.value} label={el.label} value={el.value} />
							})
						}
					</Select>

					<Table
						style={{width: '100%'}}
						columns={[
							{
								type: 'selection'
							},
							{
								label: "Date",
								prop: "date",
								sortable: true
							},
							{
								label: "Name",
								prop: "name",
								sortable: true
							},
							{
								label: "Address",
								prop: "address",
								sortable: true
							}
						]}
						data={[{
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
						}]}
					/>

					<Pagination layout="prev, pager,jumper, next" total={50} style={{margin: '20px 0', textAlign: 'center'}}/>
				</Card>
				{/*<div className="tablenav top">*/}

				{/*	<p className="search-box">*/}
				{/*		<label className="screen-reader-text" htmlFor="eaccounting-accounts-search-input">Search:</label>*/}
				{/*		<input type="search" id="eaccounting-accounts-search-input" name="s" value="" autoComplete="off"/>*/}
				{/*		<input type="submit" className="button" value="Search" id="search-submit"/>*/}
				{/*	</p>*/}
				{/*</div>*/}

				{/*<table className="wp-list-table widefat fixed striped table-view-list transactions">*/}
				{/*	<thead>*/}
				{/*	<tr>*/}
				{/*		<th scope="col" id="date" className="manage-column column-date column-primary sortable desc"><a href="http://accounting.test/wp-admin/admin.php?page=ea-transactions&amp;orderby=date&amp;order=asc"><span>Date</span><span className="sorting-indicator"></span></a></th>*/}
				{/*		<th scope="col" id="amount" className="manage-column column-amount sortable desc"><a href="http://accounting.test/wp-admin/admin.php?page=ea-transactions&amp;orderby=amount&amp;order=asc"><span>Amount</span><span className="sorting-indicator"></span></a></th>*/}
				{/*		<th scope="col" id="account_id" className="manage-column column-account_id sortable desc"><a href="http://accounting.test/wp-admin/admin.php?page=ea-transactions&amp;orderby=account_id&amp;order=asc"><span>Account Name</span><span className="sorting-indicator"></span></a></th>*/}
				{/*		<th scope="col" id="type" className="manage-column column-type sortable desc"><a href="http://accounting.test/wp-admin/admin.php?page=ea-transactions&amp;orderby=type&amp;order=asc"><span>Type</span><span className="sorting-indicator"></span></a></th>*/}
				{/*		<th scope="col" id="category_id" className="manage-column column-category_id sortable desc"><a href="http://accounting.test/wp-admin/admin.php?page=ea-transactions&amp;orderby=category_id&amp;order=asc"><span>Category</span><span className="sorting-indicator"></span></a></th>*/}
				{/*		<th scope="col" id="reference" className="manage-column column-reference sortable desc"><a href="http://accounting.test/wp-admin/admin.php?page=ea-transactions&amp;orderby=reference&amp;order=asc"><span>Reference</span><span className="sorting-indicator"></span></a></th>*/}
				{/*	</tr>*/}
				{/*	</thead>*/}

				{/*	<tbody id="the-list" data-wp-lists="list:transaction">*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-sales&amp;action=edit&amp;tab=revenues&amp;revenue_id=863">2020-09-04</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">$2,000.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">USD</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Income</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">Sales</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">—</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=862">2020-09-02</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳100.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Bank Asia Mirpur 1</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">Internet Bill</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">—</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=861">2020-09-12</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳100.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Bank Asia ByteEver</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">House Rent</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">test</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=849">2020-08-24</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳862.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Cash</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">Utility Bill</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">Electricity Bill June, July, June 2020</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=848">2020-08-24</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳108.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Cash</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">Office Supplies</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">Battery (Pencil, Remote)</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=847">2020-08-22</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳630.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Cash</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">Office Stationery</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">Punch File, Battery, Broom, Scrubber, Conveyance</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=846">2020-08-12</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳526.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Cash</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">Office Supplies</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">Savlon, Hexicol, Conveyance</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=845">2020-03-15</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳1,865.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Cash</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">Tea &amp; Coffee</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">Office Item (Tea, Milk, Napkin ETC</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=844">2020-03-15</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳674.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Cash</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">Lunch</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">Rice, Salt, Turmeric, Chili Powder, Egg, ETC &amp; Conveyance</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=843">2020-08-10</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳32,000.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Bank Asia ByteEver</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">House Rent</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">House Rent, August 2020</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=842">2020-08-09</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳249,000.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Bank Asia ByteEver</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">Payroll – Salary &amp; Wages</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">Salary for the month of July 2020</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=841">2020-07-06</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳32,000.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Bank Asia ByteEver</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">House Rent</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">House Rent, July 2020</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=840">2020-07-05</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳259,000.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Bank Asia ByteEver</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">Payroll – Salary &amp; Wages</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">Salary for the month of June 2020</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=839">2020-06-08</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳32,000.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Cash</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">House Rent</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">House Rent, June 2020</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=838">2020-06-04</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳274,000.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Bank Asia ByteEver</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">Payroll – Salary &amp; Wages</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">Salary for the month of May 2020</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=837">2020-05-06</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳32,000.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Bank Asia ByteEver</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">House Rent</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">House Rent, May 2020</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=836">2020-05-04</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳313,000.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Bank Asia ByteEver</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">Payroll – Salary &amp; Wages</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">Salary for the month of April 2020</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=835">2020-04-07</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳5,000.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Cash</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">Payroll – Salary &amp; Wages</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">Cook, Cleaner salary, March 2020</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=834">2020-04-07</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳38,000.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Bank Asia ByteEver</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">House Rent</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">House Rent, Service Charge April 2020</td>*/}
				{/*	</tr>*/}
				{/*	<tr>*/}
				{/*		<td className="date column-date has-row-actions column-primary" data-colname="Date"><a href="http://accounting.test/wp-admin/admin.php?page=ea-expenses&amp;action=edit&amp;tab=payments&amp;payment_id=833">2020-04-06</a>*/}
				{/*			<button type="button" className="toggle-row"><span className="screen-reader-text">Show more details</span></button>*/}
				{/*		</td>*/}
				{/*		<td className="amount column-amount" data-colname="Amount">৳324,866.00</td>*/}
				{/*		<td className="account_id column-account_id" data-colname="Account Name">Bank Asia ByteEver</td>*/}
				{/*		<td className="type column-type" data-colname="Type">Expense</td>*/}
				{/*		<td className="category_id column-category_id" data-colname="Category">Payroll – Salary &amp; Wages</td>*/}
				{/*		<td className="reference column-reference" data-colname="Reference">Employee Salary for the month of March 2020</td>*/}
				{/*	</tr>*/}
				{/*	</tbody>*/}

				{/*	<tfoot>*/}
				{/*	<tr>*/}
				{/*		<th scope="col" className="manage-column column-date column-primary sortable desc"><a href="http://accounting.test/wp-admin/admin.php?page=ea-transactions&amp;orderby=date&amp;order=asc"><span>Date</span><span className="sorting-indicator"></span></a></th>*/}
				{/*		<th scope="col" className="manage-column column-amount sortable desc"><a href="http://accounting.test/wp-admin/admin.php?page=ea-transactions&amp;orderby=amount&amp;order=asc"><span>Amount</span><span className="sorting-indicator"></span></a></th>*/}
				{/*		<th scope="col" className="manage-column column-account_id sortable desc"><a href="http://accounting.test/wp-admin/admin.php?page=ea-transactions&amp;orderby=account_id&amp;order=asc"><span>Account Name</span><span className="sorting-indicator"></span></a></th>*/}
				{/*		<th scope="col" className="manage-column column-type sortable desc"><a href="http://accounting.test/wp-admin/admin.php?page=ea-transactions&amp;orderby=type&amp;order=asc"><span>Type</span><span className="sorting-indicator"></span></a></th>*/}
				{/*		<th scope="col" className="manage-column column-category_id sortable desc"><a href="http://accounting.test/wp-admin/admin.php?page=ea-transactions&amp;orderby=category_id&amp;order=asc"><span>Category</span><span className="sorting-indicator"></span></a></th>*/}
				{/*		<th scope="col" className="manage-column column-reference sortable desc"><a href="http://accounting.test/wp-admin/admin.php?page=ea-transactions&amp;orderby=reference&amp;order=asc"><span>Reference</span><span className="sorting-indicator"></span></a></th>*/}
				{/*	</tr>*/}
				{/*	</tfoot>*/}

				{/*</table>*/}


			</Fragment>
		)
	}
}
