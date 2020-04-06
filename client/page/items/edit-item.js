import {Component, Fragment} from '@wordpress/element';
import PropTypes from 'prop-types';
import {__} from '@wordpress/i18n';
import {Form, Field} from "react-final-form";
import {
	TextControl,
	Icon,
	AccountControl,
	Card,
	CompactCard,
	FileControl,
	Button,
	TextareaControl, PriceControl, DateControl, PaymentMethodControl, CategoryControl, TaxRateControl
} from "@eaccounting/components";
import {withEntity} from "@eaccounting/hoc";
import EditCategory from "../categories/edit-category";
class EditItem extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isOpenCategoryModal:false
		};
		this.openCategoryModal = this.openCategoryModal.bind(this);
		this.closeCategoryModal = this.closeCategoryModal.bind(this);
		this.onSubmit = this.onSubmit.bind(this);
	}

	openCategoryModal(){
		this.setState({
			isOpenCategoryModal:true
		})
	}

	closeCategoryModal(){
		this.setState({
			isOpenCategoryModal:false
		})
	}

	onSubmit(form) {
		console.log(form);
		form.image_id = form.image && form.image.id && form.image.id || null;
		form.category_id = form.category && form.category.id && form.category.id || null;
		form.tax_id = form.tax && form.tax.id && form.tax.id || null;

		const {history, isNew} = this.props;
		delete form.image;
		delete form.category;
		delete form.tax;

		this.props.handleSubmit(form, (data)=> {
			const path = isNew ? `/items/edit/${data.id}` : `/items`;
			history.push(path)
		});
	}


	render() {
		const {isNew, item, settings, history} = this.props;
		const {isOpenCategoryModal} = this.state;
		return (
			<Fragment>
				<CompactCard tagName="h3">{ isNew ? __('New Item') : __('Update Item')}</CompactCard>
				<Card>
					{isOpenCategoryModal && <EditCategory
						onSubmit={(data) => this.props.handleSubmit(data, this.closeCategoryModal, true, 'categories')}
						defaultCategory={'item'}
						onClose={this.closeCategoryModal}
						tittle={__('Add Category')}
						buttonTittle={__('Submit')}/>}
					<Form
						onSubmit={this.onSubmit}
						initialValues={item}
						render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
							<form onSubmit={handleSubmit}>
								<div className="ea-row">

									<Field
										label={__('Name', 'wp-ever-accounting')}
										name="name"
										className="ea-col-6"
										before={<Icon icon={'id-card-o'}/>}
										required>
										{props => (
											<TextControl {...props.input} {...props}/>
										)}
									</Field>
									<Field
										label={__('SKU', 'wp-ever-accounting')}
										name="sku"
										className="ea-col-6"
										before={<Icon icon={'key'}/>}
										required>
										{props => (
											<TextControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Sale Price', 'wp-ever-accounting')}
										name="sale_price"
										className="ea-col-6"
										defaultValue={0}
										code={'USD'}
										before={<Icon icon={'money'}/>}
										required>
										{props => (
											<PriceControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Sale Price', 'wp-ever-accounting')}
										name="purchase_price"
										className="ea-col-6"
										defaultValue={0}
										code={'USD'}
										before={<Icon icon={'money'}/>}
										required>
										{props => (
											<PriceControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Quantity', 'wp-ever-accounting')}
										name="quantity"
										className="ea-col-6"
										defaultValue={0}
										before={<Icon icon={'cubes'}/>}
										required>
										{props => (
											<TextControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Category', 'wp-ever-accounting')}
										name="category"
										className="ea-col-6"
										type="item"
										defaultValue={0}
										hasButton={true}
										before={<Icon icon={'folder-open-o'}/>}
										after={<Icon onClick={this.openCategoryModal} icon={'plus'}/>}
										required>
										{props => (
											<CategoryControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Description', 'wp-ever-accounting')}
										className="ea-col-12"
										name="description">
										{props => (
											<TextareaControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Tax', 'wp-ever-accounting')}
										name="tax"
										className="ea-col-6"
										type="item"
										hasButton={true}
										before={<Icon icon={'percent'}/>}>
										{props => (
											<TaxRateControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Photo', 'wp-ever-accounting')}
										name="image"
										className="ea-col-6"
										hasButton={true}>
										{props => (
											<FileControl {...props.input} {...props}/>
										)}
									</Field>


								</div>
								<p style={{marginTop: '20px'}}>
									<Button
										isPrimary
										disabled={submitting || pristine}
										type="submit">{__('Submit')}
									</Button>
									<Button primary={false} onClick={()=> history.goBack()}>{__('Cancel')}</Button>
								</p>
							</form>
						)}/>
				</Card>
			</Fragment>
		)
	}
}

export default withEntity('items')(EditItem);
