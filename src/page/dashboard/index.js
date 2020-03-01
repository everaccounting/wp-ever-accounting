import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';

export default class Dashboard extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	render() {
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Dashboard')}</h1>
			</Fragment>
		)
	}
}
