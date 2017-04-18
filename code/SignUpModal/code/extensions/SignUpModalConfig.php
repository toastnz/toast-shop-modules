<?php

/**
 * Class SignUpModalConfig
 *
 * @property string EnableSignUpModal
 * @property string SignUpModalText
 * @property string SignUpSuccessMessage
 *
 * @mixin SiteConfig
 */
class SignUpModalConfig extends DataExtension
{
    private static $db = array(
        'EnableSignUpModal' => 'Boolean',
        'SignUpModalText' => 'HTMLText',
        'SignUpSuccessMessage' => 'HTMLText'
    );

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        /** =========================================
         * @var HtmlEditorField $content
         * @var HtmlEditorField $success
         * @var CheckboxField $enableSignup
        ===========================================*/

        if (Permission::check('ADMIN')) {

            if (!$fields->fieldByName('Root.Toast')) {
                $fields->addFieldToTab('Root', TabSet::create('Toast', 'Shop Settings'));
            }

            /** -----------------------------------------
             * Sign-up Modal
             * ----------------------------------------*/

            $fields->findOrMakeTab('Root.Toast.SignUp', 'SignUp Modal');

            $content = HtmlEditorField::create('SignUpModalText', 'Intro Content');
            $content->setRows(15);

            $success = HtmlEditorField::create('SignUpSuccessMessage', 'Success Message');
            $success->setRows(15);

            $enableSignup = CheckboxField::create('EnableSignUpModal', 'Enable Modal?');
            $enableSignup->addExtraClass('toast-checkbox');

            $fields->addFieldsToTab('Root.Toast.SignUp', array(
                HeaderField::create('Sign-Up Modal'),
                $enableSignup,
                $success,
                $content
            ));

        }
    }

    public static function currentSignUpRecord()
    {
        $recordID = Session::get('Toast.SignUpRecord');

        if (is_numeric($recordID)) {
            return SignUpMessage::get_by_id('SignUpMessage', $recordID);
        }

        return null;
    }
}
