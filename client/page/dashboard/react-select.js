import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import Async from "react-select/async";
import { components } from "react-select";

export default class ReactSelectControl extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		const options = [
			{ value: "Chocolate", label: "Chocolate" },
			{ value: "Strawberry", label: "Strawberry" },
			{ value: "Vanilla", label: "Vanilla" }
		];
		const MenuList = props => {
			return (
				<components.MenuList {...props}>
					{props.children}
					<button onClick={() => console.log('clicked')}>Add</button>
				</components.MenuList>
			);
		};
		const cleanInput = (inputValue) =>{
			console.log(inputValue);
			// Strip all non-number characters from the input
			return inputValue.replace(/[^0-9]/g, "");
		}

		return (
			<Fragment>
				<Async
					options={options}
					simpleValue
					isLoading={true}
					onInputChange={this.cleanInput}
					// isMulti={true}
					delimiter={','}
					hideSelectedOptions={true}
					controlShouldRenderValue={true}
					components={{ MenuList }}
					onChange={(value) => console.log(value)}/>
			</Fragment>
		);
	}
}

