<?php

/**
 * Class RegistrationForm
 */
class RegistrationForm extends Form
{
    public function __construct($controller, $name, $arguments = [])
    {
        /** =========================================
         * @var Form $form
        ===========================================*/

        /** -----------------------------------------
         * Fields
         * ----------------------------------------*/

        $fields = $this->createFields($arguments);

        /** -----------------------------------------
         * Actions
         * ----------------------------------------*/

        $actions = FieldList::create(
            FormAction::create('Submit')
                ->setTitle('SEND')
        );

        /** -----------------------------------------
         * Validation
         * ----------------------------------------*/

        $required = RequiredFields::create([
            'FirstName',
            'Surname',
            'Contact',
            'Address',
            'City'
        ]);

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

        if ($formMessage = Session::get('FormInfo.Form_' . $this->name . '.message')) {
            $this->setMessage($formMessage, 'bad');
        }

        $this->setAttribute('data-parsley-validate', true);
        $this->setAttribute('autocomplete', 'on');
        $this->addExtraClass('form');
    }

    /**
     * @param array $arguments
     * @return FieldList
     */
    private function createFields($arguments = [])
    {
        /** =========================================
         * @var FieldList     $fields
         * @var TextField     $firstName
         * @var TextField     $surname
         * @var EmailField    $email
         * @var TextareaField $address
         * @var TextField     $city
         * @var TextField     $postalCode
         * @var TextField     $phone
         * @var TextField     $store
         * @var DateField     $date
         * @var TextField     $code
         * @var DropdownField $products
         * @var EmailField    $honeypot
        ===========================================*/

        /** -----------------------------------------
         * Fields
         * ----------------------------------------*/

        $firstName  = TextField::create('FirstName', 'First Name *');
        $surname    = TextField::create('Surname', 'Surname *');
        $email      = EmailField::create('Contact', 'Email *');
        $address    = TextareaField::create('Address', 'Address *');
        $city       = TextField::create('City', 'City *');
        $postalCode = TextField::create('PostalCode', 'Postal Code');
        $phone      = TextField::create('Phone', 'Phone');
        $store      = TextField::create('Store', 'Store');
        $date       = DateField::create('DateOfPurchase', 'Date of Purchase');
        $code       = TextField::create('PromoCode', 'Redemption Code');

        $honeypot = EmailField::create('Email', 'SPAM PROTECTION - DO NOT FILL THIS FIELD IN');

        /** -----------------------------------------
         * Customise
         * ----------------------------------------*/

        $date->setConfig('showcalendar', true)
            ->setConfig('dateformat', 'dd/MM/YYYY')
            ->setAttribute('placeholder', 'dd/mm/yyyy');

        $honeypot->addExtraClass('honeypot');

        /** -----------------------------------------
         * Return
         * ----------------------------------------*/

        $fields = FieldList::create([
            $firstName,
            $surname,
            $email,
            $address,
            $city,
            $postalCode,
            $phone,
            $store,
            $date,
            $code,
            $honeypot,
        ]);

        /** -----------------------------------------
         * Product Dropdown
         * ----------------------------------------*/

        if (isset($arguments['Products'])) {
            $products = DropdownField::create('RegisteredProductID', 'Machine', $arguments['Products']);
            $products->setEmptyString('( Please Choose )');

            $fields->insertBefore('FirstName', $products);
        }

        return $fields;
    }

    public function Submit($data, $form)
    {
        /** =========================================
         * @var Form                $form
         * @var Email               $email
         * @var RegistrationMessage $registrationMessage
         * @var PromoCode           $promoCode
        ===========================================*/

        $data       = $form->getData();
        $siteConfig = SiteConfig::current_site_config();
        $promoCode  = null;

        // Save data to session
        Session::set('FormInfo.Form_' . $this->name . '.data', $data);

        if (isset($data['Email']) && $data['Email'] != '') {
            if ($this->request->isAjax()) {
                Session::set('FormInfo.Form_' . $this->name . '.message', 'Spam protection field should be empty.');
                return json_encode([
                    'success' => true,
                    'message' => 'Spam protection field should be empty.'
                ]);
            } else {
                Session::set('FormInfo.Form_' . $this->name . '.message', 'Spam protection field should be empty.');
                return $this->controller->redirect($this->controller->data()->Link('?success=1'));
            }
        }

        $data['Email'] = $data['Contact'];

        /** -----------------------------------------
         * Record
         * ----------------------------------------*/

        $registrationMessage = RegistrationMessage::create($data);
        $registrationID      = $registrationMessage->write();

        // Set reference
        $registrationMessage->setField('Reference', 'RED' . $registrationID);
        $registrationMessage->write();

        /** -----------------------------------------
         * Redemption Code
         * ----------------------------------------*/

        if (isset($data['PromoCode']) && !empty($data['PromoCode'])) {
            // Get a promo code that is not redeemed, is valid, and
            // is associated with the selected product

            $promoCode = PromoCode::get()->filter([
                'Redeemed' => 0,
                'Code'     => $data['PromoCode']
            ])->first();

            // If a promo code is found, update with form data
            if ($promoCode && $promoCode->exists()) {
                $promoCode->update([
                    'Redeemed'  => 1,
                    'MessageID' => $registrationID
                ]);
                $promoCode->write();

                $registrationMessage->setField('PromoCodeID', $promoCode->ID);
                $registrationMessage->write();
            } else {
                if ($this->request->isAjax()) {
                    Session::set('FormInfo.Form_' . $this->name . '.message', 'Redemption code does not exist.');

                    return json_encode([
                        'success' => true,
                        'message' => 'Redemption code does not exist.'
                    ]);

                } else {
                    Session::set('FormInfo.Form_' . $this->name . '.message', 'Redemption code does not exist.');
                    return $this->controller->redirect($this->controller->Link());
                }
            }
        }

        /** -----------------------------------------
         * Email
         * ----------------------------------------*/

        $to      = Config::inst()->get('Email', 'admin_email');
        $from    = $data['Email'];
        $subject = 'Enquiry received.';

        if ($promoCode && $promoCode->exists()) {
            $data['PromoCode'] = $promoCode;
        }

        $email = Email::create($from, $to, $subject);

        $email->setTemplate('RegistrationEmail')
            ->populateTemplate($data)
            ->send();

        /** -----------------------------------------
         * Finish
         * ----------------------------------------*/

        Session::clear('FormInfo.Form_' . $this->name . '.data');
        Session::clear('FormInfo.Form_' . $this->name . '.message');

        $message = 'Your enquiry has been received.';

        if ($promoCode && $promoCode->exists()) {
            // Get the promotion
            $promotion = $promoCode->Promotion();

            if ($promotion && $promotion->exists()) {
                $message = $promotion->SuccessMessage;
            }

            Session::set('Promotion.ID', $promotion->ID);
        }

        $this->setMessage($message, 'success');

        if ($this->request->isAjax()) {
            return json_encode([
                'success' => true,
                'message' => $message
            ]);
        } else {
            return $this->controller->redirect($this->controller->data()->Link('?success=1'));
        }
    }

    public function getSuccess()
    {
        return isset($_REQUEST['success']) && $_REQUEST['success'] == "1";
    }
}
