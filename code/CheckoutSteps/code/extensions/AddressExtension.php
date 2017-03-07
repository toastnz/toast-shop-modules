<?php

/**
 * Class ToastAddressExtension
 *
 * @property Address $owner
 */
class ToastAddressExtension extends DataExtension
{
    private static $db = [
        'Email' => 'Varchar(255)'
    ];

    /**
     * @param DropdownField|ReadonlyField $field
     */
    public function updateCountryField(&$field)
    {
        if ($field instanceof DropdownField) {
            $field->setEmptyString(_t('TOASTSHOP.CountryPlaceholder', 'Select Country'))
                ->addExtraClass('input-wrap input-wrap--half input-wrap--half--last');
        }
    }

    /**
     * @param FieldList $fields
     */
    public function updateFormFields(&$fields)
    {
        $additionalFields = FieldList::create([
            TextField::create('FirstName', 'First Name')
                ->setAttribute('placeholder', _t('Address.FirstNamePlaceholder', 'First Name'))
                ->addExtraClass('input-wrap input-wrap--half'),
            TextField::create('Surname', 'Last Name')
                ->setAttribute('placeholder', _t('Address.SurnamePlaceholder', 'Last Name'))
                ->addExtraClass('input-wrap input-wrap--half input-wrap--half--last'),
            EmailField::create('Email', 'Email')
                ->setAttribute('placeholder', _t('Address.EmailPlaceholder', 'Email'))
                ->addExtraClass('input-wrap input-wrap--half')
        ]);

        $fields->merge($additionalFields);

        // Set up placeholders
        $fields->fieldByName('Address')
            ->setAttribute('placeholder', _t('Address.AddressPlaceholder', 'Address'));

        $fields->fieldByName('City')
            ->setAttribute('placeholder', _t('Address.CityPlaceholder', 'City'))
            ->addExtraClass('input-wrap input-wrap--half');

        $fields->fieldByName('State')
            ->setAttribute('placeholder', _t('Address.StatePlaceholder', 'State'))
            ->addExtraClass('input-wrap input-wrap--half input-wrap--half--last');

        $fields->fieldByName('PostalCode')
            ->setAttribute('placeholder', _t('Address.PostalCodePlaceholder', 'Postal Code'))
            ->addExtraClass('input-wrap input-wrap--half');

        $fields->fieldByName('Phone')
            ->setAttribute('placeholder', _t('Address.PhonePlaceholder', 'Phone'))
            ->setAttribute('data-parsley-type', 'number')
            ->addExtraClass('input-wrap input-wrap--half input-wrap--half--last');

        // Adjust field order
        $fields->changeFieldOrder(['FirstName', 'Surname', 'Email', 'Phone', 'Address', 'AddressLine2', 'City', 'State', 'PostalCode', 'Country']);
    }
}

/**
 * Class ToastAddressComponentExtension
 *
 * @property AddressCheckoutComponent $owner
 */
class ToastAddressComponentExtension extends Extension
{
    public function updateAdditionalComponentFields(FieldList &$fields)
    {
        $fields->push(CheckboxField::create('SeparateBilling', _t('TOASTSHOP.SeparateBilling', 'Different Billing Address')));
    }
}
