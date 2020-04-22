import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {DashboardCard} from '@eaccounting/components';
import {Dashicon} from '@wordpress/components';
import {withSelect} from '@wordpress/data';
import classNames from 'classnames';
import {Placeholder} from "@eaccounting/components";


const SummeryCard = (props) => {
	const {label, amount, className, icon, isLoading} = props;
	const classes = classNames('ea-summery-box__icon', className);
	return (
		<DashboardCard className="ea-col-4 ea-summery-box">
			<div className={classes}>
				<Dashicon icon={icon} size="50"/>
			</div>
			<div className="ea-summery-box__content">
				{label && <span className="ea-summery-box__text">{label}</span>}
				<span className="ea-summery-box__number">{isLoading ? <Placeholder/> : amount && amount}</span>
			</div>
		</DashboardCard>
	)
};


class Summery extends Component {
	render() {
		const {isLoading, summery} = this.props;
		const {income, expense, profit} =  summery;
		return (
			<div className="ea-row">
				<SummeryCard label={__('Total Incomes')} icon={'chart-pie'} amount={income} className="income" isLoading={isLoading}/>
				<SummeryCard label={__('Total Expense')} icon={'cart'} amount={expense} className="expense" isLoading={isLoading}/>
				<SummeryCard label={__('Total Profit')} icon={'heart'} amount={profit} className="profit" isLoading={isLoading}/>
			</div>
		)
	}
}

export default withSelect((select, ownProps) => {
	const {date} = ownProps;
	const {fetchAPI, isRequestingFetchAPI} = select('ea/collection');
	return {
		summery: fetchAPI('reports/summery', {date}),
		isLoading: isRequestingFetchAPI('reports/summery', {date}),
	}
})(Summery);

