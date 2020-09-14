import {Fragment, Component} from '@wordpress/element';
import {Button} from "@eaccounting/components";

export default class App extends Component {
	render() {
		return (
			<div>
				<Button>Default Button</Button>
				<Button type="primary">Primary Button</Button>
				<Button type="text">Text Button</Button>
			</div>
		)
	}
}
