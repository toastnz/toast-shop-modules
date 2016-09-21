<?php

/**
 * Class ToastAdmin
 */
class ToastAdmin extends ModelAdmin
{
    private static $managed_models = array(
        'SignUpMessage' => array(
            'title' => 'Sign-up Messages'
        )
    );

    private static $url_segment = 'toast-settings';

    private static $menu_title = 'Shop Settings';

    private static $url_handlers = array(
        '$ModelClass/$Action' => 'handleAction',
        '$ModelClass/$Action/$ID' => 'handleAction',
    );

    public function getEditForm($id = null, $fields = null)
    {
        /** =========================================
         * @var Form $form
         * @var GridField $gridField
        ===========================================*/

        $form = parent::getEditForm($id, $fields);

//        if ($this->modelClass == 'Model' && $gridField = $form->Fields()->dataFieldByName($this->sanitiseClassName($this->modelClass))) {
//            /**
//             * This is just a precaution to ensure we got a GridField from dataFieldByName() which you should have
//             */
//            if ($gridField instanceof GridField) {
//                $gridField->getConfig()->addComponent(new GridFieldOrderableRows('SortOrder'));
//            }
//        }

        return $form;
    }

}
