import Downshift from 'downshift';
import {TextControl} from '@wordpress/components';

const items = [
	'Apple',
	'Banana',
	'Cherry',
	'Date',
	'Fig',
	'Grape',
	'Honeydew',
];

const SearchableDropdown = () => {
	return (
		<Downshift
			// onChange={selection => alert(`You selected ${selection}`)}
			itemToString={item => (item ? item : '')}
			initialInputValue="Apple"
		>
			{({
				  getInputProps,
				  getItemProps,
				  getMenuProps,
				  isOpen,
				  inputValue,
				  highlightedIndex,
			  }) => {
				const filteredItems = items.filter(item =>
					item.toLowerCase().includes(inputValue ? inputValue.toLowerCase() : '')
				);

				return (
					<div>
						<label htmlFor="item-select">Select an item:</label>
						<TextControl
							{...getInputProps({
								placeholder: 'Search an item',
							})}
							style={{ marginBottom: '10px' }}
							onChange={value => {
								const event = { target: { value } };
								getInputProps().onChange(event);
							}}
						/>
						<ul {...getMenuProps()} style={{ border: '1px solid #ccc', marginTop: 0, maxHeight: '200px', overflowY: 'auto' }}>
							{isOpen &&
								filteredItems.map((item, index) => (
									<li
										key={item}
										{...getItemProps({
											item,
											style: {
												backgroundColor: highlightedIndex === index ? '#bde4ff' : 'white',
												cursor: 'pointer',
												padding: '8px',
											},
										})}
									>
										{item}
									</li>
								))}
							{isOpen && filteredItems.length === 0 && (
								<li style={{ padding: '8px' }}>No results found</li>
							)}
						</ul>
					</div>
				);
			}}
		</Downshift>
	);
};

export default SearchableDropdown;
