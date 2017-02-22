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
        'EnabledSteps'        => 'Varchar'
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
                        'ContactDetails'  => 'Contact Details',
                        'ShippingAddress' => 'Shipping Address',
                        'BillingAddress'  => 'Billing Address',
                        'ShippingMethod'  => 'Shipping Method',
                        'PaymentMethod'   => 'Payment Method'
                    ])
                ]);
            }
        }
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();

        $configFile = Controller::join_links(Director::baseFolder(), TOAST_MODULES_DIR, '_config/shop_gen.yml');

        $fileHeader = <<<yml
---
Name: shopgen
After: 'framework/*','cms/*'
---
yml;
        file_put_contents($configFile, $fileHeader);

        if ($this->owner->EnableCheckoutSteps) {

            $steps = $this->owner->getStepsArray();


            if (!empty($steps)) {
                $fullFileContents = <<<yml
---
Name: shopgen
After: 'framework/*','cms/*'
---
CheckoutPage:
  steps:\r
yml;
                foreach ($steps as $step => $stepClass) {
                    $fullFileContents .= sprintf("    %s: %s\r", $step, $stepClass);
                }
                // Always tack on summary
                $fullFileContents .= "    summary: CheckoutStep_Summary";
                file_put_contents($configFile, $fullFileContents);

            }
        }

        exec('php framework/cli-script.php dev/build');
    }

    public function getStepsArray()
    {
        $list = explode(',', $this->owner->EnabledSteps);

        $steps = [];

        foreach ($list as $step) {
            if ($step == 'ShippingAddress' || $step == 'BillingAddress') {
                $steps[strtolower($step)] = 'CheckoutStep_Address';
                continue;
            }
            if (class_exists('CheckoutStep_' . $step)) {
                $steps[strtolower($step)] = 'CheckoutStep_' . $step;
            }
        }

        // Use default steps / config
        if (empty($steps)) {
            return null;
        }

        return $steps;
    }

    /**
     * Link list for templates
     *
     * @return ArrayList
     */
    public function getCheckoutStepList()
    {
        /** =========================================
         * @var ArrayList       $list
         * @var CartPage        $checkout
         * @var ShippingPage    $shipping
         * @var WarrantyPage    $warranty
         * @var AccessoriesPage $accessories
         * @var ReviewOrderPage $reviewPage
         * ========================================*/

        $list = ArrayList::create();

        /** -----------------------------------------
         * Cart
         * ----------------------------------------*/

        $checkout = CartPage::get()->first();

        if ($checkout && $checkout->exists()) {
            $list->push(ArrayData::create([
                'Title'       => $checkout->Title,
                'MenuTitle'   => $checkout->MenuTitle,
                'LinkingMode' => $checkout->LinkingMode(),
                'Link'        => $checkout->Link()
            ]));
        }

        /** -----------------------------------------
         * Shipping
         * ----------------------------------------*/

        $shipping = ShippingPage::get()->first();

        if ($shipping && $shipping->exists()) {
            $list->push(ArrayData::create([
                'Title'       => $shipping->Title,
                'MenuTitle'   => $shipping->MenuTitle,
                'LinkingMode' => $shipping->LinkingMode(),
                'Link'        => $shipping->Link()
            ]));
        }

        /** -----------------------------------------
         * Warranty
         * ----------------------------------------*/

        if ($this->owner->AddWarrantyStep) {

            $warranty = WarrantyPage::get()->first();

            if ($warranty && $warranty->exists()) {
                $list->push(ArrayData::create([
                    'Title'       => $warranty->Title,
                    'MenuTitle'   => $warranty->MenuTitle,
                    'LinkingMode' => $warranty->LinkingMode(),
                    'Link'        => $warranty->Link()
                ]));
            }
        }

        /** -----------------------------------------
         * Accessories
         * ----------------------------------------*/

        if ($this->owner->AddAccessoriesStep) {

            $accessories = AccessoriesPage::get()->first();

            if ($accessories && $accessories->exists()) {
                $list->push(ArrayData::create([
                    'Title'       => $accessories->Title,
                    'MenuTitle'   => $accessories->MenuTitle,
                    'LinkingMode' => $accessories->LinkingMode(),
                    'Link'        => $accessories->Link()
                ]));
            }
        }

        /** -----------------------------------------
         * Review
         * ----------------------------------------*/

        $reviewPage = ReviewOrderPage::get()->first();

        if ($reviewPage && $reviewPage->exists()) {
            $list->push(ArrayData::create([
                'Title'       => $reviewPage->Title,
                'MenuTitle'   => $reviewPage->MenuTitle,
                'LinkingMode' => $reviewPage->LinkingMode(),
                'Link'        => $reviewPage->Link()
            ]));
        }

        return $list;
    }
}
