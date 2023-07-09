import { useEffect, useState } from '@wordpress/element';
import PropTypes from 'prop-types';
import { debounce } from 'lodash';
import { FormTokenField, Spinner } from '@wordpress/components';
import './style.scss';

const FormToken = (props) => {
	const [suggestions, setSuggestions] = useState([]);
	const [validValues, setValidValues] = useState({});
	const [loading, setLoading] = useState(isFetchingInfoOnLoad());

	const debouncedUpdateSuggestions = debounce(updateSuggestions, 300);

	function isFetchingInfoOnLoad() {
		const { tokens, fetchSavedInfo } = props;
		return Boolean(tokens.length && fetchSavedInfo);
	}

	useEffect(() => {
		if (isFetchingInfoOnLoad()) {
			const { tokens, fetchSavedInfo } = props;

			fetchSavedInfo(tokens).then((results) => {
				const newValidValues = {};

				results.forEach((suggestion) => {
					newValidValues[suggestion.value] = suggestion.label;
				});

				setValidValues(newValidValues);
				setLoading(false);
			});
		}
	}, []);

	useEffect(() => {
		return () => {
			debouncedUpdateSuggestions.cancel();
		};
	}, []);

	function getLabelsForValues(values) {
		return values.reduce((accumulator, value) => {
			if (validValues[value]) {
				return [...accumulator, validValues[value]];
			}
			return accumulator;
		}, []);
	}

	function getValuesForLabels(labels) {
		return labels.map((label) =>
			Object.keys(validValues).find((key) => validValues[key] === label)
		);
	}

	function updateSuggestions(input) {
		const { fetchSuggestions } = props;
		if (!fetchSuggestions) {
			return;
		}

		setLoading(true);

		const request = fetchSuggestions(input);
		request
			.then((suggestions) => {
				const newValidValues = {};
				const currentSuggestions = [];

				suggestions.forEach((suggestion) => {
					const duplicatedSuggestionIndex = currentSuggestions.indexOf(
						suggestion.label
					);
					if (duplicatedSuggestionIndex >= 0) {
						suggestion.label = `${suggestion.label} (${suggestion.value})`;
					}
					currentSuggestions.push(suggestion.label);
					newValidValues[suggestion.value] = suggestion.label;
				});

				if (request === currentRequest) {
					setSuggestions(currentSuggestions);
					setValidValues(newValidValues);
					setLoading(false);
				}
			})
			.catch(() => {
				if (request === currentRequest) {
					setLoading(false);
				}
			});

		const currentRequest = request;
	}

	function handleOnChange(tokenStrings) {
		const { onChange } = props;
		onChange(getValuesForLabels(tokenStrings));
	}

	function getTokens() {
		const { tokens } = props;
		return getLabelsForValues(tokens);
	}

	const { help, label = '' } = props;

	return (
		<div className="autocomplete-tokenfield">
			<FormTokenField
				value={getTokens()}
				suggestions={suggestions}
				onChange={(tokens) => handleOnChange(tokens)}
				onInputChange={(input) => debouncedUpdateSuggestions(input)}
				label={label}
				{...props}
			/>
			{loading && <Spinner />}
			{help && <p className="autocomplete-tokenfield__help">{help}</p>}
		</div>
	);
};

FormToken.propTypes = {
	tokens: PropTypes.arrayOf(PropTypes.string).isRequired,
	fetchSavedInfo: PropTypes.func.isRequired,
	fetchSuggestions: PropTypes.func,
	onChange: PropTypes.func.isRequired,
	help: PropTypes.string,
	label: PropTypes.string,
};

export default FormToken;
