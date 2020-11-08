import {Fragment, Component, useState} from '@wordpress/element';
import {ASSET_URL} from "@eaccounting/data";
import {Loading, SearchBox, SubSub, Pagination, DropdownButton, Drawer, Table} from "@eaccounting/components";
import {__} from '@wordpress/i18n';
import {TextControl, Button, Dashicon} from '@wordpress/components';

const Menu = [{
	name: __('Redirects'),
	value: '',
},
	{
		name: __('Groups'),
		value: 'groups',
	},
	{
		name: __('Site'),
		value: 'site',
	},
	{
		name: __('Log'),
		value: 'log',
	},
	{
		name: __('404s'),
		value: '404s',
	}];
const dropdown = [{
	name: __('Redirects'),
	title: '',
},
	{
		name: __('Groups'),
		title: 'groups',
	},
	{
		name: __('Site'),
		title: 'site',
	},
	{
		name: __('Log'),
		title: 'log',
	},
	{
		name: __('404s'),
		title: '404s',
	}];

import {columns, data} from "./table";

export default class App extends Component {
	constructor(props) {
		super(props);
		this.state = {
			total: 100,
			dropdownItem: '',
			drawer1: false,
			drawer2: false,
		}
	}

	render() {

		return (
			<Fragment>
				APP
				{/*<Button isSecondary={true} onClick={ () => this.setState({drawer1: !this.state.drawer1})}>Toggle</Button>*/}

				{/*{this.state.drawer1 && <Drawer onClose={() => this.setState({drawer1: !this.state.drawer1})}>*/}
				{/*</Drawer>}*/}

				{/*<Button isSecondary={true} onClick={ () => this.setState({drawer2: !this.state.drawer2})}>Toggle</Button>*/}

				{/*{this.state.drawer2 && <Drawer onClose={() => this.setState({drawer2: !this.state.drawer2})}>*/}
				{/*	Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quam, quos.*/}
				{/*</Drawer>}*/}

				<div className="ea-search-control">
					<input type="text" className='ea-search-control__input' placeholder='Search'/>
					<span className="ea-search-control__icon">
						<Dashicon icon={'search'}/>
					</span>
					<div className="ea-search-control__btn">
						<Button>
							<svg viewBox="0 0 14 14" id="svg-sprite-search" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd" stroke-width="2"><circle cx="6" cy="6" r="5"></circle><path d="M10 10l3 3" stroke-linecap="round"></path></g></svg>
						</Button>
					</div>
				</div>


				<Table
					defaultSort={{orderby: 'city', order: 'asc'}}
					columns={columns}
					data={data}/>

				<DropdownButton
					options={dropdown}
					onSelect={select => console.log(select)}
					selected={this.state.dropdownItem}
					onChange={(dropdownItem) => this.setState({dropdownItem})}
				/>

				<TextControl value={this.state.total} onChange={total => this.setState({total})}/>
				<SearchBox onSearch={val => console.log(val)}/>
				<SubSub items={Menu} onClick={(active) => console.log(active)}/>
				<Pagination page={1} total={this.state.total} onPageChange={page => console.log(page)}/>
			</Fragment>
		)
	}
}
