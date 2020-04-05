/**
 * WordPress dependencies
 */
import {Component} from '@wordpress/element';
import {createHigherOrderComponent, compose} from '@wordpress/compose';
import {withDispatch, withSelect} from '@wordpress/data';
import {__} from "@wordpress/i18n";
import {PER_PAGE} from "@eaccounting/data";
import withTableNavigation from "./withTableNavigation";
import {addQueryArgs} from "@wordpress/url"
import isShallowEqual from '@wordpress/is-shallow-equal';
import {xor} from 'lodash';
import qs from "querystring";


const withTable = (resourceName, initQuery = {}) => {
	if (!resourceName)
		throw 'No resourceName in child component';
	return createHigherOrderComponent(WrappedComponent => {
		class Hoc extends Component {
			constructor(props) {
				super(props);
				this.state = {
					selected: [],
					total: 0,
				};
			}

			componentDidUpdate(prevProps) {
				if (!isNaN(this.props.total) && !isShallowEqual(this.state.total, this.props.total)) {
					this.setState({
						total: this.props.total,
					});
				}
			}

			render() {
				// console.group("withTable");
				// console.info(this.props);
				// console.groupEnd();
				return <WrappedComponent
					{...this.props}
					selected={this.state.selected}
					total={this.state.total}/>;
			}

		}

		return compose(
			withTableNavigation(initQuery),
			withSelect((select, ownProp) => {
				const {getCollection, isRequestingGetCollection} = select('ea/collection');
				const {queries = {} } = ownProp;
				const {items = [], total = NaN} = getCollection(resourceName, queries);
				return {
					items: items,
					total: total,
					status: isRequestingGetCollection(resourceName, queries) === true ? "STATUS_IN_PROGRESS" : "STATUS_COMPLETE",
				}
			}),
		)(Hoc)
	}, 'withTable');
};


export default withTable;
