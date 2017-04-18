<?php

/**
 * Class Promotion
 *
 * @property string Title
 * @property string SuccessMessage
 * @property string Status
 *
 * @method HasManyList Codes
 *
 * @method ManyManyList Products
 */
class Promotion extends DataObject
{
    private static $singular_name = 'Promotion';
    private static $plural_name = 'Promotions';

    private static $db = [
        'Title'          => 'Varchar(255)',
        'SuccessMessage' => 'Text',
        'Status'         => 'Enum("Active,Expired","Active")'
    ];

    private static $has_many = [
        'Codes' => 'PromoCode'
    ];

    private static $many_many = [
        'Products' => 'Product'
    ];

    private static $many_many_extraFields = [
        'Products' => [
            'Quantity' => 'Int'
        ]
    ];

    private static $summary_fields = [
        'ID'                     => 'ID',
        'Title'                  => 'Name',
        'Status'                 => 'Status',
        'SuccessMessage.Summary' => 'Message',
        'StockCodes'             => 'Product SKU'
    ];

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        /** =========================================
         * @var FieldList            $fields
         * @var TextareaField        $successMessage
         * @var FieldList            $productFields
         * @var GridFieldConfig      $config
         * @var GridFieldConfig      $codeConfig
         * @var GridFieldDataColumns $columns
         * ========================================*/

        $fields = parent::getCMSFields();

        $fields->removeByName(['SuccessMessage', 'Status', 'Codes', 'Products', 'Title']);

        /** -----------------------------------------
         * Config
         * ----------------------------------------*/

        $successMessage = TextareaField::create('SuccessMessage', 'Success Message');
        $successMessage->setRows(10)
            ->setDescription('Unique to this promotional campaign. Displays to user after they register their machine and use a redemption code.');

        $fields->addFieldsToTab('Root.Main', [
            HeaderField::create('', 'Configuration'),
            TextField::create('Title', 'Name'),
            $this->dbObject('Status')->formField(),
            $successMessage
        ]);

        /** -----------------------------------------
         * Products
         * ----------------------------------------*/

        if ($this->ID) {

            // Create base GridField
            $config = GridFieldConfig_RelationEditor::create(50);
            $config->removeComponentsByType('GridFieldAddNewButton');

            // Customise columns
            $columns = $config->getComponentByType('GridFieldDataColumns');
            $columns->setDisplayFields([
                'InternalItemID'        => 'SKU',
                'Title'                 => 'Title',
                'BasePrice.NiceOrEmpty' => 'Price',
                'Quantity'              => 'Quantity'
            ]);

            // Customise form
            $productFields = FieldList::create([
                ReadonlyField::create('Title', 'Title'),
                ReadonlyField::create('InternalItemID', 'SKU'),
                NumericField::create('ManyMany[Quantity]', 'Quantity')
            ]);
            $config->getComponentByType('GridFieldDetailForm')->setFields($productFields);

            $gridField = GridField::create(
                'Products',
                '',
                $this->Products(),
                $config
            );

            $fields->addFieldsToTab('Root.Main', [
                HeaderField::create('', 'Products'),
                LiteralField::create('', '<div class="message notice"><p>Click on a product below to adjust the quantity.</p></div>'),
                $gridField
            ]);
        }

        /** -----------------------------------------
         * Codes
         * ----------------------------------------*/

        if ($this->ID) {

            // Create base GridField
            $codeConfig = GridFieldConfig_RelationEditor::create(20);
            $codeConfig->removeComponentsByType('GridFieldDeleteAction')
                ->addComponent(new GridFieldDeleteAction())
                ->addComponent(new GridFieldImporter('before'));

            $codeGridField = GridField::create(
                'Codes',
                '',
                $this->Codes(),
                $codeConfig
            );

            $fields->addFieldsToTab('Root.Main', [
                HeaderField::create('', 'Codes'),
                $codeGridField
            ]);
        }

        return $fields;
    }

    /**
     * Count the order items
     *
     * @return null|string
     */
    public function ItemsQty()
    {
        $items = $this->Products();

        if ($items && $items->exists()) {
            return implode(',', $items->column('Quantity'));
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
        $items = $this->Products();

        if ($items && $items->exists()) {
            return implode(',', $items->column('BasePrice'));
        }

        return null;
    }

    /**
     * Count the order items
     *
     * @return null|string
     */
    public function StockCodes()
    {
        $items = $this->Products();

        if ($items && $items->exists()) {
            return implode(',', $items->column('InternalItemID'));
        }

        return null;
    }

    public function getTotal()
    {
        $items = $this->Products();

        if ($items && $items->exists()) {
            return array_sum($items->column('BasePrice'));
        }

        return null;
    }
}
