import { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';
export default class Reconciliations extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	componentDidCatch(error, info) {
		this.setState({ error: true, stack: error, info });
	}

	render() {
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Reconciliations')}</h1>
			</Fragment>
		);
	}
}
