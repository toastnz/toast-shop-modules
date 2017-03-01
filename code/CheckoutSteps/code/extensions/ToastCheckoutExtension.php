<?php

/**
 * Class CheckoutPageControllerExtension
 *
 * @property CheckoutPage_Controller $owner
 */
class CheckoutPageControllerExtension extends Extension
{
    public function updateOrderForm(PaymentForm $form)
    {
        /** =========================================
         * @var CheckoutComponentConfig $config
         * @var CheckoutComponent       $component
         * @var FieldList               $cfields
         * ========================================*/

        if (SiteConfig::current_site_config()->EnableCheckoutSteps) {

            $config = Injector::inst()->create("CheckoutComponentConfig", ShoppingCart::curr());

            $fields = FieldList::create();
            $pos    = 1;
            foreach ($config->getComponents() as $component) {

                $cname = $component->Name();

                if ($cfields = $component->getFormFields($config->getOrder())) {

                    if ($cfields->count()) {

                        /** -----------------------------------------
                         * Header
                         * ----------------------------------------*/

                        $componentData = ArrayData::create([
                            'Title'      => preg_replace('/([a-z])([A-Z])/s', '$1 $2', str_replace('CheckoutComponent', '', $cname)),
                            'StepNumber' => $pos
                        ]);

                        $cfields->unshift(LiteralField::create(
                            $cname . 'Header',
                            $this->owner->customise($componentData)->renderWith('CheckoutComponentHeader')->forTemplate()
                        ));

                        /** -----------------------------------------
                         * Wrapper
                         * ----------------------------------------*/

                        $cfields->unshift(LiteralField::create($cname . 'Wrapper', '<div id="' . $cname . '_wrapper' . '">'));

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

            $form->setFields($fields);
        }
    }
}
