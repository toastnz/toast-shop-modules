<?php

/**
 * Class CustomerDetailsComponentExtension
 *
 * @property CustomerDetailsCheckoutComponent $owner
 */
class CustomerDetailsComponentExtension extends Extension
{
    protected $requiredfields = array(

    );

    public function updateAdditionalComponentFields(FieldList &$fields)
    {
        $loginForm = new MemberLoginForm(Controller::curr(), 'LoginForm');
        $loginFields = $loginForm->Fields();

        $loginFields->unshift(HeaderField::create('LoginHeader', _t('TOASTSHOP.LoginHeader', 'Please log in below'), 5));
        $loginFields->unshift(LiteralField::create('LoginWrapper', '<div id="Login_wrapper">'));

        $loginFields->push(LiteralField::create('LoginClose', '</div>'));

        $fields->merge($loginFields);
    }
}
