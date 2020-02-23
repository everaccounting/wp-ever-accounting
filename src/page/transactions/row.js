/**
 * External dependencies
 */

import {Component, Fragment} from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import moment from 'moment'
/**
 * Internal dependencies
 */
import {setSelected, performTableAction} from 'state/transactions/action';
import {STATUS_SAVING, STATUS_IN_PROGRESS} from 'lib/status';
import Spinner from 'component/spinner';
import Column from 'component/table/column';
import RowActions from 'component/table/row-action';
import {translate as __} from 'lib/locale';

export default class TransactionsRow extends Component {
	static propTypes = {
		item: PropTypes.object.isRequired,
	};

	constructor(props) {
		super(props);
		this.state = {};
	}

	getProp = (prop, properties, initial='-') => {
		if( (properties instanceof Object) && properties.hasOwnProperty(prop)){
			return properties[prop];
		}
		return initial;
	};

	render() {
		const {paid_at, account, type = '', category = {name: '-'}, reference = '-', amount = ''} = this.props.item;
		return (
			<Fragment>
				<tr>
					<Column className="column-primary column-date">
						{moment(paid_at).format("d MMM Y")}
					</Column>

					<Column className="column-primary column-account">
						{this.getProp('name', account)}
					</Column>

					<Column className="column-primary column-type">
						{type}
					</Column>

					<Column className="column-primary column-category">
						{this.getProp('name', category)}
					</Column>

					<Column className="column-primary column-reference">
						{reference}
					</Column>

					<Column className="column-primary column-amount">
						{amount}
					</Column>
				</tr>
			</Fragment>
		)
	}
}
