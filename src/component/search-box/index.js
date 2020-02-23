/**
 * External dependencies
 */

import React from 'react';
import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import {STATUS_IN_PROGRESS} from 'lib/status';
import './style.scss';
import {__} from "@wordpress/i18n";

class SearchBox extends React.Component {
	static propTypes = {
		onSearch: PropTypes.func.isRequired,
		status: PropTypes.string,
	};

	constructor(props) {
		super(props);
		this.state = {
			search: ''
		};
	}

	onSearch = (ev) => {
		this.setState({search: ev.target.value});
		if (ev.target.value === '') {
			this.props.onSearch('');
		}
	};

	onSubmit = (ev) => {
		ev && ev.preventDefault();
		this.props.onSearch(this.state.search);
	};

	render() {
		const {status} = this.props;
		const disabled = status === STATUS_IN_PROGRESS || (this.state.search === '');

		return (
			<form onSubmit={this.onSubmit} className="ea-searchbox">
				<input type="search" name="s" value={this.state.search} onChange={this.onSearch}/>
				<input type="submit" className="button" value={__('Search')} disabled={disabled}/>
			</form>
		);
	}
}

export default SearchBox;
