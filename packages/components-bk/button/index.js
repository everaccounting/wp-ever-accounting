export default function Button( props ) {
	return React.createElement(
		'button',
		{
			className: 'button',
			onClick: props.onClick,
			disabled: props.disabled,
		},
		props.children
	);
}
