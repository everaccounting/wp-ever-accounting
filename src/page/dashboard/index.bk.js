/**
 * External dependencies
 */

import {Component} from 'react';
import {translate as __} from 'lib/locale';
import {
	Icon,
	TextControl,
	SelectControl,
	ReactSelect,
	TextareaControl,
	ToggleControl,
	DateRangePicker
} from '@eaccounting/components';
import AccountControl from "component/account-control";
import ContactControl from "component/contact-control";
import CategoryControl from "component/category-control";
import CurrencyControl from "component/currency-control";
// import DateRangeControl from "component/date-range-control";

/**
 * Internal dependencies
 */
import './style.scss';
import moment from "moment";

export default class Dashboard extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isVisible: false,
			start:undefined,
			end:undefined,
		};
		window.addEventListener('popstate', this.onPageChanged);
	}

	componentDidCatch(error, info) {
		this.setState({error: true, stack: error, info});
	}

	componentWillUnmount() {
		window.removeEventListener('popstate', this.onPageChanged);
	}

	render() {
		const {isVisible, start, end} = this.state;
		return (
			<div>
				{/*<DateRangeControl startDate={start} endDate={end} onChange={(start, end)=> {*/}
				{/*	this.setState({*/}
				{/*		start,*/}
				{/*		end*/}
				{/*	})*/}
				{/*}}/>*/}

				{/*<EditAccount item={initialAccount}/>*/}
				<DateRangePicker startDate={undefined} endDate={undefined} ranges={{
					'Today': [moment(), moment()],
					'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
					'Last 7 Days': [moment().subtract(6, 'days'), moment()],
					'Last 30 Days': [moment().subtract(29, 'days'), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
				}}>
					<button>Click Me To Open Picker!</button>
				</DateRangePicker>

				<AccountControl selected={[91, 92]}/>
				<AccountControl
					placeholder={__('Select Account')}
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					selected={[91, 92]}/>

				<AccountControl
					placeholder={__('Select Account')}
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					isMulti
					selected={[91, 92]}/>

				<ContactControl selected={[91, 92]}/>
				<ContactControl
					placeholder={__('Select contact')}
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					selected={[91, 92]}/>

				<ContactControl
					placeholder={__('Select contact')}
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					isMulti
					selected={[91, 92]}/>

				<CategoryControl selected={[91, 92]}/>
				<CategoryControl
					placeholder={__('Select category')}
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					selected={[40, 41]}/>

				<CategoryControl
					placeholder={__('Select contact')}
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					isMulti
					selected={[40, 41]}/>


				<CurrencyControl selected={[1, 2]}/>
				<CurrencyControl
					placeholder={__('Select category')}
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					selected={[1, 2]}/>

				<CurrencyControl
					placeholder={__('Select contact')}
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					isMulti
					selected={[1, 2]}/>



				<TextareaControl
					label="Text field with both affixes"
					help="Text field with both affixes"
					value={'third'}
					required
					onChange={value => setState({third: value})}
				/>

				<TextControl
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					label="Text field with both affixes"
					help="Text field with both affixes"
					value={'third'}
					required
					onChange={value => setState({third: value})}
				/>

				<SelectControl
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					label="Size"
					value={'25%'}
					help="Text field with both affixes"
					options={[
						{label: 'Big', value: '100%'},
						{label: 'Medium', value: '50%'},
						{label: 'Small', value: '25%'},
					]}
					onChange={(size) => {
						this.setState({size})
					}}
				/>

				<ReactSelect
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					label="React Select"
					help="Text field with both affixes"
					options={[
						{label: 'Big', value: '100%'},
						{label: 'Medium', value: '50%'},
						{label: 'Small', value: '25%'},
					]}
					value={{label: 'Medium', value: '50%'}}
					required
					onChange={(size) => {
						this.setState({size})
					}}
				/>

				<ReactSelect
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					label="React Select"
					help="Text field with both affixes"
					options={[
						{label: 'Big', value: '100%'},
						{label: 'Medium', value: '50%'},
						{label: 'Small', value: '25%'},
					]}
					value={{label: 'Medium', value: '50%'}}
					required
					isMulti={true}
					onChange={(size) => {
						this.setState({size})
					}}
				/>

				<CurrencyControl
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					help="Text field with both affixes"
					label={__('Opening Balance')} required/>

				<ToggleControl
					help="Text field with both affixes"
					label={__('Opening Balance')}
					check={'on'}
					value={'on1'}
					onChange={() => setState(state => ({checked: !state.checked}))}
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
