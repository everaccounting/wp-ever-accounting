// import {Fragment, Component, useEffect, useState} from '@wordpress/element';
// import {useSelect, AsyncModeProvider} from '@wordpress/data';
// import {ASSET_URL} from "@eaccounting/data";
// import {TextControl} from "@wordpress/components";
// import {useSelectWithRefresh} from "@eaccounting/data";
// import {Loading, SearchBox, SubSub, Pagination, DropdownButton, Drawer, Table, AutoComplete} from "@eaccounting/components";
// import Notices from './components/notices';
import {SelectControl, Dropdown} from "@eaccounting/components";

import InvoiceTable from './invoice-table';

function App() {
	return (
		<div>
			<SelectControl.Customer isMulti={true}/>
			<SelectControl.Vendor isMulti={true}/>
			<SelectControl.Currency isMulti={true}/>
			<InvoiceTable/>
		</div>
	)
}

export default App;
