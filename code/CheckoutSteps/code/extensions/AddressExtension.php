<?php

/**
 * Class ToastAddressExtension
 *
 * @property Address $owner
 */
class ToastAddressExtension extends DataExtension
{
    /**
     * @param DropdownField|ReadonlyField $field
     */
    public function updateCountryField(&$field)
    {
        if ($field instanceof DropdownField) {
            $field->setEmptyString(_t('TOASTSHOP.CountryPlaceholder', 'Select Country'));
        }
    }

    /**
     * @param FieldList $fields
     */
    public function updateFormFields(&$fields)
    {
        // Set up placeholders
        $fields->fieldByName('Address')->setAttribute('placeholder', _t('Address.AddressPlaceholder', 'Address'));
        $fields->fieldByName('City')->setAttribute('placeholder', _t('Address.CityPlaceholder', 'City'));
        $fields->fieldByName('State')->setAttribute('placeholder', _t('Address.StatePlaceholder', 'State'));
        $fields->fieldByName('PostalCode')->setAttribute('placeholder', _t('Address.PostalCodePlaceholder', 'Postcode'));
        $fields->fieldByName('Phone')
            ->setAttribute('placeholder', _t('Address.PhonePlaceholder', 'Phone'))
            ->setAttribute('data-parsley-type', 'number');
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
