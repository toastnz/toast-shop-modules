<?php

/**
 * Class ShippingPage
 */
class ShippingPage extends Page
{
    private static $icon = 'toast-shop-modules/CheckoutSteps/images/icons/shopping-basket--arrow.png';

    public function requireDefaultRecords()
    {
        /** =========================================
         * @var ShippingPage $page
         * ========================================*/

        parent::requireDefaultRecords();

        if (!self::get()->exists() && $this->config()->create_default_pages) {
            $checkout = CheckoutPage::get()->first();
            if (!$checkout) {
                $singleton = singleton('CheckoutPage');
                $singleton->requireDefaultRecords();
                $checkout = CheckoutPage::get()->first();
            }

            $page = self::create(
                array(
                    'Title' => 'Shipping',
                    'URLSegment' => 'shipping',
                    'ShowInMenus' => 0,
                    'ParentID' => $checkout->ID
                )
            );

            $page->write();
            $page->publish('Stage', 'Live');
            $page->flushCache();

            DB::alteration_message('Shipping Page created', 'created');
        }
    }

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
