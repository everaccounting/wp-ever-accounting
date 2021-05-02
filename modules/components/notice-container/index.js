import '@wordpress/notices';

/**
 * WordPress dependencies
 */
import { SnackbarList, } from '@wordpress/components';
import { withSelect, withDispatch } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import './style.scss';

export function NoticeContainer({ notices, onRemove }) {
	return (
		<>
			<SnackbarList
				notices={notices}
				className="ea-notices__snackbar"
				onRemove={onRemove}
			/>
		</>
	);
}
export default compose([
	withSelect((select) => ({
		notices: select('core/notices').getNotices(),
	})),
	withDispatch((dispatch) => ({
		onRemove: dispatch('core/notices').removeNotice,
	})),
])(NoticeContainer);
