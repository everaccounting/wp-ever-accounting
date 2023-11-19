/**
 * External dependencies
 */
import classnames from 'classnames';
/**
 * Internal dependencies
 */
import Step from './step';
import './style.scss';

function Steps( props ) {
	const {
		className,
		percent,
		size,
		direction = 'horizontal',
		labelPlacement = 'horizontal',
		type = 'default',
		status = 'process',
		items = [],
		current,
		initial = 0,
		children,
		style,
		onChange,
		...restProps
	} = props;

	const isNav = type === 'navigation';
	const isInline = type === 'inline';
	const mergedDirection = isInline ? 'horizontal' : direction;
	const mergedSize = isInline ? undefined : size;

	const onStepClick = ( next ) => {
		if ( onChange && current !== next ) {
			onChange( next );
		}
	};

	const renderStep = ( item, index ) => {
		const mergedItem = { ...item };
		const stepNumber = initial + index;
		if ( status === 'error' && index === current - 1 ) {
			mergedItem.className = 'eac-steps__item--error';
		}

		if ( ! mergedItem.status ) {
			if ( stepNumber === current ) {
				mergedItem.status = status;
			} else if ( stepNumber < current ) {
				mergedItem.status = 'finish';
			} else {
				mergedItem.status = 'wait';
			}
		}
		if ( isInline ) {
			mergedItem.icon = undefined;
			mergedItem.subTitle = undefined;
		}

		return (
			<Step
				{ ...mergedItem }
				key={ stepNumber }
				active={ stepNumber === current }
				stepNumber={ stepNumber + 1 }
				stepIndex={ stepNumber }
				direction={ mergedDirection }
				size={ mergedSize }
				// labelPlacement={ labelPlacement }
				step={ stepNumber }
				percent={ percent }
				onStepClick={ onChange && onStepClick }
			/>
		);
	};

	const classes = classnames( 'eac-steps', className, {
		'eac-steps--navigation': isNav,
		'eac-steps--inline': isInline,
		[ `eac-steps--${ direction }` ]: ! isInline,
		[ `eac-steps--${ size }` ]: size,
		[ `eac-steps--label-${ labelPlacement }` ]: ! isInline,
	} );

	return (
		<div className={ classes } style={ style } { ...restProps }>
			{ items.filter( ( item ) => item ).map( renderStep ) }
		</div>
	);
}

export default Steps;
