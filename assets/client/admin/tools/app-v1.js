/**
 * External dependencies
 */
import { SelectControl } from '@eac/components';
/**
 * WordPress dependencies
 */
import { TextControl } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';

const sampleItems = [
	{ value: 'apple', label: 'Apple' },
	{ value: 'pear', label: 'Pear' },
	{ value: 'orange', label: 'Orange' },
	{ value: 'grape', label: 'Grape' },
	{ value: 'banana', label: 'Banana' },
];

const App = () => {
	const [ selected, setSelected ] = useState( [] );
	const [ select, setSelect ] = useState();

	return (
		<div>
			<h1>Async/Await Test</h1>
			<TextControl label="Text" help="This is a text control" />
			<SelectControl
				items={ sampleItems }
				label="Single value"
				selected={ selected }
				multiple={ true }
				onSelect={ ( item ) => item && setSelected( [ ...selected, item ] ) }
				onRemove={ () => setSelected( [] ) }
				help="This is a select control"
			/>
			<SelectControl
				items={ sampleItems }
				label="Single value"
				selected={ select }
				multiple={ false }
				onSelect={ ( item ) => item && setSelect( item ) }
				onRemove={ () => setSelect( null ) }
				help="This is a select control"
			/>
		</div>
	);
};

export default App;
