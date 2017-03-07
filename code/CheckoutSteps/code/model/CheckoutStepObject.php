<?php

/**
 * Class CheckoutStepObject
 *
 * @property string Type
 */
class CheckoutStepObject extends DataObject
{
    private static $singular_name = 'Step';
    private static $plural_name = 'Steps';
    private static $default_sort = 'SortOrder';

    /** @var CheckoutComponent $component */
    protected $component;

    private static $has_one = [
        'Parent'   => 'SiteConfig'
    ];

    protected $order;

    private static $db = [
        'Title'     => 'Varchar(255)',
        'Enabled'   => 'Boolean',
        'SortOrder' => 'Int',
        'Type'      => 'Varchar(100)'
    ];

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        /** =========================================
         * @var FieldList $fields
         * ========================================*/

        $fields = FieldList::create(TabSet::create('Root'));

        $fields->addFieldsToTab('Root.Main', [
            ReadonlyField::create('Title'),
            ReadonlyField::create('Type'),
            CheckboxField::create('Enabled', 'Enabled')
        ]);

        return $fields;
    }

    public function __construct($record = null, $isSingleton = false, $model = null)
    {
        parent::__construct($record, $isSingleton, $model);

        if ($this->exists() && $this->Type) {
            if (class_exists($this->Type . 'CheckoutComponent')) {
                $this->component = singleton($this->Type . 'CheckoutComponent');
            }
        }
    }

    public function getOrder()
    {
        return ShoppingCart::curr();
    }

    public function forTemplate()
    {
        if ($this->component) {
            return $this->renderWith([$this->Type, 'CheckoutComponent'], ['Fields' => $this->component->getFrontEndFields()]);
        }

        return '';
    }
}
