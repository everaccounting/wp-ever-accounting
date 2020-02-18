/**
 * External dependencies
 */

import {Component} from 'react';
import { translate as __ } from 'lib/locale';
import {Icon, IconInput} from '@eaccounting/components';
// import {TextControl, SelectControl} from "@wordpress/components";
import TextControl from 'component/text-control'
import SelectControl from 'component/select-control'
/**
 * Internal dependencies
 */
import './style.scss';

const options = [
	{
		key: 'apple',
		label: 'Apple',
		value: { id: 'apple' },
	},
	{
		key: 'apricot',
		label: 'Apricot',
		value: { id: 'apricot' },
	},
	{
		key: 'banana',
		label: 'Banana',
		keywords: [ 'best', 'fruit' ],
		value: { id: 'banana' },
	},
	{
		key: 'blueberry',
		label: 'Blueberry',
		value: { id: 'blueberry' },
	},
	{
		key: 'cherry',
		label: 'Cherry',
		value: { id: 'cherry' },
	},
	{
		key: 'cantaloupe',
		label: 'Cantaloupe',
		value: { id: 'cantaloupe' },
	},
	{
		key: 'dragonfruit',
		label: 'Dragon Fruit',
		value: { id: 'dragonfruit' },
	},
	{
		key: 'elderberry',
		label: 'Elderberry',
		value: { id: 'elderberry' },
	},
];
export default class Dashboard extends Component {
	constructor( props ) {
		super(props);
		this.state = {
			simpleSelected: [],
			simpleMultipleSelected: [],
			singleSelected: [],
			singleSelectedShowAll: [],
			multipleSelected: [],
			inlineSelected: [{
				key: 'elderberry',
				label: 'Elderberry',
				value: { id: 'elderberry' },
			}],
		};
		window.addEventListener( 'popstate', this.onPageChanged );
	}

	componentDidCatch( error, info ) {
		this.setState( { error: true, stack: error, info } );
	}

	componentWillUnmount() {
		window.removeEventListener( 'popstate', this.onPageChanged );
	}


	render() {
		return (
			<div>
				<TextControl
					prefix={<Icon icon={'pencil'}/>}
					suffix="Suffix"
					label="Text field with both affixes"
					help="Text field with both affixes"
					value={'third'}
					onChange={value => setState({ third: value })}
				/>

				<SelectControl
					label="Simple single value"
					onChange={ ( selected ) =>
						this.setState( { simpleSelected: selected } )
					}
					options={ options }
					placeholder="Start typing to filter options..."
					help="Start typing to filter options..."
					selected={ this.state.simpleSelected }
				/>


				<br />
				<SelectControl
					label="Multiple values"
					multiple
					onChange={ ( selected ) =>
						this.setState( { simpleMultipleSelected: selected } )
					}
					options={ options }
					placeholder="Start typing to filter options..."
					help="Start typing to filter options..."
					selected={ this.state.simpleMultipleSelected }
				/>
				<br />
				<SelectControl
					label="Single value searchable"
					isSearchable
					onChange={ ( selected ) =>
						this.setState( { singleSelected: selected } )
					}
					options={ options }
					placeholder="Start typing to filter options..."
					help="Start typing to filter options..."
					selected={ this.state.singleSelected }
				/>
				<br />
				<SelectControl
					label="Single value searchable with options on refocus"
					isSearchable
					onChange={ ( selected ) =>
						this.setState( { singleSelectedShowAll: selected } )
					}
					options={ options }
					placeholder="Start typing to filter options..."
					help="Start typing to filter options..."
					selected={ this.state.singleSelectedShowAll }
					showAllOnFocus
				/>
				<br />
				<SelectControl
					label="Inline tags searchable"
					isSearchable
					multiple
					inlineTags
					onChange={ ( selected ) =>
						this.setState( { inlineSelected: selected } )
					}
					options={ options }
					placeholder="Start typing to filter options..."
					help="Start typing to filter options..."
					selected={ this.state.inlineSelected }
				/>
				<br />
				<SelectControl
					hideBeforeSearch
					isSearchable
					label="Hidden options before search"
					multiple
					onChange={ ( selected ) =>
						this.setState( { multipleSelected: selected } )
					}
					options={ options }
					help="Start typing to filter options..."
					placeholder="Start typing to filter options..."
					autofill={'apple'}
					selected={ 	this.state.multipleSelected }
					showClearButton
				/>
			</div>
		);
	}
}

// function mapDispatchToProps( dispatch ) {
// 	return {}
// }
// function mapStateToProps( state ) {
// 	return {}
// }
//
// export default connect(
// 	mapStateToProps,
// 	mapDispatchToProps,
// )( Dashboard );
