/**
 * WordPress dependencies
 */
import { Modal as ModalComponent } from '@wordpress/components';
import { forwardRef } from '@wordpress/element';

function Modal(props, ref) {
	const { style, ...rest } = props;
	const modalStyle = {
		minWidth: '50%',
	};

	return <ModalComponent style={{ ...modalStyle, ...style }} {...rest} ref={ref} />;
}

export default Modal;
