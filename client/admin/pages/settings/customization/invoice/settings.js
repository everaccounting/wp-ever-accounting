/**
 * External dependencies
 */
import { Form, Button, Text, Space,  SelectControl } from '@eac/components';
import { useSettings } from '@eac/data';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function Settings (props) {
    const settings = useSettings();


    return (
        <Form
            enableReinitialize
            initialValues={ {
                ...settings?.options,
            } }
            validations={ {

            } }
            onSubmit={ ( values ) => {
                return settings.updateOptions( values );
            } }
        >
            { ( { dirty, isSubmitting, isValid, handleSubmit } ) => (
                <>
                    <Text as="h3" size="14" lineHeight="1.75">
                        { __( 'Invoice Settings', 'wp-ever-accounting' ) }
                    </Text>
                    <Text as="p" style={ { marginBottom: '20px' } } color="gray">
                        { __(
                            'Customize how your invoice number gets generated automatically when you create a new invoice.',
                            'wp-ever-accounting'
                        ) }
                    </Text>
                    <Space size="medium" direction="vertical" style={ { display: 'flex' } }>
                        <Form.Field.Input
                            name="number_prefix"
                            label={ __( 'Number Prefix', 'wp-ever-accounting' ) }
                            placeholder={ __( 'e.g. INV-', 'wp-ever-accounting' ) }
                        />
                        <Form.Field.Input
                            name="minimum_digits"
                            label={ __( 'Minimum Digits', 'wp-ever-accounting' ) }
                            placeholder={ __( 'e.g. 4', 'wp-ever-accounting' ) }
                        />
                        <Form.Field.Input
                            name="due_date"
                            label={ __( 'Due Date', 'wp-ever-accounting' ) }
                            placeholder={ __( 'e.g. 30', 'wp-ever-accounting' ) }
                        />
                        <Form.Field.Select
                            name="retrospective_field"
                            label={ __( 'Retrospective Edits', 'wp-ever-accounting' ) }
                            help={ __(
                                'Select an option for the retrospective field.',
                                'wp-ever-accounting'
                            ) }
                            options={ [
                                {
                                    value: 'partial',
                                    label: __(
                                        'Disable after partial payment',
                                        'wp-ever-accounting'
                                    ),
                                },
                                {
                                    value: 'paid',
                                    label: __(
                                        'Disable after paid',
                                        'wp-ever-accounting'
                                    ),
                                },
                                {
                                    value: 'sent',
                                    label: __(
                                        'Disable after sent',
                                        'wp-ever-accounting'
                                    ),
                                }
                            ] }
                        />
                    </Space>

                    <Text as="h3" size="14" lineHeight="1.75">
                        { __( 'Invoice Defaults', 'wp-ever-accounting' ) }
                    </Text>
                    <Text as="p" style={ { marginBottom: '20px' } } color="gray">
                        { __(
                            'Customize the default values of your invoices.',
                            'wp-ever-accounting'
                        ) }
                    </Text>

                        <Form.Field.Textarea
                            name="invoice_notes"
                            label={ __( 'Notes', 'wp-ever-accounting' ) }
                            placeholder={ __( 'e.g. Thank you for your business!', 'wp-ever-accounting' ) }
                        />



                        <Text as="p" size="14" lineHeight="1.75" style={ { marginTop: '20px' } }>
                            { __( 'Invoice Columns', 'wp-ever-accounting' ) }
                        </Text>
                        <Text as="p" style={ { marginBottom: '20px' } } color="gray">
                            { __(
                                'Customize the columns of your invoices.',
                                'wp-ever-accounting'
                            ) }
                        </Text>
                    <Space size="medium" direction="vertical" style={ { display: 'flex' } }>
                        <Form.Field.Input
                            name="invoice_itemlabel"
                            label={ __( 'Item Label', 'wp-ever-accounting' ) }
                            placeholder={ __( 'e.g. Items', 'wp-ever-accounting' ) }
                        />

                        <Form.Field.Input
                            name="price_label"
                            label={ __( 'Price Label', 'wp-ever-accounting' ) }
                            placeholder={ __( 'e.g. Price', 'wp-ever-accounting' ) }
                        />
                        <Form.Field.Input
                            name="quantity_label"
                            label={ __( 'Quantity Label', 'wp-ever-accounting' ) }
                            placeholder={ __( 'e.g. Qty', 'wp-ever-accounting' ) }
                        />
                        <Form.Field.Input
                            name="discount_label"
                            label={ __( 'Discount Label', 'wp-ever-accounting' ) }
                            placeholder={ __( 'e.g. 44', 'wp-ever-accounting' ) }
                        />

                        <Button
                            onClick={ handleSubmit }
                            disabled={ ! dirty || isSubmitting || ! isValid }
                            isPrimary
                        >
                            { __( 'Save Changes', 'wp-ever-accounting' ) }
                        </Button>
                    </Space>
                </>
            ) }
        </Form>

    )

}

export default Settings;
