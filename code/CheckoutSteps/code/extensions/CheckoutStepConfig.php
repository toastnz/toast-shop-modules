<?php

/**
 * Class CheckoutStepConfig
 *
 * @property bool                          EnableCheckoutSteps
 * @property string                        EnabledSteps
 * @property SiteConfig|CheckoutStepConfig $owner
 */
class CheckoutStepConfig extends DataExtension
{
    private static $db = [
        'EnableCheckoutSteps' => 'Boolean',
        'EnabledSteps'        => 'Text'
    ];

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        /** =========================================
         * @var HtmlEditorField $content
         * @var CheckboxField   $enableSteps
         * @var CheckboxField   $warrantyStep
         * @var CheckboxField   $accessoryStep
        ===========================================*/

        if (Permission::check('ADMIN')) {

            if (!$fields->fieldByName('Root.Toast')) {
                $fields->addFieldToTab('Root', TabSet::create('Toast', 'Shop Settings'));
            }

            /** -----------------------------------------
             * Checkout steps
             * ----------------------------------------*/

            $fields->findOrMakeTab('Root.Toast.CheckoutSteps', 'Checkout Steps');

            $fields->addFieldsToTab('Root.Toast.CheckoutSteps', [
                HeaderField::create('Checkout Steps'),
                CheckboxField::create('EnableCheckoutSteps', 'Enable checkout steps?')
            ]);

            if ($this->owner->EnableCheckoutSteps) {

                $fields->addFieldsToTab('Root.Toast.CheckoutSteps', [
                    CheckboxSetField::create('EnabledSteps', 'Enabled checkout steps', [
                        'Membership'      => 'Membership',
                        'CustomerDetails' => 'Contact Details',
                        'ShippingAddress' => 'Shipping Address',
                        'BillingAddress'  => 'Billing Address',
                        'Shipping'        => 'Shipping Method',
                        'Accessories'     => 'Related Accessories',
                        'Payment'         => 'Payment Method',
                        'Notes'           => 'Notes',
                        'Terms'           => 'Terms and Conditions',
                    ])
                ]);
            }
        }
    }
}
