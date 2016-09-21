<?php

/**
 * Class ToastCheckoutExtension
 */
class ToastCheckoutExtension extends Extension
{
    public function getShippingLink()
    {
        /** =========================================
         * @var ShippingPage $initialStep
         * ========================================*/

        if (SiteConfig::current_site_config()->EnableCheckoutSteps) {
            $initialStep = SiteTree::get_one('ShippingPage');
            if ($initialStep && $initialStep->exists()) {
                return $initialStep->Link();
            }
        }

        return CheckoutPage::find_link();
    }

    public function onBeforeInit()
    {
        $key = Config::inst()->get('SiteConfig', 'google_api_key');
        Requirements::javascript('https://maps.googleapis.com/maps/api/js?key=' . $key);
        Requirements::javascript(Controller::join_links(TOAST_MODULES_DIR,  'toast-shop-modules/javascript/toast-shop.js'));
    }
}
