/**
 * @typedef SelectOption
 * @type {Object}
 * @property {string} label - a descriptive label.
 * @property {string|SelectOption[]} value - value for the option, or an array of SelectOption
 */

/**
 * Select option value.
 *
 * @param {Object} props - Component props
 * @param {SelectOption[] | string} props.value - Either another value/label array for a select with subgroup, or a value.
 * @param {string} props.label - Value label
 */
const Option = ( props ) => {
	const { value, label } = props;

	if ( typeof value === 'object' ) {
		return (
			<optgroup label={ label }>
				{ value.map( ( item, pos ) => (
					<Option
						label={ item.label }
						value={ item.value }
						key={ pos }
					/>
				) ) }
			</optgroup>
		);
	}

	return <option value={ value }>{ label }</option>;
};

export default Option;
