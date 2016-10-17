<?php

/**
 * Class ToastConfig
 *
 * @property ToastConfig|SiteConfig $owner
 */
class ToastConfig extends DataExtension
{
    private static $db = array(
    );

    private static $has_one = array(
    );

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        /** =========================================
         * @var HtmlEditorField $content
        ===========================================*/

        if (Permission::check('ADMIN')) {

            if (!$fields->fieldByName('Root.Toast')) {
                $fields->addFieldToTab('Root', TabSet::create('Toast', 'Shop Settings'));
            }
        }
    }

}
