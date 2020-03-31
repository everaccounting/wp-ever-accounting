import {Component} from "@wordpress/element"
import {compose} from "@wordpress/compose";
import {withSelect, withDispatch} from "@wordpress/data";
import {TableNav, TextControl} from "@eaccounting/components";
import {addQueryArgs} from "@wordpress/url";
const Insert = (props) => {
	const {account} = props;
	console.log(account);
	console.log(account.forInsert);
	return (
		<div>
			<TextControl label={'Name'}
			value={account.name}
			onChange={account.setName}/>
		</div>
	)
};
class Table extends Component {
	render() {

		const {items, total, status, page = 1, modelLoaded, model} = this.props;
		console.log(model);
		return (
			<div>
				{/*<TableNav*/}
				{/*	status={status}*/}
				{/*	total={total}*/}
				{/*	page={page}*/}
				{/*	selected={[]}*/}
				{/*	onChangePage={(page) => console.log(page)}*/}
				{/*	onAction={(action) => (action)}*/}
				{/*/>*/}
				{modelLoaded && <Insert account={model.createNew({name:'hello'})}/>}

				{JSON.stringify(this.props)}
			</div>
		)
	}
}

export default compose([
	withSelect((select) => {
		const {getCategories, getCategoriesTotal, isRequestingCategories} = select('ea/collection');
		const {getQuery} = select('ea/query');
		const query = addQueryArgs('', getQuery('contacts'));
		const {getAccountFactory, isRequestingAccountFactory} = select('ea/schema');
		return {
			items: getCategories(query),
			total: getCategoriesTotal(query) || 0,
			status: isRequestingCategories(query) === true ? "STATUS_IN_PROGRESS" : "STATUS_COMPLETE",
			model: getAccountFactory(),
			modelLoaded: isRequestingAccountFactory() !== true
		}
	}),
	withDispatch((dispatch) => {
		const {setPage} = dispatch('ea/query');
		return {
			setPage,
		}
	}),
])(Table);
