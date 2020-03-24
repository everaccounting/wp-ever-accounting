/**
 * External dependencies
 */
import {Fragment} from "@wordpress/element";
import {__} from '@wordpress/i18n';
import General from "./sections/general";
import Company from "./sections/company";
import {withSelect, withDispatch} from "@wordpress/data";
import {compose} from '@wordpress/compose';
import {Spinner} from "@eaccounting/components";


const Sections = (props) => {
	return (
		<Company {...props}/>
	)
};

const Settings = props => {
	const {isComplete} = props;

	return (
		<Fragment>
			<h1 className="wp-heading-inline">{__('Settings')}</h1>
			<hr className="wp-header-end"/>
			<div style={{height:'30px'}}/>
			{!isComplete && <Spinner/>}
			{isComplete && <Sections {...props}/>}
		</Fragment>
	);
};

export default compose([
	withSelect((select) => {
		const {getCollection, getCollectionStatus, getModel, getModelStatus} = select('ea/store');
		return {
			model: getModel('settings'),
			settings: getCollection('settings', {}),
			isComplete: getCollectionStatus('settings', {}) === "STATUS_COMPLETE" && "STATUS_COMPLETE" === getModelStatus('settings'),
		}
	}),
	withDispatch((dispatch) => {

	})
])(Settings);
