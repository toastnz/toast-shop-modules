<?php

/**
 * Class ToastOrdersAdmin
 *
 * @property OrdersAdmin $owner
 */
class ToastOrdersAdmin extends Extension
{
    public function updateEditForm(CMSForm $form)
    {
        $fields = $form->Fields();
        if ($gridField = $fields->dataFieldByName('SignUpMessage')) {
            if ($gridField instanceof GridField) {
                $gfConfig = $gridField->getConfig();
                $gfConfig->removeComponentsByType('GridFieldAddNewButton');
            }
        }
    }
}
