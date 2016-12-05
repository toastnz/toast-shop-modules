<?php

/**
 * Class WarrantyPage
 */
class WarrantyPage extends Product implements Buyable
{
    private static $singular_name = "Warranty Option";
    private static $plural_name = "Warranty Options";

    private static $icon = 'toast-shop-modules/CheckoutSteps/images/icons/shopping-basket--arrow.png';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName(array(
            'Model',
            'HeroImage',
            'HeroText',
            'Category',
            'ProductCategories',
            'Featured'
        ));

        return $fields;
    }

}

/**
 * Class WarrantyPage_Controller
 *
 * @property WarrantyPage dataRecord
 * @method WarrantyPage data()
 * @mixin WarrantyPage dataRecord
 */
class WarrantyPage_Controller extends Product_Controller
{

    private static $allowed_actions = array(
        'choose'
    );

    public function choose()
    {
        /** =========================================
         * @var Order $order
         * @var SiteConfig|CheckoutStepConfig $config
         * ========================================*/

        $cart  = ShoppingCart::singleton();
        $order = $cart->current();

        $this->removeAllWarranties($cart);
        $iMachinesCount = 0;
        if ($order) {
            foreach ($order->Items() as $item) {
                $buyable = $item->Buyable();
                $product = $buyable;
                if (is_a($buyable, 'ProductVariation')) {
                    $product = $buyable->Product();
                }
                if (is_a($product, 'MagicBullet')) {
                    $iMachinesCount += $item->Quantity;
                }
            }
        }

        $iSelected = $_POST['selected_id'];
        $selected  = ProductVariation::get()->byID((int)$iSelected);
        if ($selected) {
            $cart->add($selected, $iMachinesCount);
        }

        if ($order) {
            $order->calculate();
            $order->write();
        }

        $nextPage = null;
        $config   = SiteConfig::current_site_config();
        if ($config->AddAccessoriesStep && ($accessoriesPage = AccessoriesPage::get()->first())) {
            $nextPage = $accessoriesPage;
        } else {
            $nextPage = ReviewOrderPage::get()->first();
        }

        if ($nextPage) {
            return $this->redirect($nextPage->Link());
        }

        return $this->httpError(404);

    }

    public function removeAllWarranties(ShoppingCart $cart)
    {
        /** =========================================
         * @var DataList $variations
         * @var ProductVariation $variation
         * ========================================*/

        $variations = ProductVariation::get()->filter('ProductID', $this->ID);

        foreach ($variations as $variation) {
            $orderItem = $cart->get($variation);
            if ($orderItem) {
                $cart->remove($variation, $orderItem->Quantity);
            }
        }

        $orderItem = $cart->get($this->data());
        if ($orderItem) {
            $cart->remove($this, $orderItem->Quantity);
        }
    }

}
