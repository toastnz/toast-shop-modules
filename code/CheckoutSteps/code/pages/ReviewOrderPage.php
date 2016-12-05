<?php

/**
 * Class ReviewOrderPage
 *
 * @property string CheckoutContent
 */
class ReviewOrderPage extends Page
{
    private static $icon = 'toast-shop-modules/CheckoutSteps/images/icons/shopping-basket--arrow.png';

    private static $db = array(
        'CheckoutContent' => 'HTMLText'
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName(array(
            'Content'
        ));

        $fields->addFieldToTab('Root.Main', HtmlEditorField::create('CheckoutContent')->setRows(5));

        return $fields;
    }
}

/**
 * Class ReviewOrderPage_Controller
 *
 * @property ReviewOrderPage dataRecord
 * @method ReviewOrderPage data()
 * @mixin ReviewOrderPage dataRecord
 * @mixin CouponFormCheckoutDecorator
 */
class ReviewOrderPage_Controller extends Page_Controller
{
    private static $allowed_actions = array(
        'remove',
        'update'
    );

    public function CheckoutForm()
    {
        /** =========================================
         * @var CheckoutComponentConfig $config
         * ========================================*/
        if (!(bool)$this->Cart()) {
            return false;
        }

        $config = Injector::inst()->create("CheckoutComponentConfig", ShoppingCart::curr());

        $checkoutPage           = CheckoutPage::get()->first();
        $checkoutPageController = new CheckoutPage_Controller($checkoutPage);

        $form = new PaymentForm($checkoutPageController, 'OrderForm', $config);

        // Normally, the payment is on a second page, either offsite or through /checkout/payment
        // If the site has customised the checkout component config to include an onsite payment
        // component, we should honor that and change the button label. PaymentForm::checkoutSubmit
        // will also check this and process payment if needed.
        if ($config->getComponentByType('OnsitePaymentCheckoutComponent')) {
            $form->setActions(
                new FieldList(
                    FormAction::create('checkoutSubmit', _t('CheckoutForm.SubmitPayment', 'Proceed to payment'))
                )
            );
        } else {
            $form->setActions(
                new FieldList(
                    FormAction::create('checkoutSubmitCustom', _t('CheckoutForm.SubmitPayment', 'Proceed to payment'))
                )
            );
        }

        $form->Cart = $this->Cart();
        $form->setTemplate('ReviewOrderForm');

        $fields = $form->Fields();

        $fields->insertBefore(LiteralField::create('YD_S', '<div class="formHeader">Your Details</div><div class="formSection">'), 'CustomerDetailsCheckoutComponent_FirstName');


        $fields->insertBefore(LiteralField::create('CD_S', '</div><div class="formHeader">Shipping Address & Contact Information</div><div class="formSection">'), 'ShippingAddressCheckoutComponent_Country_readonly');
        $fields->insertBefore(LiteralField::create('CD_E', '</div><div class="formHeader">Shipping Address & Contact Information</div><div class="formSection">'), 'ShippingAddressCheckoutComponent_Country');

        $fields->insertBefore(LiteralField::create('BD_S', '</div><div class="formHeader">Billing Address</div><div class="formSection">'), 'BillingAddressCheckoutComponent_Country_readonly');
        $fields->insertBefore(LiteralField::create('BD_E', '</div><div class="formHeader">Billing Address</div><div class="formSection">'), 'BillingAddressCheckoutComponent_Country');


        $fields->insertBefore(LiteralField::create('N_S', '</div><div class="formHeader">Notes</div><div class="formSection">'), 'NotesCheckoutComponent_Notes');
        $fields->push(LiteralField::create('N_S', '</div>'));

        // Checkbox for copying field values
        $oCopyExistingAddressGroup = FieldGroup::create('',
            CheckboxField::create('CopyExistingAddress', 'Copy Shipping Address')
        );
        $fields->insertBefore($oCopyExistingAddressGroup, 'BillingAddressCheckoutComponent_Country');
        $fields->insertBefore($oCopyExistingAddressGroup, 'BillingAddressCheckoutComponent_Country_readonly');


        $fields->push(HiddenField::create('Description', '', $form->orderProcessor->getOrder()->getReferenceForCart()));

        $actions = $form->Actions();
        foreach ($actions as $action) {
            $action->setUseButtonTag(true);
            $action->addExtraClass('btn btn-primary');
        }

        $form->ReviewPage = $this->data();

        $form->addExtraClass('form');

        return $form;
    }

    public function remove()
    {
        $hash  = $this->getRequest()->param('ID');
        $cart  = ShoppingCart::singleton();
        $order = $cart->current();
        if ($order) {
            foreach ($order->Items() as $orderItem) {
                if ($orderItem->DeleteHash() == $hash) {
                    $cart->remove($orderItem->Buyable());
                }
            }
        }
        return $this->redirectBack();
    }


    public function update()
    {
        $hash  = $this->getRequest()->param('ID');
        $cart  = ShoppingCart::singleton();
        $order = $cart->current();
        if ($order) {
            foreach ($order->Items() as $orderItem) {
                if ($orderItem->DeleteHash() == $hash) {
                    $iDiff = (int)$_REQUEST['Quantity'] - $orderItem->Quantity;
                    if ($iDiff > 0) {
                        $cart->add($orderItem->Buyable(), $iDiff);
                    } else {
                        $cart->remove($orderItem->Buyable(), -1 * $iDiff);
                    }
                }
            }

            $order->calculate();
            $order->write();

            $ad = new ArrayData(array(
                'Cart' => $order,
                'ReviewPage' => ReviewOrderPage::get()->first()
            ));
            return $ad->renderWith('ReviewPageCart');
        }
        return 0;

    }

}
