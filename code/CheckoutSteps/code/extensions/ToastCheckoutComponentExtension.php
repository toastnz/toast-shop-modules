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

    /**
     * @return string
     */
    public function getExtraClasses()
    {
        $extraClasses = $this->owner->Name();

        $this->owner->extend('updateExtraClasses', $extraClasses);

        return $extraClasses;
    }
}
