/**
 * TODO v5: consider making it private
 *
 * passes {value} to {ref}
 *
 * WARNING: Be sure to only call this inside a callback that is passed as a ref.
 * Otherwise, make sure to cleanup the previous {ref} if it changes. See
 * https://github.com/mui/material-ui/issues/13539
 *
 * Useful if you want to expose the ref of an inner component to the public API
 * while still using it inside the component.
 * @param ref   A ref callback or ref object. If anything falsy, this is a no-op.
 * @param value
 */
export function setRef( ref, value ) {
	if ( typeof ref === 'function' ) {
		ref( value );
	} else if ( ref ) {
		ref.current = value;
	}
}

/**
 * A version of `React.useLayoutEffect` that does not show a warning when server-side rendering.
 * This is useful for effects that are only needed for client-side rendering but not for SSR.
 *
 * Before you use this hook, make sure to read https://gist.github.com/gaearon/e7d97cdf38a2907924ea12e4ebdf3c85
 * and confirm it doesn't apply to your use-case.
 */
const useEnhancedEffect = typeof window !== 'undefined' ? React.useLayoutEffect : React.useEffect;

export function useEventCallback( fn ) {
	const ref = React.useRef( fn );
	useEnhancedEffect( () => {
		ref.current = fn;
	} );
	return React.useRef(
		( ...args ) =>
			// @ts-expect-error hide `this`
			0,
		ref.current( ...args )
	).current;
}

export function useControlled( { controlled, default: defaultProp, name, state = 'value' } ) {
	// isControlled is ignored in the hook dependency lists as it should never change.
	const { current: isControlled } = React.useRef( controlled !== undefined );
	const [ valueState, setValue ] = React.useState( defaultProp );
	const value = isControlled ? controlled : valueState;

	if ( process.env.NODE_ENV !== 'production' ) {
		React.useEffect( () => {
			if ( isControlled !== ( controlled !== undefined ) ) {
				console.error(
					[
						`MUI: A component is changing the ${
							isControlled ? '' : 'un'
						}controlled ${ state } state of ${ name } to be ${
							isControlled ? 'un' : ''
						}controlled.`,
						'Elements should not switch from uncontrolled to controlled (or vice versa).',
						`Decide between using a controlled or uncontrolled ${ name } ` +
							'element for the lifetime of the component.',
						"The nature of the state is determined during the first render. It's considered controlled if the value is not `undefined`.",
						'More info: https://fb.me/react-controlled-components',
					].join( '\n' )
				);
			}
		}, [ state, name, controlled ] );

		const { current: defaultValue } = React.useRef( defaultProp );

		React.useEffect( () => {
			// Object.is() is not equivalent to the === operator.
			// See https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/is for more details.
			if ( ! isControlled && ! Object.is( defaultValue, defaultProp ) ) {
				console.error(
					[
						`MUI: A component is changing the default ${ state } state of an uncontrolled ${ name } after being initialized. ` +
							`To suppress this warning opt to use a controlled ${ name }.`,
					].join( '\n' )
				);
			}
		}, [ JSON.stringify( defaultProp ) ] );
	}

	const setValueIfUncontrolled = React.useCallback( ( newValue ) => {
		if ( ! isControlled ) {
			setValue( newValue );
		}
	}, [] );

	return [ value, setValueIfUncontrolled ];
}

let globalId = 0;

// TODO React 17: Remove `useGlobalId` once React 17 support is removed
function useGlobalId( idOverride ) {
	const [ defaultId, setDefaultId ] = React.useState( idOverride );
	const id = idOverride || defaultId;
	React.useEffect( () => {
		if ( defaultId == null ) {
			// Fallback to this default id when possible.
			// Use the incrementing value for client-side rendering only.
			// We can't use it server-side.
			// If you want to use random values please consider the Birthday Problem: https://en.wikipedia.org/wiki/Birthday_problem
			globalId += 1;
			setDefaultId( `mui-${ globalId }` );
		}
	}, [ defaultId ] );
	return id;
}

// See https://github.com/mui/material-ui/issues/41190#issuecomment-2040873379 for why
const safeReact = { ...React };
const maybeReactUseId = safeReact.useId;

/**
 *
 * @example <div id={useId()} />
 * @param  idOverride
 * @return {string}
 */
export function useId( idOverride ) {
	// React.useId() is only available from React 17.0.0.
	if ( maybeReactUseId !== undefined ) {
		const reactId = maybeReactUseId();
		return idOverride ?? reactId;
	}

	// TODO: uncomment once we enable eslint-plugin-react-compiler // eslint-disable-next-line react-compiler/react-compiler
	// eslint-disable-next-line react-hooks/rules-of-hooks -- `React.useId` is invariant at runtime.
	return useGlobalId( idOverride );
}

const usePreviousProps = ( value ) => {
	const ref = React.useRef( {} );
	React.useEffect( () => {
		ref.current = value;
	} );
	return ref.current;
};
