<?php

/**
 * Class AccessoriesCheckoutComponent
 */
class AccessoriesCheckoutComponent extends CheckoutComponent
{
    /**
     * Get form fields for manipulating the current order,
     * according to the responsibilty of this component.
     *
     * @param  Form $form the form being updated
     *
     * @throws Exception
     * @return FieldList fields for manipulating order
     */
    public function getFormFields(Order $order)
    {
        $fields = FieldList::create();

        if ($order->getRelatedProducts() && $order->getRelatedProducts()->exists()) {
            $component = CheckoutStepObject::get()->filter(['Type' => 'Accessories'])->first();

            $fields->push(LiteralField::create('Accessories',
                $component->renderWith('AccessoriesCheckoutComponent')->forTemplate()
            ));
        }

        return $fields;
    }

    /**
     * Is this data valid for saving into an order?
     *
     * This function should never rely on form.
     *
     * @param array $data data to be validated
     *
     * @throws ValidationException
     * @return boolean the data is valid
     */
    public function validateData(Order $order, array $data)
    {
        return true;
    }

    /**
     * Get required data out of the model.
     *
     * @param  Order $order order to get data from.
     *
     * @return array        get data from model(s)
     */
    public function getData(Order $order)
    {
        $data = [];
        return $data;
    }

    /**
     * Set the model data for this component.
     *
     * This function should never rely on form.
     *
     * @param array $data data to be saved into order object
     *
     * @throws Exception
     * @return Order the updated order
     */
    public function setData(Order $order, array $data)
    {
        return $order;
    }
}
