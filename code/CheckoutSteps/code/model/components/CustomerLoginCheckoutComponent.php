<?php

/**
 * Class CustomerLoginCheckoutComponent
 */
class CustomerLoginCheckoutComponent extends CheckoutComponent
{
    protected $requiredfields = array(
        'FirstName',
        'Surname',
        'Email',
    );

    public function getFormFields(Order $order)
    {
        $fields = FieldList::create([
            // Switcher
            LiteralField::create('SwitchWrapper', '<div id="CustomerSwitch_wrapper">'),
            HeaderField::create('LoginHeader', _t('TOASTSHOP.GuestOrRegister', 'Checkout as a Guest or Register'), 5),
            OptionsetField::create('GuestOrRegister', '', [
                'Guest' => 'Checkout as Guest',
                'Register' => 'Register'
            ]),
            LiteralField::create('SwitchClose', '</div>'),

            LiteralField::create('GuestWrapper', '<div id="Guest_wrapper">'),
            $firstname = TextField::create('FirstName', _t('Order.db_FirstName', 'First Name')),
            $surname = TextField::create('Surname', _t('Order.db_Surname', 'Surname')),
            $email = EmailField::create('Email', _t('Order.db_Email', 'Email')),
            LiteralField::create('GuestClose', '</div>')
        ]);

        $firstname->setAttribute('placeholder',  _t('Order.db_FirstName', 'First Name'));
        $surname->setAttribute('placeholder',  _t('Order.db_Surname', 'Surname'));
        $email->setAttribute('placeholder',  _t('Order.db_Email', 'Email'));

        $loginForm = new MemberLoginForm(Controller::curr(), 'LoginForm');
        $loginFields = $loginForm->Fields();

        $loginFields->unshift(HeaderField::create('LoginHeader', _t('TOASTSHOP.LoginHeader', 'Please log in below'), 5));
        $loginFields->unshift(LiteralField::create('LoginWrapper', '<div id="Login_wrapper">'));

        $loginFields->push(LiteralField::create('LoginClose', '</div>'));

        $loginFields->merge($fields);

        return $fields;
    }

    public function validateData(Order $order, array $data)
    {
        //all fields are required
    }

    public function getData(Order $order)
    {
        if ($order->FirstName || $order->Surname || $order->Email) {
            return array(
                'FirstName' => $order->FirstName,
                'Surname'   => $order->Surname,
                'Email'     => $order->Email,
            );
        }
        if ($member = Member::currentUser()) {
            return array(
                'FirstName' => $member->FirstName,
                'Surname'   => $member->Surname,
                'Email'     => $member->Email,
            );
        }
        return array();
    }

    public function setData(Order $order, array $data)
    {
        $order->update($data);
        $order->write();
    }
}
