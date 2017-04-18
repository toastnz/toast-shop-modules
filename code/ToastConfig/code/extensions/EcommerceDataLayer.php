<?php

/**
 * Class EcommerceDataLayer
 *
 * @property Page_Controller $owner
 */
class EcommerceDataLayer extends Extension
{
    /**
     * Create the Google Tag Manager "addToCart" dataLayer
     *
     * @return bool|string
     */
    public function AddToCartDataLayer() {
        // Set the default value to false
        $mData = false;
        // Get the session cart_item object
        $oItem = Session::get('add_cart_item');
        // Clear the session var
        Session::clear('add_cart_item');
        // If there is one
        if ($oItem) {
            if ($oItem->ClassName == 'Product') {
                $oProduct = $oItem;
                $iQuantity = 1;
            } else if ($oItem->ClassName == 'Product_OrderItem') {
                // Get the Product object
                $oProduct = DataObject::get_by_id('Product', $oItem->ProductID);
                $iQuantity = $oItem->Quantity;
            } else {
                return false;
            }
            // If there is one
            if ($oProduct) {
                // Try to get the SKU, or use the ID
                $iInternalItemID = ($oProduct->InternalItemID) ? $oProduct->InternalItemID : $oProduct->ID ;
                // Create the string var
                $mData = "dataLayer.push({\n\t\t";
                $mData .= "'event': 'addToCart',\n\t\t";
                $mData .= "'ecommerce': {\n\t\t\t";
                $mData .= "'currencyCode': '" . ShopConfig::get_site_currency() . "',\n\t\t\t";
                $mData .= "'add': {\n\t\t\t\t";                                // 'add' actionFieldObject measures.
                $mData .= "'products': [{\n\t\t\t\t\t";                        //  adding a product to a shopping cart.
                $mData .= "'name': '" . $oProduct->Title . "',\n\t\t\t\t\t";
                $mData .= "'id': '" . $iInternalItemID . "',\n\t\t\t\t\t";
                $mData .= "'price': '" . $oProduct->BasePrice . "',\n\t\t\t\t\t";
                $mData .= "'brand': '" . BRAND_NAME . "',\n\t\t\t\t\t";
                $mData .= "'category': 'Product',\n\t\t\t\t\t";
                $mData .= "'quantity': " . $iQuantity . "\n\t\t\t\t";
                $mData .= "}]\n\t\t\t";
                $mData .= "}\n\t\t";
                $mData .= "}\n\t";
                $mData .= "});";
            }
        }
        // Return the data value
        return $mData;
    }

    /**
     * Create the Google Tag Manager "removeFromCart" dataLayer
     *
     * @return bool|string
     */
    public function RemoveFromCartDataLayer() {
        // Set the default value to false
        $mData = FALSE;
        // Get the session remove_cart_items data, set in the CartPageControllerExtension
        $aCartItems = Session::get('remove_cart_items');
        // If there is any
        if (!empty($aCartItems)) {
            // Clear the session var
            Session::clear('remove_cart_items');
            // Create the string var
            $mData = "dataLayer.push({\n\t\t";
            $mData .= "'event': 'removeFromCart',\n\t\t";
            $mData .= "'ecommerce': {\n\t\t\t";
            $mData .= "'currencyCode': '" . ShopConfig::get_site_currency() . "',\n\t\t\t";
            $mData .= "'remove': {\n\t\t\t\t";                                // 'add' actionFieldObject measures.
            $mData .= "'products': [\n\t\t\t\t";
            // Set the product data string var
            $sProductData = '';
            // Loop through the items
            foreach ($aCartItems as $oItem) {
                // Get the Product_OrderItem record
                $oOrderItem = DataObject::get_by_id('Product_OrderItem', $oItem->ID);
                // If there is one
                if ($oOrderItem) {
                    // Get the Product record
                    $oProduct = DataObject::get_by_id('Product', $oOrderItem->ProductID);
                    // If there is one
                    if ($oProduct) {
                        // Try to get the SKU, or use the ID
                        $iInternalItemID = ($oProduct->InternalItemID) ? $oProduct->InternalItemID : $oProduct->ID;
                        // Add the product into the dataLayer
                        $sProductData .= "{\n\t\t\t\t\t";                        //  adding a product to a shopping cart.
                        $sProductData .= "'name': '" . $oProduct->Title . "',\n\t\t\t\t\t";
                        $sProductData .= "'id': '" . $iInternalItemID . "',\n\t\t\t\t\t";
                        $sProductData .= "'price': '" . $oItem->UnitPrice . "',\n\t\t\t\t\t";
                        $sProductData .= "'brand': '" . BRAND_NAME . "',\n\t\t\t\t\t";
                        $sProductData .= "'category': 'Product',\n\t\t\t\t\t";
                        $sProductData .= "'quantity': " . $oItem->Quantity . "\n\t\t\t\t";
                        $sProductData .= "},\n\t\t\t\t";
                    }
                }
            }
            // If the product data is not empty
            if ($sProductData != '') {
                // Add the product data to the dataLayer, removing any last comma
                $mData .= (substr(trim($sProductData), -1, 1) == ',') ? substr(trim($sProductData), 0, -1) : $sProductData;
            }
            $mData .= "]\n\t\t\t";
            $mData .= "}\n\t\t";
            $mData .= "}\n\t";
            $mData .= "});";

            // If there is only one item
        } else {
            return $this->RemoveSingleItemFromCartDataLayer();
        }

        // Return the data value
        return $mData;
    }

    /**
     * Create the Google Tag Manager "removeFromCart" dataLayer for a single order item.
     * Used for the new checkout cart functionality.
     *
     * @return bool|string
     */
    public function RemoveSingleItemFromCartDataLayer() {
        // Set the default value to false
        $mData = FALSE;
        // Get the session remove_cart_single_item data, set in the OrderExtension
        $oOrderItem = Session::get('remove_cart_single_item');
        // Clear the session var
        Session::clear('remove_cart_single_item');
        // If there is one
        if ($oOrderItem) {
            // Create the string var
            $mData = "dataLayer.push({\n\t\t";
            $mData .= "'event': 'removeFromCart',\n\t\t";
            $mData .= "'ecommerce': {\n\t\t\t";
            $mData .= "'currencyCode': '" . ShopConfig::get_site_currency() . "',\n\t\t\t";
            $mData .= "'remove': {\n\t\t\t\t";                                // 'add' actionFieldObject measures.
            $mData .= "'products': [\n\t\t\t\t";
            // Set the product data string var
            $sProductData = '';
            // If there is one
            if ($oOrderItem->Buyable()) {
                // Try to get the SKU, or use the ID
                $iInternalItemID = ($oOrderItem->Buyable()->InternalItemID) ? $oOrderItem->Buyable()->InternalItemID : $oOrderItem->Buyable()->ID ;
                // Add the product into the dataLayer
                $sProductData .= "{\n\t\t\t\t\t";                        //  adding a product to a shopping cart.
                $sProductData .= "'name': '" . $oOrderItem->Buyable()->Title . "',\n\t\t\t\t\t";
                $sProductData .= "'id': '" . $iInternalItemID . "',\n\t\t\t\t\t";
                $sProductData .= "'price': '" . $oOrderItem->UnitPrice . "',\n\t\t\t\t\t";
                $sProductData .= "'brand': '" . BRAND_NAME . "',\n\t\t\t\t\t";
                $sProductData .= "'category': 'Product',\n\t\t\t\t\t";
                $sProductData .= "'quantity': " . $oOrderItem->Quantity . "\n\t\t\t\t";
                $sProductData .= "},\n\t\t\t\t";
            }
            // If the product data is not empty
            if ($sProductData != '') {
                // Add the product data to the dataLayer, removing any last comma
                $mData .= (substr(trim($sProductData), -1, 1) == ',') ? substr(trim($sProductData), 0, -1) : $sProductData;
            }
            $mData .= "]\n\t\t\t";
            $mData .= "}\n\t\t";
            $mData .= "}\n\t";
            $mData .= "});";
        }
        // Return the data value
        return $mData;
    }

    /**
     * Create the Google Tag Manager "purchase" dataLayer
     *
     * @return bool|string
     */
    public function PurchaseDataLayer() {
        // Set the default value to false
        $mData = FALSE;
        // Get the session order_on_payment data
        $iOrderId = Session::get('order_on_payment');
        // Clear the session order_on_payment var
        Session::clear('order_on_payment');
        // If there is an Order ID
        if ($iOrderId) {
            // Get the Order
            $oOrder = DataObject::get_by_id('Order', $iOrderId);
            // If there is one
            if ($oOrder) {
                // Create the string var
                $mData = "dataLayer.push({\n\t\t";
                $mData .= "'ecommerce': {\n\t\t\t";
                $mData .= "'purchase': {\n\t\t\t\t";
                $mData .= "'actionField': {\n\t\t\t\t\t";
                $mData .= "'id': '" . $oOrder->ID . "',\n\t\t\t\t\t";
                $mData .= "'affiliation': 'Online Store " . $oOrder->ShippingAddress()->Country . "',\n\t\t\t\t\t";
                $mData .= "'revenue': '" . $oOrder->Total . "',\n\t\t\t\t\t";
                $mData .= "'shipping': '" . $oOrder->getModifier('CustomShippingFrameworkModifier')->Amount . "'\n\t\t\t\t";
                // If there are Order Items
                if ($oOrder->Items()) {
                    $mData .= "},\n\t\t\t\t";
                } else {
                    $mData .= "}\n\t\t\t\t";
                }
                // Set the product data string var
                $sProductData = '';
                // If there are Order Items
                if ($oOrder->Items()) {
                    $mData .= "'products': [\n\t\t\t\t";
                    // Loop through the items
                    foreach ($oOrder->Items() as $oItem) {
                        // Get the Product_OrderItem record
                        $oOrderItem = DataObject::get_by_id('Product_OrderItem', $oItem->ID);
                        // If there is one
                        if ($oOrderItem) {
                            // Get the Product record
                            $oProduct = DataObject::get_by_id('Product', $oOrderItem->ProductID);
                            // If there is one
                            if ($oProduct) {
                                // Try to get the SKU, or use the ID
                                $iInternalItemID = ($oProduct->InternalItemID) ? $oProduct->InternalItemID : $oProduct->ID ;
                                // Add the product into the dataLayer
                                $sProductData .= "{\n\t\t\t\t\t";                        //  adding a product to a shopping cart.
                                $sProductData .= "'name': '" . $oProduct->Title . "',\n\t\t\t\t\t";
                                $sProductData .= "'id': '" . $iInternalItemID . "',\n\t\t\t\t\t";
                                $sProductData .= "'price': '" . $oItem->UnitPrice . "',\n\t\t\t\t\t";
                                $sProductData .= "'brand': '" . BRAND_NAME . "',\n\t\t\t\t\t";
                                $sProductData .= "'category': 'Product',\n\t\t\t\t\t";
                                $sProductData .= "'quantity': " . $oItem->Quantity . "\n\t\t\t\t";
                                $sProductData .= "},\n\t\t\t\t";
                            }
                        }
                    }
                }
                // If the product data is not empty
                if ($sProductData != '') {
                    // Add the product data to the dataLayer, removing any last comma
                    $mData .= (substr(trim($sProductData), -1, 1) == ',') ? substr(trim($sProductData), 0, -1) : $sProductData;
                    //$mData .= "\n\t\t\t";
                }
                $mData .= "]\n\t\t\t";
                $mData .= "}\n\t\t";
                $mData .= "}\n\t";
                $mData .= "});";
            }
        }
        // Return the data value
        return $mData;
    }

    /**
     * Create the Google Tag Manager "checkout" dataLayer
     *
     * @return bool|string
     */
    public function CheckoutClickDataLayer() {
        // Set the default value to false
        $mData = false;
        // Get the session checkout_click_items object
        $oCartItems = Session::get('checkout_click_items');
        // Clear the session var
        Session::clear('checkout_click_items');
        // If there are any items
        if ($oCartItems) {
            // Create the string var
            $mData = "dataLayer.push({\n\t\t";
            $mData .= "'event': 'checkout',\n\t\t";
            $mData .= "'ecommerce': {\n\t\t\t";
            $mData .= "'currencyCode': '" . ShopConfig::get_site_currency() . "',\n\t\t\t";
            $mData .= "'checkout': {\n\t\t\t\t";                                // 'add' actionFieldObject measures.
            $mData .= "'actionField': {'step': 1},\n\t\t\t\t";
            $mData .= "'products': [{\n\t\t\t\t";
            // Set the product data string var
            $sProductData = '';
            // Loop through the items
            foreach ($oCartItems as $oItem) {
                // Check the item ClassName
                switch ($oItem->ClassName) {
                    case 'ProductVariation_OrderItem':
                        // Set the variables
                        $iId = $oItem->ProductID;
                        $iQuantity = $oItem->Quantity;
                        $sPrice = $oItem->ProductVariation()->Price;
                        $sCategory = $oItem->Product()->Parent()->Title;
                        $sTitle = $oItem->Product()->Title;
                        $sVariationTitle = $oItem->ProductVariation()->CustomTitle;
                        break;
                    case 'Product_OrderItem':
                        // Set the variables
                        $iId = $oItem->ProductID;
                        $iQuantity = $oItem->Quantity;
                        $sPrice = $oItem->Product()->Price;
                        $sCategory = $oItem->Product()->Parent()->Title;
                        $sTitle = $oItem->Product()->Title;
                        $sVariationTitle = false;
                        break;
                    default:
                        return false;
                        break;
                }
                // If there is an item ID
                if ($iId) {
                    // Add the product into the dataLayer
                    $sProductData .= "{\n\t\t\t\t\t";                        //  adding a product to a shopping cart.
                    $sProductData .= "'name': '" . $sTitle . "',\n\t\t\t\t\t";
                    $sProductData .= "'id': '" . $iId . "',\n\t\t\t\t\t";
                    $sProductData .= "'price': '" . $sPrice . "',\n\t\t\t\t\t";
                    $sProductData .= "'brand': '" . BRAND_NAME . "',\n\t\t\t\t\t";
                    $sProductData .= "'category': '" . $sCategory . "',\n\t\t\t\t\t";
                    if ($sVariationTitle) {
                        $sProductData .= "'variant': '" . $sVariationTitle . "',\n\t\t\t\t\t";
                    }
                    $sProductData .= "'quantity': " . $iQuantity . "\n\t\t\t\t";
                    $sProductData .= "},\n\t\t\t\t";
                }
            }
            // If the product data is not empty
            if ($sProductData != '') {
                // Add the product data to the dataLayer, removing any last comma
                $mData .= (substr(trim($sProductData), -1, 1) == ',') ? substr(trim($sProductData), 0, -1) : $sProductData;
            }
            $mData .= "]\n\t\t\t";
            $mData .= "}\n\t\t";
            $mData .= "}\n\t";
            $mData .= "});";
        }
        // Return the data value
        return $mData;
    }

    /**
     * Facebook "Purchase" tracking
     *
     * @return bool|string The order total and currency, or false
     */
    public function FacebookOrderTracking() {
        // Get the Facebook tracking Order session ID, which is set in the PurchaseDataLayer method above.
        $iOrderId = Session::get('facebook_order_tracking');
        // Clear the session var
        Session::clear('facebook_order_tracking');
        // If there is an Order ID
        if ($iOrderId) {
            // Get the Order
            $oOrder = DataObject::get_by_id('Order', $iOrderId);
            // If there is one
            if ($oOrder->exists()) {
                // Return the Purchase section for the Facebook script
                return "fbq('track', 'Purchase', {value:'" . $oOrder->Total . "', currency:'" . ShopConfig::get_site_currency() . "'});";
            } else {
                return false;
            }
        } else {
            return false;
        }

    }
}

class EcommerceDataLayerOrder extends DataExtension
{
    public function afterAdd($item, $buyable, $quantity, $filter)
    {

        Session::set('AddedToCart', true);
        // Set the cart item into a session var for the Google Tag Manager dataLayer
        Session::set('add_cart_item', $item);

    }

    public function afterRemove($item, $buyable, $quantity, $filter)
    {
        // Set the cart item into a session var
        Session::set('remove_cart_single_item', $item);
    }

    /**
     * Set "on payment" into a session var
     * Used for the Google Tag Manager dataLayer, which is created in the Page_Controller
     *
     * @return void
     */
    public function onPayment()
    {
        // Set the order ID into a session var for the Google Tag Manager dataLayer
        Session::set('order_on_payment', $this->owner->ID);
        // Set the order ID into a session var for the Facebook Purchase tracking
        Session::set('facebook_order_tracking', $this->owner->ID);
    }
}
