import { Component, Fragment } from 'react';
import {__} from '@wordpress/i18n';
import {withEntity} from "@eaccounting/hoc";

class ViewRevenue extends Component {
	constructor(props) {
		super(props);
	}
	render() {
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('View')}</h1>
			</Fragment>
		);
	}
}

export default withEntity('revenues')(ViewRevenue)
