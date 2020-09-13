import { Component } from '@wordpress/element';
import { Button } from '@wordpress/components';

class Another extends Component {
	constructor(props) {
		super(props);
	}

	render() {

		return (
			<Button>
				hello
			</Button>
		);
	}
}

export default Another;
