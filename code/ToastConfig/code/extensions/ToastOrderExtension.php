<?php

/**
 * Class ToastCartExtension
 */
class ToastCartExtension extends DataExtension
{
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

        if ($recordID = Session::get('CDB.SignUpRecord')) {
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
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();

        /** -----------------------------------------
         * Create Record
         * ----------------------------------------*/


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
            if ($recordID = Session::get('CDB.SignUpRecord')) {
                $record = SignUpMessage::get()->byID($recordID);
                if ($record && $record->exists()) {
                    $record->setField('CartID', $this->owner->ID);
                    if ($record->isChanged('CartID')) {
                        $record->write();
                    }
                }
            }
        }
    }
}
