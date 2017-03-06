<?php

/**
 * Class ToastCheckoutComponentExtension
 *
 * @property $owner CheckoutComponent
 */
class ToastCheckoutComponentExtension extends Extension
{
    public function getAdditionalComponentFields()
    {
        $fields = FieldList::create();

        $this->owner->extend('updateAdditionalComponentFields', $fields);

        return $fields;
    }
}
