<?php

/**
 * Class ShippingPage
 */
class ShippingPage extends Page
{
    private static $icon = 'toast-shop-modules/CheckoutSteps/images/icons/shopping-basket--arrow.png';
}

/**
 * Class ShippingPage_Controller
 *
 * @property ShippingPage dataRecord
 * @method ShippingPage data()
 * @mixin ShippingPage dataRecord
 */
class ShippingPage_Controller extends Page_Controller
{

	private static $allowed_actions = array(
		'ShippingForm'
	);

	public function ShippingForm()
	{
		$form = new ShippingDetailsForm($this, 'ShippingForm');

        $form->addExtraClass('form');

        return $form;
	}

}
