<?php

use SilverStripe\Omnipay\GatewayInfo;

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

        if ($siteConfig->CheckoutSteps()) {

            $steps = $siteConfig->CheckoutSteps()->filter(['Enabled' => 1])->column('Type');
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
         * @var CheckoutComponent_Namespaced $component
         * @var FieldList                    $cfields
         * ========================================*/

        if (SiteConfig::current_site_config()->EnableCheckoutSteps) {

            $fields = FieldList::create();
            $pos    = 1;

            foreach ($this->getComponents() as $component) {

                $cname = $component->Name();

                // Set descriptions on Address
                if ($component->Proxy() instanceof AddressCheckoutComponent) {
                    $component->Proxy()->setShowFormFieldDescriptions(self::config()->get('formfielddescriptions'));
                }

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

                        $cfields->unshift(LiteralField::create($cname . 'Wrapper', '<div id="' . $cname . '_wrapper" data-api-url="' . ShopAPIConfig::getApiUrl() . '/component/' . str_replace('CheckoutComponent', '', $cname) . '">'));

                        /** -----------------------------------------
                         * Component-specific Options
                         * ----------------------------------------*/

                        $cfields->merge($component->Proxy()->getAdditionalComponentFields());

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

                        $pos++;

                    }

                    // Merge fields
                    $fields->merge($cfields);

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
