<?php
use SilverStripe\Omnipay\GatewayInfo;

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
                        'Payment'         => 'Payment Method',
                        'Notes'           => 'Notes',
                        'Terms'           => 'Terms and Conditions',
                    ])
                ]);
            }
        }
    }
}

/**
 * Class ToastCheckoutComponentConfig
 */
class ToastCheckoutComponentConfig extends CheckoutComponentConfig
{
    public function __construct(Order $order)
    {
        /** =========================================
         * @var CheckoutStepConfig|SiteConfig $siteConfig
         * ========================================*/

        parent::__construct($order);

        $siteConfig = SiteConfig::current_site_config();

        if ($siteConfig->EnableCheckoutSteps) {

            $steps = explode(',', $siteConfig->EnabledSteps);
            $steps = array_filter($steps);

            if (!empty($steps)) {
                foreach ($steps as $step) {
                    if (class_exists($step . 'CheckoutComponent')) {
                        if ($step == 'Membership') {
                            if (Checkout::member_creation_enabled() && !Member::currentUserID()) {
                                $this->addComponent(call_user_func($step . 'CheckoutComponent::create'));
                            }
                            continue;
                        } elseif ($step == 'Payment') {
                            if (count(GatewayInfo::getSupportedGateways()) > 1) {
                                $this->addComponent(call_user_func($step . 'CheckoutComponent::create'));
                            }
                        } else {
                            $this->addComponent(call_user_func($step . 'CheckoutComponent::create'));
                        }
                    }
                }
            }
        }
    }

    public function getFormFields()
    {
        /** =========================================
         * @var CheckoutComponent       $component
         * @var FieldList               $cfields
         * ========================================*/

        if (SiteConfig::current_site_config()->EnableCheckoutSteps) {


            $fields = FieldList::create();
            $pos    = 1;

            foreach ($this->getComponents() as $component) {

                $cname = $component->Name();

                if ($cfields = $component->getFormFields($this->order)) {

                    if ($cfields->count()) {

                        /** -----------------------------------------
                         * Header
                         * ----------------------------------------*/

                        $componentTitle = preg_replace('/([a-z])([A-Z])/s', '$1 $2', str_replace('CheckoutComponent', '', $cname));

                        $componentData = ArrayData::create([
                            'Title'      => _t('TOASTSHOP.' . $cname . 'Title', $componentTitle),
                            'StepNumber' => $pos
                        ]);

                        $cfields->unshift(LiteralField::create(
                            $cname . 'Header',
                            $this->order->customise($componentData)->renderWith('CheckoutComponentHeader')->forTemplate()
                        ));

                        /** -----------------------------------------
                         * Wrapper
                         * ----------------------------------------*/

                        $cfields->unshift(LiteralField::create($cname . 'Wrapper', '<div id="' . $cname . '_wrapper' . '">'));

                        /** -----------------------------------------
                         * Continue button
                         * ----------------------------------------*/

                        $cfields->push(
                            FormAction::create($cname . '_continue', _t('TOASTSHOP.ContinueButton', 'Continue'))
                                ->setUseButtonTag(true)
                                ->addExtraClass('button button--secondary')
                        );

                        /** -----------------------------------------
                         * Close
                         * ----------------------------------------*/

                        $cfields->push(LiteralField::create($cname . 'Close', '</div>'));

                    }

                    // Merge fields
                    $fields->merge($cfields);
                    $pos++;

                } else {
                    user_error("getFields on  " . $cname . " must return a FieldList");
                }
            }

            return $fields;
        } else {
            $fields = FieldList::create();
            foreach ($this->getComponents() as $component) {
                if ($cfields = $component->getFormFields($this->order)) {
                    $fields->merge($cfields);
                } else {
                    user_error("getFields on  " . get_class($component) . " must return a FieldList");
                }
            }
            return $fields;
        }
    }
}
