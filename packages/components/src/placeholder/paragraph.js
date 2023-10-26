export const Paragraph = ( props ) => {
	const { className, style } = props;

	const getWidth = ( index ) => {
		const { width, rows = 2 } = props;
		if ( Array.isArray( width ) ) {
			return width[ index ];
		}
		// last paragraph
		if ( rows - 1 === index ) {
			return width;
		}
		return undefined;
	};

	const rowList = [ ...Array( props.rows ) ].map( ( _, index ) => (
		// eslint-disable-next-line react/no-array-index-key
		<li key={ index } style={ { width: getWidth( index ) } } />
	) );

	return (
		<ul className={ className } style={ style }>
			{ rowList }
		</ul>
	);
};

export default Paragraph;
