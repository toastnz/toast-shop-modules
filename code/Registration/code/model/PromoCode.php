<?php

/**
 * Class PromoCode
 *
 * @property string  Code
 * @property Boolean Redeemed
 *
 * @method RegistrationMessage Message
 * @method Promotion Promotion
 */
class PromoCode extends DataObject
{
    private static $singular_name = 'Promo Code';
    private static $plural_name = 'Promo Codes';

    private static $db = [
        'Code'     => 'Varchar(50)',
        'Redeemed' => 'Boolean'
    ];

    private static $has_one = [
        'Message'   => 'RegistrationMessage',
        'Promotion' => 'Promotion'
    ];

    private static $summary_fields = [
        'ID'       => 'ID',
        'Code'     => 'Code',
        'Redeemed' => 'Is Redeemed',
        'Created'  => 'Date'
    ];

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        /** =========================================
         * @var FieldList     $fields
         * @var ReadonlyField $messageField
         * ========================================*/

        $fields = parent::getCMSFields();

        $fields->removeByName(['MessageID']);

        /** -----------------------------------------
         * Customer
         * ----------------------------------------*/

        if ($this->MessageID) {
            $message = $this->Message();

            if ($message && $message->exists()) {
                $messageField = ReadonlyField::create('Message.Title', 'Customer');
                $messageField->setValue($message->Title);
                $fields->insertAfter($messageField, 'Redeemed');
            }
        }

        return $fields;
    }

    public function getTitle()
    {
        return $this->Code;
    }

    public function getAddress1()
    {
        if ($this->Message() && $this->Message()->exists()) {
            return $this->Message()->Address;
        }

        return null;
    }

    public function getAddress2()
    {
        if ($this->Message() && $this->Message()->exists()) {
            return $this->Message()->City;
        }

        return null;
    }

    public function getAddress3()
    {
        if ($this->Message() && $this->Message()->exists()) {
            return $this->Message()->PostalCode;
        }

        return null;
    }

    public function getShippingTotal()
    {
        return '0.00';
    }

    public function getStatus()
    {
        return 'Paid';
    }


    /**
     * Count the order items
     *
     * @return null|string
     */
    public function ItemsQty()
    {
        $promo = $this->Promotion();

        if ($promo && $promo->exists()) {
            return $promo->ItemsQty();
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
        $promo = $this->Promotion();

        if ($promo && $promo->exists()) {
            return $promo->ItemPrices();
        }

        return null;
    }

    /**
     * Get the order items discounted prices.
     *
     * NOTE: This is a duplicate of the ItemPrices method above.
     * The RegistrationMessage export (ExportRedemptions.php) needs to have
     * the same columns as the Order export (ExportOrders.php) for the CDB warehouse
     * order processing software.
     * Event though there are no discounts for a PromoCode, this method is used
     * for convenience to display the same $summary_fields as the Orders, and because we can't have two
     * $summary_fields with the same name (PromoCode.ItemPrices), in the same class.
     *
     * @return null|string
     */
    public function DiscountedPrices_UNUSED()
    {
        $promo = $this->Promotion();

        if ($promo && $promo->exists()) {
            return $promo->ItemPrices();
        }

        return null;
    }

    public function DiscountedPrices()
    {
        // Get the Promo Products
        $items = $this->Promotion()->Products();
        // If there are any
        if ($items && $items->exists()) {
            // Set the base prices into an array
            $aBasePrices = $items->column('BasePrice');
            // Replace the array values with zero
            $aZeroPrices = array_fill_keys(
                array_keys($aBasePrices),
                '0.00'
            );

            return implode(',', $aZeroPrices);
        }
    }

    /**
     * Count the order items
     *
     * @return null|string
     */
    public function StockCodes()
    {
        $promo = $this->Promotion();

        if ($promo && $promo->exists()) {
            return $promo->StockCodes();
        }

        return null;
    }

    public function TotalDiscount()
    {
        $promo = $this->Promotion();

        if ($promo && $promo->exists()) {
            return $promo->getTotal();
        }
        return null;
    }

    public function getTotal()
    {
        return '0.00'; // Redemptions should always be zero for the warehouse export processing

        $promo = $this->Promotion();

        if ($promo && $promo->exists()) {
            return $promo->getTotal();
        }
        return null;
    }
}
