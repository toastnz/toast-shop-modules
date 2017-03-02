<?php

/**
 * Created by PhpStorm.
 * User: danae
 * Date: 2/03/17
 * Time: 3:24 PM
 */
class CheckoutStepObject extends DataObject
{
    private static $singular_name = 'Step';
    private static $plural_name = 'Steps';
    private static $default_sort = 'SortOrder';

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
}
