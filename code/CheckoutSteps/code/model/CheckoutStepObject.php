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
        'Parent' => 'SiteConfig'
    ];

    protected $order;

    private static $db = [
        'Title'     => 'Varchar(255)',
        'Enabled'   => 'Boolean',
        'SortOrder' => 'Int',
        'Type'      => 'Varchar(100)'
    ];

    public function __construct()
    {
        parent::__construct();

        if ($this->exists() && $this->Type) {
            if (class_exists($this->Type . 'CheckoutComponent')) {
                $this->component = self::singleton($this->Type . 'CheckoutComponent');
            }
        }
    }

    public function forTemplate()
    {
        if ($this->component) {
            return $this->renderWith([$this->Type, 'CheckoutComponent'], ['Fields' => $this->component->getFrontEndFields()]);
        }

        return '';
    }
}
