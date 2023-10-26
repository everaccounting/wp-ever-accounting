/**
 * WordPress dependencies
 */
import { createElement, isValidElement, unmountComponentAtNode, render } from '@wordpress/element';

/**
 * Internal dependencies
 */
import Toast from './toast';

export default function Message(props = {}, type) {
	const div = document.createElement('div');
	const messageBox = document.getElementsByClassName('el-message-content')[0];
	if (messageBox) {
		messageBox.appendChild(div);
		document.body.appendChild(messageBox);
	} else {
		const box = document.createElement('div');
		box.className = 'el-message-content';
		box.appendChild(div);
		document.body.appendChild(box);
	}

	if (typeof props === 'string' || isValidElement(props)) {
		props = {
			message: props,
		};
	}

	if (type) {
		props.type = type;
	}

	const component = createElement(
		Toast,
		Object.assign(props, {
			willUnmount: () => {
				const box = document.getElementsByClassName('el-message-content')[0];
				unmountComponentAtNode(div);
				box.removeChild(div);

				if (props.onClose instanceof Function) {
					props.onClose();
				}
			},
		})
	);

	render(component, div);
}

['success', 'warning', 'info', 'error'].forEach((type) => {
	Message[type] = (options = {}) => {
		return Message(options, type);
	};
});
