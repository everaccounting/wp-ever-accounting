import {Component, Fragment} from '@wordpress/element';
import {__} from '@wordpress/i18n';
import {Field, Form} from "react-final-form";
import {
	SelectControl,
	ContactSelect,
	CategorySelect,
	Button,
	CompactCard
} from "@eaccounting/components";

const now = new Date().getUTCFullYear();
const years = Array(now - (now - 20)).fill('').map((v, idx) => now - idx);

export default class IncomeReportFilter extends Component {
	constructor(props) {
		super(props);
		this.submitForm = this.submitForm.bind(this);
	}

	submitForm(data) {
		data.customer_id = data && data.customer && data.customer.id && data.customer.id;
		data.customer_id = data && data.category && data.category.id && data.category.id;
		delete data.customer;
		delete data.category;

		this.props.onFilter && this.props.onFilter(data);
	}

	render() {
		return (
			<Fragment>
				<CompactCard className="ea-report-filter-bar">
					<Form
						onSubmit={this.submitForm}
						initialValues={{}}
						keepDirtyOnReinitialize
						render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
							<form onSubmit={handleSubmit} className="ea-row">
								<Field
									className="ea-col-2"
									name="year"
									options={years.map(year => ({label: year, value: year}))}
								>
									{props => <SelectControl {...props.input} {...props} />}
								</Field>

								<Field
									className="ea-col-2"
									name="customer"
								>
									{props => <ContactSelect type='customer' {...props.input} {...props} />}
								</Field>
								<Field
									className="ea-col-2"
									name="category"
								>
									{props => <CategorySelect type='income' {...props.input} {...props} />}
								</Field>

								<Button isPrimary compact disabled={submitting || pristine}
										type="submit">{__('Filter')}</Button>
								<Button secondary compact disabled={submitting || pristine}
										onClick={form.reset}>{__('Reset')}</Button>
							</form>
						)}
					/>
				</CompactCard>
			</Fragment>
		)
	}
}
