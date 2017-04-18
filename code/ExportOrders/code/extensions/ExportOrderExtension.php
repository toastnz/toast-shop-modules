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
        $fields = Config::inst()->get($this->class, 'summary_fields');
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
}
