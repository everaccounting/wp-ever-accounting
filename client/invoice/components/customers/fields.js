import {Fragment} from "@wordpress/element";
import {TextControl} from "@eaccounting/components";

function CustomerFields({getInputProps, values, errors, handleSubmit}) {
	return (
		<Fragment>
			<TextControl
				required={true}
				label={'Name'}
				{...getInputProps('name')}
			/>
			<TextControl
				label={'Email'}
				type='email'
				{...getInputProps('email')}
			/>
			<TextControl
				label={'Phone'}
				{...getInputProps('phone')}
			/>
			<TextControl
				label={'Fax'}
				{...getInputProps('fax')}
			/>
			<TextControl
				label={'Tax Number'}
				{...getInputProps('tax_number')}
			/>
			<TextControl
				label={'Website'}
				type='url'
				{...getInputProps('website')}
			/>
			<TextControl
				label={'Birth Date'}
				type='url'
				{...getInputProps('birth_date')}
			/>
		</Fragment>
	)
}

export default CustomerFields;
