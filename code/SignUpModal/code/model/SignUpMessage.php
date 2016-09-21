<?php

/**
 * Class SignUpMessage
 *
 * @property string Name
 * @property string Email
 * @property string Status
 *
 * @method Order Cart()
 */
class SignUpMessage extends DataObject
{
    private static $db = array(
        'Name' => 'Varchar(500)',
        'Email' => 'Varchar(500)',
        'Status' => 'Enum("New,Processed","New")'
    );

    private static $has_one = array(
        'Cart' => 'Order'
    );

    private static $summary_fields = array(
        'Name' => 'Name',
        'Email' => 'Email',
        'Created.Nice' => 'Submitted',
        'Status' => 'Status',
        'HasAbandonedCart' => 'Abandoned Cart?',
        'Cart.Reference' => 'Cart Reference'
    );

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

        /** -----------------------------------------
         * Fields
         * ----------------------------------------*/

        $fields->addFieldsToTab('Root.Main', array(
            ReadonlyField::create('Name', 'Name'),
            ReadonlyField::create('Email', 'Email'),
            ReadonlyField::create('CartID', 'Cart Reference')
        ));

        return $fields;
    }

    public function getHasAbandonedCart()
    {
        if ($this->CartID) {
            if ($this->Cart()->Status == 'Cart') {
                return 'Yes';
            }
        }

        return 'No';
    }
}
