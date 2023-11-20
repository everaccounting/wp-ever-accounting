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
                        { __( 'Payment Settings', 'wp-ever-accounting' ) }
                    </Text>
                    <Text as="p" style={ { marginBottom: '20px' } } color="gray">
                        { __(
                            'Customize how your payment number gets generated automatically when you create a new payment.',
                            'wp-ever-accounting'
                        ) }
                    </Text>
                    <Space size="medium" direction="vertical" style={ { display: 'flex' } }>
                        <Form.Field.Input
                            name="number_prefix"
                            label={ __( 'Number Prefix', 'wp-ever-accounting' ) }
                            placeholder={ __( 'e.g. PAY-', 'wp-ever-accounting' ) }
                        />
                        <Form.Field.Input
                            name="minimum_digits"
                            label={ __( 'Minimum Digits', 'wp-ever-accounting' ) }
                            placeholder={ __( 'e.g. 4', 'wp-ever-accounting' ) }
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
