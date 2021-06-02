/**
 * WordPress dependencies
 */
import {createRef, Component} from '@wordpress/element';
/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import classnames from 'classnames';
import moment from 'moment';
import { isEmpty } from 'lodash';
import $ from 'jquery';

/**
 * Internal dependencies
 */

class DateControl extends Component{
	constructor(props) {
		super(props);
		this.datepickerContainer = createRef();
	}
	componentDidMount() {
		$(this.datepickerContainer.current).datepicker({
			onSelect: this.props.onDateChange,
			defaultDate: this.props.initialDate
		});
	}
	componentWillUnmount() {
		$(this.datepickerContainer.current).datepicker("destroy");
	}

	// const datepickerContainer = createRef();
	// useEffect(() => {
	// 	window.$(this.datepickerContainer.current).datepicker({
	// 		onSelect: props.onDateChange,
	// 		defaultDate: props.initialDate
	// 	});
	// }, []);
	// console.log(datepickerContainer)
	// return <div className="DatePicker" ref={datepickerContainer}>date</div>;

	render(){
		return <div className="DatePicker" ref={this.datepickerContainer} />;
	}
}

export default DateControl;
