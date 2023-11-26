/**
 * WordPress dependencies
 */
import { ENTER, SPACE } from '@wordpress/keycodes';
import { Icon } from '@wordpress/components';
/**
 * External dependencies
 */
import classnames from 'classnames';

function Step( props ) {
	const {
		className,
		active,
		status,
		icon,
		stepNumber,
		disabled,
		description,
		title,
		subTitle,
		stepIcon,
		tailContent,
		icons,
		stepIndex,
		onStepClick,
		onClick,
		...restProps
	} = props;
	const clickable = !! onStepClick && ! disabled;
	const mergedStatus = status || 'wait';
	const accessibilityProps = {};
	if ( clickable ) {
		accessibilityProps.role = 'button';
		accessibilityProps.tabIndex = 0;
		accessibilityProps.onClick = ( e ) => {
			onClick?.( e );
			onStepClick( stepIndex );
		};
		accessibilityProps.onKeyDown = ( e ) => {
			const { which } = e;
			if ( which === ENTER || which === SPACE ) {
				onStepClick( stepIndex );
			}
		};
	}

	const renderIcon = () => {
		let iconNode;
		const iconClasses = classnames( 'eac-steps__item-icon', {
			'eac-steps__item-icon--check':
				! icon && status === 'finish' && ( ( icons && ! icons.finish ) || ! icons ),
			'eac-steps__item-icon--cross':
				! icon && status === 'error' && ( ( icons && ! icons.error ) || ! icons ),
		} );
		const iconDot = <span className="eac-steps__item-icon--dot" />;
		if ( icon && typeof icon === 'string' ) {
			iconNode = <Icon icon={ icon } />;
		} else if ( icon && typeof icon === 'object' ) {
			iconNode = icon;
		} else if ( status === 'finish' ) {
			iconNode = <Icon icon="yes" />;
		} else if ( status === 'error' ) {
			iconNode = <Icon icon="no-alt" />;
		} else {
			iconNode = <span className="eac-steps__item-icon">{ stepNumber }</span>;
		}

		return iconNode;
	};

	const classes = classnames( 'eac-steps__item', className, {
		'eac-steps__item--active': active,
		'eac-steps__item--disabled': disabled,
		'eac-steps__item--clickable': clickable,
		[ `eac-steps__item--${ mergedStatus }` ]: mergedStatus,
	} );

	return (
		<div className={ classes } { ...accessibilityProps } { ...restProps }>
			<div
				onClick={ onClick }
				{ ...accessibilityProps }
				className="eac-steps__item-container"
			>
				<div className="eac-steps__item-tail" />
				<div className="eac-steps__item-icon">{ renderIcon() }</div>
				<div className="eac-steps__item-content">
					<div className="eac-steps__item-title">
						{ title }
						{ subTitle && (
							<div
								title={ typeof subTitle === 'string' ? subTitle : undefined }
								className="eac-steps__item-subtitle"
							>
								{ subTitle }
							</div>
						) }
					</div>
					{ description && (
						<div className="eac-steps__item-description">{ description }</div>
					) }
				</div>
			</div>
		</div>
	);
}

export default Step;
