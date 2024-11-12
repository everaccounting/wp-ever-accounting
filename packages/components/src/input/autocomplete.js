/**
 * WordPress dependencies
 */
import { forwardRef, memo, Suspense, lazy } from '@wordpress/element';
import { BaseControl, Spinner } from '@wordpress/components';
import { useInstanceId } from '@wordpress/compose';
import { Icon, chevronDown, closeSmall } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import { components } from 'react-select';
import styled from '@emotion/styled';
const AsyncSelect = lazy( () => import( 'react-select/async' ) );

const Autocomplete = forwardRef( ( { label, help, ...props }, ref ) => {
	const id = useInstanceId( Autocomplete, 'eac-autocomplete', props.id );
	const AddNewItemButton = styled.button`
		background: none;
		border: none;
		color: #007cba;
		cursor: pointer;
		font-size: 13px;
		padding: 8px 12px;
		text-align: left;
		width: 100%;
		&:hover {
			background: #f1f1f1;
		}
	`;

	return (
		<BaseControl id={ id } label={ label } help={ help }>
			<Suspense fallback={ <Spinner /> }>
				<AsyncSelect
					id={ id }
					ref={ ref }
					unstyled
					className="eac-autocomplete"
					classNamePrefix="eac-autocomplete"
					styles={ {
						container: ( css ) => ( {
							...css,
							flex: '1 1 auto',
							alignSelf: 'stretch',
						} ),
						control: ( css ) => ( { ...css, borderRadius: 0 } ),
						input: ( css ) => ( { ...css, minHeight: 0 } ),
					} }
					components={ {
						DropdownIndicator: ( _props ) => (
							<components.DropdownIndicator { ..._props }>
								<Icon icon={ chevronDown } size={ 20 } />
							</components.DropdownIndicator>
						),
						ClearIndicator: ( _props ) => (
							<components.ClearIndicator { ..._props }>
								<Icon icon={ closeSmall } size={ 20 } />
							</components.ClearIndicator>
						),
						LoadingIndicator: ( _props ) => (
							<components.LoadingIndicator { ..._props }>
								<Spinner size={ 20 } />
							</components.LoadingIndicator>
						),
					} }
					{ ...props }
				/>
			</Suspense>
		</BaseControl>
	);
} );

export default memo( Autocomplete );
