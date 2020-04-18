import {Component, Fragment} from 'react';
import {__, sprintf} from '@wordpress/i18n';
import {withEntity} from "@eaccounting/hoc";
import {
	Card,
	CompactCard,
	SelectControl,
	TextControl,
	BackButton,
	Button,
} from "@eaccounting/components";
import {Form, Field, FormSpy} from "react-final-form";
import {NotificationManager} from "react-notifications";
import {getGlobalCurrencies} from "@eaccounting/data";

class EditCurrency extends Component {
	constructor(props) {
		super(props);
		this.onSubmit = this.onSubmit.bind(this);
	}

	onSubmit(form) {
		const {history, isAdd} = this.props;
		this.props.handleSubmit(form, function (res) {
			NotificationManager.success(sprintf(__('"%s" category %s.'), res.name, isAdd ? __('created') : __('updated')));
			history.push('/settings/categories')
		});
	}

	render() {
		const {isAdd, item} = this.props;
		const currencies = getGlobalCurrencies();
		return (
			<Fragment>
				<CompactCard tagName="h3">{isAdd ? __('Add Category') : __('Update Category')}</CompactCard>
				<Card>
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
										required>
										{props => (
											<TextControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Code', 'wp-ever-accounting')}
										name="code"
										defaultValue={'USD'}
										options={currencies}
										className="ea-col-6"
										required>
										{props => (
											<SelectControl {...props.input} {...props}/>
										)}
									</Field>

									<Field
										label={__('Rate', 'wp-ever-accounting')}
										name="rate"
										className="ea-col-6"
										parse={value => value.replace(/[^\d.]+/g, '')}
										help={__('Rate against default currency. NOTE: Default currency rate is always 1')}
										required>
										{props => (
											<TextControl {...props.input} {...props}/>
										)}
									</Field>

								</div>

								<Button
									isPrimary
									disabled={submitting || pristine}
									type="submit">{__('Submit')}
								</Button>

								<BackButton title={__('Cancel')}/>

							</form>
						)}/>
				</Card>
			</Fragment>
		);
	}
}

export default withEntity('currencies')(EditCurrency);
