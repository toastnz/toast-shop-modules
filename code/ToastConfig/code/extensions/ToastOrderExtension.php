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
