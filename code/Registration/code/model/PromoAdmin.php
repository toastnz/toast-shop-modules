<?php

/**
 * Class PromoAdmin
 */
class PromoAdmin extends ModelAdmin
{
    private static $managed_models = [
        'Promotion'           => [
            'title' => 'Promotions'
        ],
        'PromoCode'           => [
            'title' => 'Redemption Codes'
        ],
        'RegistrationMessage' => [
            'title' => 'Messages'
        ]
    ];

    private static $model_importers = [
        'PromoCode' => 'PromoCsvBulkLoader',
    ];


    private static $url_segment = 'promo-codes';

    private static $menu_title = 'Promotions';

    private static $url_handlers = [
        '$ModelClass/$Action'     => 'handleAction',
        '$ModelClass/$Action/$ID' => 'handleAction',
    ];

    public function getEditForm($id = null, $fields = null)
    {
        /** =========================================
         * @var Form      $form
         * @var GridField $gridField
        ===========================================*/

        $form = parent::getEditForm($id, $fields);

        if ($this->modelClass == 'PromoCode' && $gridField = $form->Fields()->dataFieldByName($this->sanitiseClassName($this->modelClass))) {
            /**
             * This is just a precaution to ensure we got a GridField from dataFieldByName() which you should have
             */
            if ($gridField instanceof GridField) {
//                $gridField->getConfig()->addComponent(new GridFieldOrderableRows('SortOrder'));
            }
        }

        return $form;
    }

}
