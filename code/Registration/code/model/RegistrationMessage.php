<?php

/**
 * Class RegistrationMessage
 *
 * @property string FirstName
 * @property string Surname
 * @property string Email
 * @property string Address
 * @property string City
 * @property string PostalCode
 * @property string Phone
 * @property string Store
 * @property string DateOfPurchase
 *
 * @method Product RegisteredProduct
 */
class RegistrationMessage extends DataObject
{
    private static $singular_name = 'Registration';
    private static $plural_name = 'Registrations';

    private static $db = [
        'Serial'         => 'Varchar(500)',
        'FirstName'      => 'Varchar(255)',
        'Surname'        => 'Varchar(255)',
        'Email'          => 'Varchar(500)',
        'Address'        => 'Text',
        'City'           => 'Varchar(500)',
        'PostalCode'     => 'Varchar(50)',
        'Phone'          => 'Varchar(50)',
        'Store'          => 'Varchar(500)',
        'DateOfPurchase' => 'Date',
        'Reference'      => 'Varchar(20)',
        'AutoExported'   => 'Boolean(0)'
    ];

    private static $summary_fields = [
        'Reference'                  => 'Order No',
        'Created'                    => 'Date',
        'Title'                      => 'Customer',
        'Address'                    => 'Address1',
        'City'                       => 'Address2',
        'PostalCode'                 => 'Address3',
        'Address4'                   => 'Address4',
        'Address5'                   => 'Address5',
        'Address6'                   => 'Address6',
        'Email'                      => 'Email',
        'Phone'                      => 'Phone',
        'PromoCode.StockCodes'       => 'Stock Codes',
        'PromoCode.ItemPrices'       => 'Prices',
        'PromoCode.DiscountedPrices' => 'Discounted Prices',
        'PromoCode.ItemsQty'         => 'Qty',
        'PromoCode.Code'             => 'Discount',
        'PromoCode.TotalDiscount'    => 'Discount Amount',
        'Notes'                      => 'Delivery Instructions',
        'PromoCode.ShippingTotal'    => 'Shipping',
        'PromoCode.Total'            => 'Total',
        'PromoCode.Status'           => 'Status',
        'AutoExported'               => 'Auto Exported',
        'Store'                      => 'Store',
        'DateOfPurchase'             => 'Date of Purchase',
    ];

    private static $searchable_fields = [
        'PromoCode.Code',
        'PromoCode.Redeemed',
        'PromoCode.Promotion.ID',
        'PromoCode.Promotion.Status',
        'AutoExported'
    ];

    private static $field_labels = [
        'PromoCode.Code'         => 'Redemption Code',
        'PromoCode.Redeemed'     => 'Redeemed',
        'PromoCode.Promotion.ID' => 'Promotion'
    ];

    private static $has_one = [
        'RegisteredProduct' => 'Product',
        'PromoCode'         => 'PromoCode'
    ];

    private static $default_sort = 'Created DESC';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        /** =========================================
         * @var FieldList $fields
         * ========================================*/

        $fields = parent::getCMSFields();

        $fields->removeByName(['RegisteredProductID', 'Serial', 'PromoCodeID']);

        /** -----------------------------------------
         * Fields
         * ----------------------------------------*/

        $fields->addFieldsToTab('Root.Main', [
            ReadonlyField::create('Reference', 'Reference'),
            ReadonlyField::create('Serial', 'Serial Number'),
            ReadonlyField::create('FirstName', 'First Name'),
            ReadonlyField::create('Surname', 'Surname'),
            ReadonlyField::create('Email', 'Email'),
            ReadonlyField::create('Address', 'Address'),
            ReadonlyField::create('City', 'City'),
            ReadonlyField::create('PostalCode', 'Postal Code'),
            ReadonlyField::create('Phone', 'Phone'),
            ReadonlyField::create('Store', 'Store'),
            ReadonlyField::create('DateOfPurchase', 'Date of Purchase')
        ]);

        if ($this->RegisteredProductID) {
            $product = $this->RegisteredProduct();

            if ($product && $product->exists()) {
                $productField = ReadonlyField::create('RegisteredProduct.Title', 'Product');
                $productField->setValue($product->Title);
                $fields->insertAfter($productField, 'Serial');
            }
        }

        if ($this->PromoCodeID) {
            $product = $this->PromoCode();

            if ($product && $product->exists()) {
                $productField = ReadonlyField::create('PromoCode.Title', 'Redemption Code');
                $productField->setValue($product->Title);
                $fields->insertAfter($productField, 'Serial');
            }
        }

        return $fields;
    }

    public function getCustomSearchContext()
    {
        $fields = $this->scaffoldSearchFields([
            'restrictFields' => [
                'PromoCode.Code',
                'PromoCode.Redeemed',
                'PromoCode.StockCodes'
            ]
        ]);

        $filters = [
            'PromoCode.Code'       => new ExactMatchFilter('PromoCode.Code'),
            'PromoCode.StockCodes' => new PartialMatchFilter('PromoCode.StockCodes')
        ];

        return new SearchContext(
            $this->class,
            $fields,
            $filters
        );
    }

    public function getTitle()
    {
        return sprintf('%s %s', $this->FirstName, $this->Surname);
    }
}
