export default function Title( { className, width, style } ) {
	return (
		<h3 className={ className } style={ { width, ...style } }>
			&nbsp;
		</h3>
	);
}
