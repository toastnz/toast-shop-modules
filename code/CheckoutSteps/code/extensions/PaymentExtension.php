<?php

/**
 * Class ToastPaymentComponentExtension
 *
 * @property PaymentCheckoutComponent $owner8
 */
class ToastPaymentComponentExtension extends Extension
{
    public function updateAdditionalComponentFields(FieldList &$fields)
    {
        $fields->push(LiteralField::create('PaymentSummary', ShoppingCart::curr()->renderWith('PaymentSummary')->forTemplate()));
    }
}
