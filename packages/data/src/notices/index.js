/**
 * WordPress dependencies
 */
import { useSelect, useDispatch } from '@wordpress/data';
import { useCallback, useMemo } from '@wordpress/element';

export const useNotices = () => {
	const noticeDispathes = useDispatch( 'core/notices' );

	const createNotice = useCallback(
		( type, notice ) => {
			return noticeDispathes.createNotice( type, notice );
		},
		[ noticeDispathes ]
	);

    const removeNotice = useCallback(
        ( noticeId ) => {
            return noticeDispathes.removeNotice( noticeId );
        }
    );

	// const mutations =  useMemo(
	// 	() => ( {
	// 		createNotice :
	// 		createSuccessNotice,
	// 	} ),
	// 	[ createErrorNotice, createSuccessNotice ]
	// );
};
