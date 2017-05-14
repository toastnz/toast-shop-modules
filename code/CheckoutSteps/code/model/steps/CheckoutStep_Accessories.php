<?php

class CheckoutStep_Accessories extends CheckoutStep
{
    private static $allowed_actions = [
        'accessories',
        'AccessoriesForm',
    ];

    public function accessories()
    {
        $form = $this->AccessoriesForm();
        $data = [
            'OrderForm' => $form,
        ];

        if (Director::is_ajax()) {
            return $this->owner->customise(
                [
                    'OrderForm' => $form,
                ]
            )->renderWith(
                ["CheckoutStep"]
            );
        }

        return $data;
    }

    public function AccessoriesForm()
    {
        $cart = ShoppingCart::curr();
        if (!$cart) {
            return false;
        }
        $config = new CheckoutComponentConfig(ShoppingCart::curr());
        $config->addComponent(AccessoriesCheckoutComponent::create());
        $form = CheckoutForm::create($this->owner, 'AccessoriesForm', $config);
        $form->setRedirectLink($this->NextStepLink());
        $form->setActions(
            FieldList::create(
                FormAction::create("checkoutSubmit", _t('CheckoutStep.Continue', "Continue"))
            )
        );
        $this->owner->extend('updateAccessoriesForm', $form);

        return $form;
    }
}
