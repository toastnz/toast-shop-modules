<?php

/**
 * Class CheckoutStepConfig
 *
 * @property bool AddWarrantyStep
 * @property bool AddAccessoriesStep
 * @property bool EnableCheckoutSteps
 */
class CheckoutStepConfig extends DataExtension
{
    private static $db = array(
        'EnableCheckoutSteps' => 'Boolean',
        'AddWarrantyStep' => 'Boolean',
        'AddAccessoriesStep' => 'Boolean'
    );

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        /** =========================================
         * @var HtmlEditorField $content
         * @var CheckboxField $enableSteps
         * @var CheckboxField $warrantyStep
         * @var CheckboxField $accessoryStep
        ===========================================*/

        if (Permission::check('ADMIN')) {

            if (!$fields->fieldByName('Root.Toast')) {
                $fields->addFieldToTab('Root', TabSet::create('Toast', 'Shop Settings'));
            }

            /** -----------------------------------------
             * Checkout steps
             * ----------------------------------------*/

            $fields->findOrMakeTab('Root.CDB.CheckoutSteps', 'Checkout Steps');

            $enableSteps = CheckboxField::create('EnableCheckoutSteps', 'Enable checkout steps?');
            $enableSteps->addExtraClass('toast-checkbox');

            $warrantyStep = CheckboxField::create('AddWarrantyStep', 'Add warranty step?');
            $warrantyStep->addExtraClass('toast-checkbox');

            $accessoryStep = CheckboxField::create('AddAccessoriesStep', 'Add accessories step?');
            $accessoryStep->addExtraClass('toast-checkbox');

            $fields->addFieldsToTab('Root.CDB.CheckoutSteps', array(
                HeaderField::create('Checkout Steps'),
                $enableSteps,
                $warrantyStep,
                $accessoryStep
            ));
        }
    }

    /**
     * Link list for templates
     *
     * @return ArrayList
     */
    public function getCheckoutStepList()
    {
        /** =========================================
         * @var ArrayList $list
         * @var CartPage $checkout
         * @var ShippingPage $shipping
         * @var WarrantyPage $warranty
         * @var AccessoriesPage $accessories
         * @var ReviewOrderPage $reviewPage
         * ========================================*/

        $list = ArrayList::create();

        /** -----------------------------------------
         * Cart
         * ----------------------------------------*/

        $checkout = CartPage::get()->first();

        if ($checkout && $checkout->exists()) {
            $list->push(ArrayData::create(array(
                'Title' => $checkout->Title,
                'MenuTitle' => $checkout->MenuTitle,
                'LinkingMode' => $checkout->LinkingMode(),
                'Link' => $checkout->Link()
            )));
        }

        /** -----------------------------------------
         * Shipping
         * ----------------------------------------*/

        $shipping = ShippingPage::get()->first();

        if ($shipping && $shipping->exists()) {
            $list->push(ArrayData::create(array(
                'Title' => $shipping->Title,
                'MenuTitle' => $shipping->MenuTitle,
                'LinkingMode' => $shipping->LinkingMode(),
                'Link' => $shipping->Link()
            )));
        }

        /** -----------------------------------------
         * Warranty
         * ----------------------------------------*/

        if ($this->owner->AddWarrantyStep) {

            $warranty = WarrantyPage::get()->first();

            if ($warranty && $warranty->exists()) {
                $list->push(ArrayData::create(array(
                    'Title' => $warranty->Title,
                    'MenuTitle' => $warranty->MenuTitle,
                    'LinkingMode' => $warranty->LinkingMode(),
                    'Link' => $warranty->Link()
                )));
            }
        }

        /** -----------------------------------------
         * Accessories
         * ----------------------------------------*/

        if ($this->owner->AddAccessoriesStep) {

            $accessories = AccessoriesPage::get()->first();

            if ($accessories && $accessories->exists()) {
                $list->push(ArrayData::create(array(
                    'Title' => $accessories->Title,
                    'MenuTitle' => $accessories->MenuTitle,
                    'LinkingMode' => $accessories->LinkingMode(),
                    'Link' => $accessories->Link()
                )));
            }
        }

        /** -----------------------------------------
         * Review
         * ----------------------------------------*/

        $reviewPage = ReviewOrderPage::get()->first();

        if ($reviewPage && $reviewPage->exists()) {
            $list->push(ArrayData::create(array(
                'Title' => $reviewPage->Title,
                'MenuTitle' => $reviewPage->MenuTitle,
                'LinkingMode' => $reviewPage->LinkingMode(),
                'Link' => $reviewPage->Link()
            )));
        }

        return $list;
    }
}
