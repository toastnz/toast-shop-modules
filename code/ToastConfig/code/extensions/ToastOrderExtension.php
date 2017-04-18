<?php

/**
 * Class ToastOrderExtension
 *
 * @property Order $owner
 */
class ToastOrderExtension extends DataExtension
{
    public function getRelatedProducts()
    {
        /** =========================================
         * @var ArrayList $related
         * ========================================*/

        $itemIDs = $this->owner->Items()->column();
        $related = ArrayList::create();

        if ($itemIDs) {
            $products = Versioned::get_by_stage('Product', Versioned::current_stage())
                ->leftJoin('Product_OrderItem', '"Product_OrderItem"."ProductID" = "Product"."ID"')
                ->where('"Product_OrderItem"."ID" IN (' . implode(',',$itemIDs) . ')');


            foreach ($products as $product) {
                if ($product->RelatedProducts() && $product->RelatedProducts()->exists()) {
                    $related->merge($product->RelatedProducts());
                }
            }

            // Remove products already in the cart
            $related = $related->exclude(['ID' => $products->column()]);
        }

        return $related;
    }

    /**
     * @return null|OrderDiscountModifier
     */
    public function getDisplayPromoButton()
    {
        return $this->owner->getModifier('OrderDiscountModifier');
    }

    /**
     * @param Address $address
     * @throws ValidationException
     * @throws null
     */
    public function onSetShippingAddress($address)
    {
        /** =========================================
         * @var SignUpMessage $record
         * ========================================*/

        if ($recordID = Session::get('Toast.SignUpRecord')) {
            $record = SignUpMessage::get()->byID($recordID);
            if ($record && $record->exists()) {
                $record->setField('CartID', $this->owner->ID);
                if ($record->isChanged('CartID')) {
                    $record->write();
                }
            }
        } else {
            $postVars = Controller::curr()->getRequest()->postVars();

            if (isset($postVars['FirstName']) && isset($postVars['Email'])) {
                $record = SignUpMessage::create(array(
                    'Name' => $postVars['FirstName'],
                    'Email' => $postVars['Email'],
                    'CartID' => $this->owner->ID
                ));
                $record->write();
            }
        }

        /** -----------------------------------------
         * Member
         * ----------------------------------------*/

        // Check if the order has a firstname/email
        if (empty($this->owner->FirstName) || empty($this->owner->Email)) {
            // Set them to be the shipping address
            $this->owner->setField('FirstName', $address->FirstName);
            $this->owner->setField('Surname', $address->Surname);
            $this->owner->setField('Email', $address->Email);
//            $this->owner->write();
        }
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();

        /** -----------------------------------------
         * Clear Abandoned Cart
         * ----------------------------------------*/

        if ($this->owner->Status != 'Cart') {
            $record = SignUpMessage::get()->filter(array(
                'Status' => 'New',
                'CartID' => $this->owner->ID
            ))->first();

            if ($record && $record->exists()) {
                $record->setField('Status', 'Processed');
                $record->write();
            }
        }

        if ($this->owner->Status == 'Cart') {
            if ($recordID = Session::get('Toast.SignUpRecord')) {
                $record = SignUpMessage::get()->byID($recordID);
                if ($record && $record->exists()) {
                    $record->setField('CartID', $this->owner->ID);
                    if ($record->isChanged('CartID')) {
                        $record->write();
                    }
                }
            } elseif ($this->owner->Email) {
                if (!SignUpMessage::get()->filter(array(
                    'CartID' => $this->owner->ID
                ))->first()
                ) {
                    $record   = SignUpMessage::create(array(
                        'Email' => $this->owner->Email,
                        'Name' => $this->owner->FirstName,
                        'Status' => 'New',
                        'CartID' => $this->owner->ID
                    ));
                    $recordID = $record->write();

                    Session::set('Toast.SignUpRecord', $recordID);
                }
            }
        }
    }
}
