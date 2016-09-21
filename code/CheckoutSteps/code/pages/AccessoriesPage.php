<?php

class AccessoriesPage extends ProductCategory
{
    private static $singular_name = "Accessories Holder Page";
    private static $plural_name = "Accessories Holder Pages";

    private static $icon = 'toast-shop-modules/CheckoutSteps/images/icons/shopping-basket--arrow.png';

    private static $db = array(
        'MoreProductsText' => 'Varchar(100)'
    );

    private static $has_one = array(
        'MoreProductsImage' => 'Image'
    );


    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName(array(
            'Content',
            'HeroText',
            'HeroImage'
        ));

        $fields->addFieldsToTab('Root.Main', array(
            TextField::create('MoreProductsText'),
            UploadField::create('MoreProductsImage')
        ));

        return $fields;
    }

    public function requireDefaultRecords()
    {
        /** =========================================
         * @var AccessoriesPage $page
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
                    'Title' => 'Accessories',
                    'URLSegment' => 'accessories',
                    'ShowInMenus' => 0,
                    'ParentID' => $checkout->ID
                )
            );

            $page->write();
            $page->publish('Stage', 'Live');
            $page->flushCache();
            DB::alteration_message('Accessories Page created', 'created');
        }
    }
}

/**
 * Class AccessoriesPage_Controller
 *
 * @property AccessoriesPage dataRecord
 * @method AccessoriesPage data()
 * @mixin AccessoriesPage dataRecord
 */
class AccessoriesPage_Controller extends ProductCategory_Controller
{
    private static $allowed_actions = array(
        'proceed'
    );

    public function proceed()
    {
        $cart = ShoppingCart::singleton();

        foreach ($_POST as $name => $val) {
            if (StringUtils::StartsWith($name, 'product__') && (int)$val) {
                $id      = str_replace('product__', '', $name);
                $product = Product::get()->byID((int)$id);
                if ($product) {
                    $variations = $product->Variations();
                    if ($variations->count()) {
                        $cart->add($variations->first(), (int)$val);
                    } else {
                        $cart->add($product, (int)$val);
                    }
                }
            }
        }

        $order = $cart->current();
        if ($order) {
            $order->calculate();
            $order->write();
        }

        $nextPage = null;

        $nextPage = ReviewOrderPage::get()->first();

        if ($nextPage) {
            return $this->redirect($nextPage->Link());
        }

        return $this->httpError(404);
    }

    /**
     * Get accessories that are related to what we have in the cart.
     *
     * @return DataList|null
     */
    public function getAccessories()
    {
        /** =========================================
         * @var Order $order
         * ========================================*/

        $cart = ShoppingCart::singleton();

        $order = $cart->current();

        if ($order->Items()) {
            $productIDs = $order->Items()->column('ProductID');

            if (singleton('Product')->hasDatabaseField('BelongsTo')) {

                /**
                 * TODO: Using PartialMatch for now.
                 * This is fine to match the current sets of product IDs. However, something like "5" would be
                 * matched with the list "55,25,88" and so on. In the future, use the ORM. Have fun sorting out the
                 * edge cases, lol.
                 */

                $accessories = Product::get()->filter(array(
                    'BelongsTo:PartialMatch' => $productIDs
                ));

                return $accessories;
            }
        }

        return null;
    }
}
