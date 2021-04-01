import { STORE_KEY } from '../constants';
/**
 * WordPress dependencies
 */
import { controls } from '@wordpress/data';
import { dispatch } from '../controls';
export function* __unstableAcquireStoreLock( store, path, { exclusive } ) {
	const promise = yield* __unstableEnqueueLockRequest( store, path, {
		exclusive,
	} );
	// yield* __unstableProcessPendingLockRequests();
	yield dispatch( STORE_KEY, '__unstableProcessPendingLockRequests' );
	// return yield awaitPromise( promise );
}

export function* __unstableEnqueueLockRequest( store, path, { exclusive } ) {
	let notifyAcquired;
	const promise = new Promise( ( resolve ) => {
		notifyAcquired = resolve;
	} );
	yield {
		type: 'ENQUEUE_LOCK_REQUEST',
		request: { store, path, exclusive, notifyAcquired },
	};
	return promise;
}

export function* __unstableReleaseStoreLock( lock ) {
	yield {
		type: 'RELEASE_LOCK',
		lock,
	};
	yield* dispatch( STORE_KEY, '__unstableProcessPendingLockRequests' );
}

export function* __unstableProcessPendingLockRequests() {
	yield {
		type: 'PROCESS_PENDING_LOCK_REQUESTS',
	};
	const lockRequests = yield controls.select(
		STORE_KEY,
		'__unstableGetPendingLockRequests'
	);
	for ( const request of lockRequests ) {
		const { store, path, exclusive, notifyAcquired } = request;
		const isAvailable = yield controls.select(
			STORE_KEY,
			'__unstableIsLockAvailable',
			store,
			path,
			{
				exclusive,
			}
		);
		if ( isAvailable ) {
			const lock = { store, path, exclusive };
			yield {
				type: 'GRANT_LOCK_REQUEST',
				lock,
				request,
			};
			notifyAcquired( lock );
		}
	}
}
