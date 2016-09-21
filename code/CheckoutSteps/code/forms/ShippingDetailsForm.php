<?php

/**
 * Class ShippingDetailsForm
 */
class ShippingDetailsForm extends Form
{
    private static $allowed_children = array(
        'save'
    );

    public function __construct($controller, $name)
    {
        /** =========================================
         * @var Order $order
         * ========================================*/

        $cart  = ShoppingCart::singleton();
        $order = $cart->current();

        if ($order && $order->Items()->count()) {
            $shipping  = new ShippingAddressCheckoutComponent();
            $fields    = $shipping->getFormFields($order);
            $validator = new RequiredFields($shipping->getRequiredFields($order));
            $actions   = FieldList::create(
                FormAction::create(
                    'save',
                    'Continue'
                )->setUseButtonTag(true)->addExtraClass('btn btn-primary')
            );
        } else {
            $fields    = new FieldList(array(
                LiteralField::create('EmptyCart', '<p>You have nothing added on your cart</p>')
            ));
            $validator = null;
            $actions   = new FieldList();
        }

        $fields->insertBefore('Country_readonly', TextField::create('FirstName', 'First Name'));
        $fields->insertBefore('Country_readonly', EmailField::create('Email', 'Email'));

        $fields->push(HiddenField::create('Description', '', $order->getReferenceForCart()));

        parent::__construct($controller, $name, $fields, $actions, $validator);

        $this->setTemplate('ShippingDetailsForm');

    }

    public function save($data, $form)
    {
        /** =========================================
         * @var Order $order
         * @var SiteConfig|CheckoutStepConfig $config
         * ========================================*/

        $cart  = ShoppingCart::singleton();
        $order = $cart->current();

        if ($order && $order->Items()->count()) {
            // Set the user details
            if (isset($data['FirstName'])) {
                $order->setField('FirstName', $data['FirstName']);
                $order->write();
            }

            if (isset($data['Email'])) {
                $order->setField('Email', $data['Email']);
                $order->write();
            }

            $shipping = new ShippingAddressCheckoutComponent();
            $shipping->setData($order, $data);

            $config = SiteConfig::current_site_config();

            $nextPage = null;

            if ($config->AddWarrantyStep && ($warrantyPage = WarrantyPage::get()->first())) {
                $nextPage = $warrantyPage;
            } else if ($config->AddAccessoriesStep && ($accessoriesPage = AccessoriesPage::get()->first())) {
                $nextPage = $accessoriesPage;
            } else {
                $nextPage = ReviewOrderPage::get()->first();
            }

            if ($nextPage) {
                return $this->controller->redirect($nextPage->Link());
            }

        }
        return $this->controller->httpError(404);

    }

}
