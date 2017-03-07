<?php

/**
 * Class CheckoutStepConfig
 *
 * @property bool                          EnableCheckoutSteps
 * @property string                        EnabledSteps
 * @property SiteConfig|CheckoutStepConfig $owner
 *
 * @method HasManyList|CheckoutStepObject[] CheckoutSteps()
 */
class CheckoutStepConfig extends DataExtension
{
    private static $db = [
        'EnableCheckoutSteps' => 'Boolean'
    ];

    private static $has_many = [
        'CheckoutSteps' => 'CheckoutStepObject'
    ];

    private static $available_components = [
        'CustomerDetails' => 'Contact Details',
        'ShippingAddress' => 'Shipping Address',
        'BillingAddress'  => 'Billing Address',
        'Accessories'     => 'Related Accessories',
        'Payment'         => 'Payment Method',
        'Notes'           => 'Notes',
        'Terms'           => 'Terms and Conditions'
    ];

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        /** =========================================
         * @var HtmlEditorField $content
         * @var GridFieldConfig $config
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

                $gridField = GridField::create(
                    'CheckoutSteps',
                    'Steps',
                    $this->owner->CheckoutSteps(),
                    GridFieldConfig::create()
                        ->addComponent(new GridFieldButtonRow('before'))
                        ->addComponent(new GridFieldToolbarHeader())
                        ->addComponent(new GridFieldOrderableRows('SortOrder'))
                        ->addComponent(new GridFieldEditableColumns())
                        ->addComponent(new GridFieldDetailForm())
                        ->addComponent(new GridFieldEditButton())
                        ->addComponent(new GridFieldTitleHeader())
                );

                $gridField->getConfig()->getComponentByType('GridFieldEditableColumns')->setDisplayFields([
                    'Title'   => [
                        'title' => 'Title',
                        'field' => 'ReadonlyField'
                    ],
                    'Type'    => [
                        'title' => 'Type',
                        'field' => 'ReadonlyField'
                    ],
                    'Enabled' => function ($record, $column, $grid) {
                        return new DropdownField($column, 'Enabled', [
                            '0' => 'No',
                            '1' => 'Yes'
                        ]);
                    },
                ]);

                $fields->addFieldsToTab('Root.Toast.CheckoutSteps', [
                    $gridField
                ]);
            }
        }
    }

    public function onBeforeWrite()
    {
        $componentTypes = Config::inst()->get('SiteConfig', 'available_components');

        if (is_array($componentTypes) && !empty($componentTypes)) {
            $sort = 0;
            foreach ($componentTypes as $type => $name) {
                $obj = CheckoutStepObject::get()->filter(['Type' => $type, 'ParentID' => $this->owner->ID])->first();
                if (!$obj) {
                    $obj = CheckoutStepObject::create([
                        'Type'      => $type,
                        'Title'     => $name,
                        'Enabled'   => 0,
                        'ParentID'  => $this->owner->ID,
                        'SortOrder' => $sort
                    ]);
                    $obj->write();
                }
                $sort++;
            }
        }

        parent::onBeforeWrite();
    }
}
