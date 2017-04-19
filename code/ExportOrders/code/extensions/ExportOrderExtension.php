<?php

/**
 * Class ExportOrderExtension
 */
class ExportOrderExtension extends DataExtension
{
    private static $db = [
        'AutoExported' => 'Boolean(0)'
    ];

    private static $summary_fields = [
        'Reference'     => 'Order No',
        'Placed'        => 'Date',
        'Name'          => 'Customer',
        'Address1'      => 'Address1',
        'Address2'      => 'Address2',
        'Address3'      => 'Address3',
        'Address4'      => 'Address4',
        'Address5'      => 'Address5',
        'Address6'      => 'Address6',
        'Email'         => 'Email',
        'Phone'         => 'Phone',
        'StockCodes'    => 'Stock Codes',
        'ItemPrices'    => 'Prices',
        'ItemsQty'      => 'Qty',
        'Discount'      => 'Discount',
        'Notes'         => 'Delivery Instructions',
        'ShippingTotal' => 'Shipping',
        'Total'         => 'Total',
        'Status'        => 'Status',
        'AutoExported'  => 'Auto Exported'
    ];

    public function updateSummaryFields(&$fields)
    {
        $fields = Config::inst()->get($this->owner->class, 'summary_fields');
    }

    private static $searchable_fields = [
        'Reference'    => [],
        'FirstName'    => [
            'title' => 'Customer Name',
        ],
        'Email'        => [
            'title' => 'Customer Email',
        ],
        'Status'       => [
            'filter' => 'ExactMatchFilter',
            'field'  => 'CheckboxSetField',
        ],
        'AutoExported' => [
            'title' => 'Auto Exported'
        ]
    ];


    /**
     * Get Address line 1
     *
     * @return mixed
     */
    public function getAddress1()
    {
        // Get the ShippingAddress for this order
        $oShippingAddress = $this->owner->ShippingAddress();
        // If there is one
        if ($oShippingAddress) {
            // Get the field
            return $oShippingAddress->Address;
        }
        return null;
    }

    /**
     * Get Address line 2
     *
     * @return mixed
     */
    public function getAddress2()
    {
        // Get the ShippingAddress for this order
        $oShippingAddress = $this->owner->ShippingAddress();
        // If there is one
        if ($oShippingAddress) {
            // Get the field
            return $oShippingAddress->AddressLine2;
        }
        return null;
    }

    /**
     * Get Address line 3
     *
     * @return mixed
     */
    public function getAddress3()
    {
        // Get the ShippingAddress for this order
        $oShippingAddress = $this->owner->ShippingAddress();
        // If there is one
        if ($oShippingAddress) {
            // Get the field
            return $oShippingAddress->State;
        }
        return null;
    }

    /**
     * Get Address line 4
     *
     * @return mixed
     */
    public function getAddress4()
    {
        // Get the ShippingAddress for this order
        $oShippingAddress = $this->owner->ShippingAddress();
        // If there is one
        if ($oShippingAddress) {
            // Get the field
            return $oShippingAddress->City;
        }
        return null;
    }

    /**
     * Get Address line 5
     *
     * @return mixed
     */
    public function getAddress5()
    {
        // Get the ShippingAddress for this order
        $oShippingAddress = $this->owner->ShippingAddress();
        // If there is one
        if ($oShippingAddress) {
            // Get the field
            return $oShippingAddress->PostalCode;
        }
        return null;
    }

    /**
     * Get Address line 6
     *
     * @return mixed
     */
    public function getAddress6()
    {
        // Get the ShippingAddress for this order
        $oShippingAddress = $this->owner->ShippingAddress();
        // If there is one
        if ($oShippingAddress) {
            // Get the field
            return $oShippingAddress->Country;
        }
        return null;
    }

    /**
     * Get Address Phone number
     *
     * @return mixed
     */
    public function getPhone()
    {
        // Get the ShippingAddress for this order
        $oShippingAddress = $this->owner->ShippingAddress();
        // If there is one
        if ($oShippingAddress) {
            // Get the field
            return $oShippingAddress->Phone;
        }
        return null;
    }

    /**
     * Get the Shipping total
     *
     * @return string|boolean The shipping total, or false
     */
    public function ShippingTotal()
    {
        $sShippingTotal = 0;
        // Get the shipping amount for this order
        if (class_exists('ShippingFrameworkModifier')) {
            $sShippingTotal = $this->owner->getModifier('ShippingFrameworkModifier')->Amount;
        }
        // If there is one
        if ($sShippingTotal) {
            // Return the total
            return $sShippingTotal;
        }
        return false;
    }

    /**
     * Get any discount codes
     *
     * @return string
     */
    public function getDiscount()
    {
        // Get the discounts for this order
        $oDiscounts = $this->owner->Discounts();
        // If there are any
        if ($oDiscounts) {
            // Set the discounts array
            $aDiscountsArray = [];
            // Loop through the discounts
            foreach ($oDiscounts as $oDiscount) {
                // Add them into the array
                array_push($aDiscountsArray, $oDiscount->Code);
            }
            // Return the comma separated string
            return implode(', ', $aDiscountsArray);
        }

        return '';
    }

    /**
     * Count the order items
     *
     * @return null|string
     */
    public function ItemsQty()
    {
        // Get the order items
        $oItems = $this->owner->Items();
        // If there are any
        if ($oItems) {
            // Create the return string
            $sCounts = '';
            // Loop through the items
            foreach ($oItems as $oItem) {
                // If there is a buyable product and a product BasePrice
                if ($oItem->Buyable() && $oItem->Quantity) {
                    // Add the price into the return string
                    $sCounts .= $oItem->Quantity . ',';
                }
            }
            // If the last character is a comma
            if (substr(trim($sCounts), -1) == ',') {
                // Remove the last comma
                $sCounts = substr($sCounts, 0, -1);
            }
            return $sCounts;
        }

        return null;
    }

    /**
     * Get the order items prices
     *
     * @return null|string
     */
    public function ItemPrices()
    {
        // Get the order items
        $oItems = $this->owner->Items();
        // If there are any
        if ($oItems) {
            // Create the return string
            $sPrices = '';
            // Loop through the items
            foreach ($oItems as $oItem) {
                // If there is a buyable product and a product BasePrice
                if ($oItem->Buyable() && $oItem->Buyable()->BasePrice) {
                    // Add the price into the return string
                    $sPrices .= $oItem->Buyable()->BasePrice . ',';
                }
            }
            // If the last character is a comma
            if (substr(trim($sPrices), -1) == ',') {
                // Remove the last comma
                $sPrices = substr($sPrices, 0, -1);
            }
            return $sPrices;
        }

        return null;
    }


    /**
     * Get the order items SKUs
     *
     * @return null|string
     */
    public function StockCodes()
    {
        // Get the order items
        $oItems = $this->owner->Items();
        // If there are any
        if ($oItems) {
            // Create the return string
            $sSkus = '';
            // Loop through the items
            foreach ($oItems as $oItem) {
                // If there is a buyable product and a product SKU
                if ($oItem->Buyable() && $oItem->Buyable()->InternalItemID) {
                    // Add the sku into the return string
                    $sSkus .= $oItem->Buyable()->InternalItemID . ',';
                }
            }
            // If the last character is a comma
            if (substr(trim($sSkus), -1) == ',') {
                // Remove the last comma
                $sSkus = substr($sSkus, 0, -1);
            }
            return $sSkus;
        }

        return null;
    }


    /**
     * Get the order total for the export functionality
     *
     * @return mixed|number The concatenated value of all order items
     */
    public function ExportTotal() {
        // If this is a Token payment order (it contains a Token product)
        if ($this->owner->getIsTokenPayment()) {
            // Set the return value
            $nExportTotal = 0;
            // If there are any order items
            if (count($this->owner->Items())) {
                // Get the items
                $oItems = $this->owner->Items();
                // Loop through the items
                foreach ($oItems as $oItem) {
                    // Get the item price, multiplied by the quantity
                    $nExportTotal += ($this->owner->FindItemPrice($oItem, true) * $oItem->Quantity);
                }
            }

            // Return the total plus the shipping, minus the discount
            return ($nExportTotal + $this->owner->ShippingTotal()) - $this->owner->getDiscountAmount();

        } else {
            return $this->owner->Total;
        }
    }

    /**
     * Get the discount code
     *
     * @return string
     */
    public function getDiscountCode() {
        // If there are discounts applied
        if (count($this->owner->Discounts())) {
            // Set the discounts array
            $aDiscountArray = array();
            // Get the discounts
            $oDiscounts = $this->owner->Discounts();
            // Loop through the discounts
            foreach ($oDiscounts as $oDiscount) {
                // If the discount is a coupon
                if ($oDiscount->ClassName == 'OrderCoupon') {
                    // Get the discount code and add it to the discounts array
                    array_push($aDiscountArray, 'Coupon: ' . $oDiscount->Code);
                } else {
                    // Get the discount code and add it to the discounts array
                    array_push($aDiscountArray, 'Discount: ' . $oDiscount->Title);
                }
            }
            // Return the comma imploded codes string
            return implode(', ', $aDiscountArray);
        }

        return '';
    }

    /**
     * Get the discounted amount
     *
     * @return string The discounted amount for this order
     */
    public function getDiscountAmount() {
        // If there are any Discounts for this Order
        if (count($this->owner->Discounts())) {
            // Get the Discount Total
            $sDiscountTotal = $this->owner->getModifier('OrderDiscountModifier')->Amount();
            // Return the number formatted value
            return ($sDiscountTotal) ? number_format($sDiscountTotal, 2) : '';
        }

        return '';
    }

    /**
     * Get the order items discounted prices
     *
     * @param boolean $bExport Whether or not the result is being used for the export functionality
     * @return null|string
     */
    public function ItemDiscountedPrices($bExport = false) {
        // Set the max execution time
        ini_set('max_execution_time', 0);
        // Create the return string
        $sDiscountedItemPrices = '';
        // If there are any items in this order
        if (count($this->owner->Items())) {
            // Get the Order Items
            $oItems = $this->owner->Items();
            // Loop through them
            foreach ($oItems as $oItem) {
                // If there is a Product
                if ($oItem->Product()) {
                    // Try to get a ProductVariation
                    $oVariation = ProductVariation::get_one('ProductVariation', "ProductID =" . $oItem->Product()->ID);
                    // If there is one
                    if ($oVariation && $oVariation->exists()) {
                        // Use the variation Price and CustomTitle
                        $sItemPrice = $oVariation->Price;
                    } else {
                        // Get the item price, and add it into the return string
                        $sItemPrice = $this->owner->FindItemPrice($oItem, $bExport);
                    }
                    // Set the default Discount and Amount vars to zero
                    $nDiscountPercentTotal = 0;
                    $nDiscountAmountTotal = 0;
                    // Set the default discount item price to the item price
                    $sDiscountedItemPrice = $sItemPrice;
                    /*
                     * Loop through the Order Discounts and add the values together for each discount.
                     * NOTE: There may be multiple discounts for each item.
                     */
                    // If there are discounts
                    if (count($this->owner->Discounts())) {
                        // Loop through them
                        foreach ($this->owner->Discounts() as $oDiscount) {
                            /*
                             * %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                             * If the discount applies to Each Individual Item
                             * %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                             */
                            if ($oDiscount->ForItems && !$oDiscount->ForCart && !$oDiscount->ForShipping) {
                                /*
                                 * ************************************************************
                                 * If there are discount Products specified
                                 * ************************************************************
                                 */
                                if (count($oDiscount->Products())) {
                                    // Loop through the items
                                    foreach ($oDiscount->Products() as $oDiscountProduct) {
                                        // If the OrderItem Product ID matches the Discount Product ID
                                        if ($oDiscountProduct && ($oItem->Product()->ID == $oDiscountProduct->ID)) {
                                            // If the discount type is a percentage
                                            if ($oDiscount->Type == 'Percent') {
                                                // Add the percentage value
                                                $nDiscountPercentTotal += $oDiscount->Percent;
                                            }
                                            // If the discount type is an amount
                                            if ($oDiscount->Type == 'Amount') {
                                                // Add the number value
                                                $nDiscountAmountTotal += $oDiscount->Amount;
                                            }
                                        }
                                    }
                                }
                                /*
                                 * ************************************************************
                                 * If there are NO discount Products specified
                                 * ************************************************************
                                 */
                                if (!count($oDiscount->Products())) {
                                    // If the discount type is a percentage
                                    if ($oDiscount->Type == 'Percent') {
                                        // Add the percentage value
                                        $nDiscountPercentTotal += $oDiscount->Percent;
                                    }
                                    // If the discount type is an amount
                                    if ($oDiscount->Type == 'Amount') {
                                        // Add the number value
                                        $nDiscountAmountTotal += $oDiscount->Amount;
                                    }
                                }
                            }
                            /*
                             * %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                             * If the discount applies to Cart Subtotal only
                             * %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                             */
                            if ($oDiscount->ForCart && !$oDiscount->ForItems && !$oDiscount->ForShipping) {

                                // If the discount type is a percentage
                                if ($oDiscount->Type == 'Percent') {
                                    // Add the percentage value
                                    $nDiscountPercentTotal += $oDiscount->Percent;
                                }
                                // If the discount type is an amount
                                if ($oDiscount->Type == 'Amount') {
                                    // Add the number value
                                    $nDiscountAmountTotal += $oDiscount->Amount;
                                }
                            }
                            /*
                             * %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                             * If the discount applies to Shipping Subtotal only
                             * %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                             */
                            if ($oDiscount->ForShipping && !$oDiscount->ForCart && !$oDiscount->ForItems) {
                                // The discount is dealt with in the ShippingTotal() method
                                break;
                            }
                            /*
                             * %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                             * If the discount applies to shipping and cart only (Entire Order)
                             * %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                             */
                            if ($oDiscount->ForShipping && $oDiscount->ForCart && !$oDiscount->ForItems) {
                                // If the discount type is a percentage
                                if ($oDiscount->Type == 'Percent') {
                                    // Add the percentage value
                                    $nDiscountPercentTotal += $oDiscount->Percent;
                                }
                                // If the discount type is an amount
                                if ($oDiscount->Type == 'Amount') {
                                    // Add the number value
                                    $nDiscountAmountTotal += $oDiscount->Amount;
                                }
                            }
                        }
                    }
                    /* END DISCOUNT LOOP */
                    // If there is a percentage discount
                    if ($nDiscountPercentTotal) {
                        // Deduct the total discount percentage from the item price
                        $sDiscountedItemPrice = ($sItemPrice - ($sItemPrice * $nDiscountPercentTotal));
                    }
                    // If there is an amount discount
                    if ($nDiscountAmountTotal) {
                        // Deduct the total discount amount from the discounted item price
                        $sDiscountedItemPrice = ($sDiscountedItemPrice - $nDiscountAmountTotal);
                    }
                    // Add the Order Item Discount Price into the return string
                    $sDiscountedItemPrices .= $sDiscountedItemPrice . ',';
                }
            }
            /* END ITEM LOOP */
        }
        // If the last Discounted Item Prices string character is a comma
        if (substr(trim($sDiscountedItemPrices), -1) == ',') {
            // Remove the last comma
            $sDiscountedItemPrices = substr($sDiscountedItemPrices, 0, -1);
        }

        return $sDiscountedItemPrices;

    }

    /**
     * Get the Cart reference
     *
     * @return int|mixed|string
     */
    public function getReference() {
        $reference = $this->owner->getField('Reference') ? $this->owner->getField('Reference') : $this->owner->ID;

        if (strpos($reference, 'BB') === false) {
            $reference = 'BB' . $reference;
        }

        if ($reference == 'BB0') {
            $reference = 'BB' . $this->owner->ID;
        }

        return $reference;
    }

    public function getReferenceForCart() {
        return $this->getReference();
    }

    public function Reference() {
        return $this->getReference();
    }

}
