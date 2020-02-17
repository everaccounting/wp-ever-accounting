/**
 * External dependencies
 */

import React from 'react';
import PropTypes from 'prop-types';

const MultiOption = ( { label, value, onSelect, isSelected } ) => {
	return (
		<p>
			<label>
				<input type="checkbox" name={ value } onChange={ ev => onSelect( value, ev.target.checked ) } checked={ isSelected } />

				{ label }
			</label>
		</p>
	);
};

class MultiOptionGroup extends React.Component {
	static propTypes = {
		label: PropTypes.string.isRequired,
		options: PropTypes.array,
		value: PropTypes.string.isRequired,
		selected: PropTypes.object.isRequired,
		onApply: PropTypes.func.isRequired,
		multiple: PropTypes.bool.isRequired,
	};

	onSelect = ( optionValue, checked ) => {
		const { selected, value, multiple } = this.props;
		const newSelected = { ...selected };

		// Now add the new option
		if ( checked ) {
			const target = optionValue === value ? true : optionValue;
			newSelected[ value ] = multiple ? [ ...newSelected[ value ], optionValue ] : target;
		} else if ( multiple ) {
			newSelected[ value ] = newSelected[ value ].filter( item => item !== optionValue );
		} else {
			delete newSelected[ value ];
		}

		this.props.onApply( newSelected, optionValue );
	}

	isSelected( option ) {
		const { multiple, selected, value } = this.props;

		if ( multiple && Array.isArray( selected[ value ] ) ) {
			return selected[ value ].indexOf( option ) !== -1;
		}

		if ( value === option && selected[ value ] ) {
			return true;
		}

		return selected[ value ] === option;
	}

	render() {
		const { label, options, value } = this.props;

		if ( options ) {
			return (
				<div className="redirect-multioption__group">
					<h5>{ label }</h5>

					{ options.map( option => (
						<MultiOption
							label={ option.label }
							value={ option.value }
							onSelect={ this.onSelect }
							isSelected={ this.isSelected( option.value ) }
							key={ option.value }
						/>
					) ) }
				</div>
			);
		}

		return (
			<MultiOption
				label={ label }
				value={ value }
				onSelect={ this.onSelect }
				isSelected={ this.isSelected( value ) }
			/>
		);
	}
}

export default MultiOptionGroup;
