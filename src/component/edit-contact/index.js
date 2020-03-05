import { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';

export default class EditContact extends Component {
	_isMounted = false;

	constructor(props) {
		super(props);
		this.state = {
			id: null,
			user_id: null,
			first_name: '',
			last_name: '',
			email: '',
			phone: '',
			address: '',
			city: '',
			state: '',
			postcode: '',
			country: '',
			website: '',
			note: '',
			avatar_url: '',
			types: {},
			tax_number: '',
			currency: eAccountingi10n.data.currency,
			created_at: '',
			updated_at: '',
		};
	}

	componentDidMount() {
		const { item = {} } = this.props;
		this._isMounted &&
			this.setState({
				...this.state,
				...item,
			});
	}

	render() {
		return <Fragment></Fragment>;
	}
}
