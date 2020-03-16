import {Component, Fragment} from '@wordpress/element';
import SelectControl from '../select-control';
import apiFetch from "@wordpress/api-fetch";

export default class ContactTypesControl extends Component {

	constructor(props) {
		super(props);
		this.state = {
			options: [],
		};

		this.fetchAPI = this.fetchAPI.bind(this);
	}

	componentDidMount() {
		this.fetchAPI();
	}

	fetchAPI() {
		apiFetch({path: '/ea/v1/contacts/types'}).then(options => {
			this.setState({
				options
			})
		})
	}

	render() {
		const { options } = this.state;
		return (
			<Fragment>
				<SelectControl
					options={options}
					{...this.props}
				/>
			</Fragment>
		);
	}
}
