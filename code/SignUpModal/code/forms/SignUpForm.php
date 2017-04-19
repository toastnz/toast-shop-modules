<?php

/**
 * Class SignUpForm
 */
class SignUpForm extends Form
{
    public function __construct($controller, $name, $arguments = array())
    {
        /** =========================================
         * @var Form $form
         * @var FormAction $submit
        ===========================================*/

        /** -----------------------------------------
         * Fields
         * ----------------------------------------*/

        $fields = $this->createFields();

        /** -----------------------------------------
         * Actions
         * ----------------------------------------*/

        $submit = FormAction::create('Submit');
        $submit->setTitle(_t('Toast.SIGNUP_BUTTON', 'Sign me up'))
            ->addExtraClass('btn btn-primary');

        $actions = FieldList::create(
            $submit
        );

        /** -----------------------------------------
         * Validation
         * ----------------------------------------*/

        $required = RequiredFields::create(array(
            'Name',
            'Contact'
        ));

        /** -----------------------------------------
         * Form
         * ----------------------------------------*/

        $form = Form::create($this, $name, $fields, $actions, $required);

        if ($arguments) {
            $form->loadDataFrom($arguments);
        }

        if ($formData = Session::get('FormInfo.Form_' . $name . '.data')) {
            $form->loadDataFrom($formData);
        }

        parent::__construct($controller, $name, $fields, $actions, $required);

        $this->setAttribute('data-parsley-validate', true);
        $this->setAttribute('autocomplete', 'on');
        $this->addExtraClass('form');
    }

    private function createFields()
    {
        /** =========================================
         * @var FieldList     $fields
         * @var TextField     $name
         * @var EmailField    $email
         * @var EmailField    $honeypot
        ===========================================*/

        /** -----------------------------------------
         * Fields
         * ----------------------------------------*/

        $name    = TextField::create('Name', _t('Toast.SIGNUP_NAME', 'First Name'));
        $email   = EmailField::create('Contact', _t('Toast.SIGNUP_EMAIL', 'Email'));

        $honeypot = EmailField::create('Email', 'SPAM PROTECTION - DO NOT FILL THIS FIELD IN');

        /** -----------------------------------------
         * Customise
         * ----------------------------------------*/

        $name->setAttribute('placeholder', _t('Toast.SIGNUP_NAME_PLACEHOLDER', ''));
        $email->setAttribute('placeholder', _t('Toast.SIGNUP_EMAIL_PLACEHOLDER', ''));

        $honeypot->addExtraClass('honeypot');

        /** -----------------------------------------
         * Return
         * ----------------------------------------*/

        $fields = FieldList::create(array(
            $name,
            $email,
            $honeypot,
        ));

        return $fields;
    }

    public function Submit($data, $form)
    {
        /** =========================================
         * @var Form  $form
         * @var 0Email $email
         * @var SignUpMessage $record
        ===========================================*/

        $data       = $form->getData();
        $siteConfig = SiteConfig::current_site_config();

        // Save data to session
        Session::set('FormInfo.Form_' . $this->name . '.data', $data);

        if (isset($data['Email']) && $data['Email'] != '') {
            $form->setMessage('Spam protection field should be empty', 'bad');
            return $this->controller->redirect($this->controller->Link());
        }

        $data['Email'] = $data['Contact'];

        /** -----------------------------------------
         * Record
         * ----------------------------------------*/

        $record = SignUpMessage::create($data);
        $recordID = $record->write();

        Session::set('Toast.SignUpRecord', $recordID);

        // Set the cart if it exists
        if ($cartID = Session::get('shoppingcartid')) {
            $record->CartID = $cartID;
            $record->write();
        }

        /** -----------------------------------------
         * Finish
         * ----------------------------------------*/

        Session::clear('FormInfo.Form_' . $this->name . '.data');

        $message = $siteConfig->dbObject('SignUpSuccessMessage')->forTemplate() ? : '<p>Your enquiry has been received.</p>';

        $this->setMessage($message, 'success');

        if ($this->request->isAjax()) {
            $data = array(
                'record_id' => $recordID,
                'html' => $message
            );
            return json_encode($data,  JSON_HEX_QUOT | JSON_HEX_TAG);
        } else {
            return $this->controller->redirect($this->controller->data()->Link('?success=1'));
        }
    }

    public function getSuccess()
    {
        return isset($_REQUEST['success']) && $_REQUEST['success'] == "1";
    }
}
