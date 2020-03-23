import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {withSelect} from "@wordpress/data";

class Dashboard extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	componentDidCatch(error, info) {
		this.setState({error: true, stack: error, info});
	}

	render() {
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Dashboard')}</h1>
				{this.props.status}
				<br/>
				{this.props.total}
				<br/>
				{JSON.stringify(this.props.items)}
			</Fragment>
		);
	}
}

export default withSelect((select) => {
	const {getCollection, getTotal, getStatus} = select('ea/store');
	const query = {page: 2};
	getStatus('contacts', query);
	return {
		items: getCollection('contacts', query),
		total: getTotal('contacts', query),
		status: getStatus('contacts', query),
	}
})(Dashboard)
