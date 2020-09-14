import {Fragment, Component} from '@wordpress/element';
import Button from "./Button";

export default class Example extends Component {
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
