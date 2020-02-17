import { NotificationManager } from 'react-notifications';
import { __ } from '@wordpress/i18n';

const notify = (message, type = 'success') => {
	switch (type) {
		case 'info':
			NotificationManager.info(message, '', 2000);
			break;
		case 'success':
			NotificationManager.success(message, __('Success', 'wp-ever-crm'), 2000);
			break;
		case 'warning':
			NotificationManager.warning(message, __('Warning', 'wp-ever-crm'), 2000);
			break;
		case 'error':
			NotificationManager.error(message, __('Error', 'wp-ever-crm'), 2000);
			break;
	}
};
export default notify;
