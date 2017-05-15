<?php
use SilverStripe\Omnipay\GatewayInfo;

/**
 * Class CheckoutStepsControllerExtension
 *
 * @property CheckoutPage_Controller $owner
 *
 * @todo find all-encompassing override for ajax renderWith responses
 */
class CheckoutStepsControllerExtension extends Extension
{
//    protected $owner;


    public function getStepBlocks()
    {
        $steps = CheckoutPage::config()->steps;

        $list = ArrayList::create();

        foreach ($steps as $step => $stepClass) {
            if ($step == 'membership') {
                if (ShoppingCart::curr() && CheckoutStep_Membership::$skip_if_logged_in && Member::currentUser()) {
                    continue;
                }
            }
            $list->push(ArrayData::create([
                'Title'     => $step,
                'IsCurrent' => $this->owner->IsCurrentStep($step),
                'Link'      => Controller::join_links(Director::absoluteBaseURL(), CheckoutPage::find_link(), $step)
            ]));
        }

        return $this->owner->renderWith('CheckoutSteps', ['Steps' => $list])->forTemplate();
    }

    public function shippingaddress()
    {
        $form = $this->owner->ShippingAddressForm();
        $form->Fields()->push(
            CheckboxField::create(
                "SeperateBilling",
                _t('CheckoutStep_Address.SeperateBilling', "Bill to a different address from this")
            )
        );
        $order = $this->owner->shippingconfig()->getOrder();
        if ($order->BillingAddressID !== $order->ShippingAddressID) {
            $form->loadDataFrom(["SeperateBilling" => 1]);
        }

        if (Director::is_ajax()) {
            return $this->owner->renderWith('CheckoutStep', ['OrderForm' => $form]);
        }
        return ['OrderForm' => $form];
    }

    public function billingaddress()
    {
        $data = ['OrderForm' => $this->owner->BillingAddressForm()];
        if (Director::is_ajax()) {
            return $this->owner->renderWith('CheckoutStep', $data);
        }
        return $data;
    }

    public function paymentmethod()
    {
        $gateways = GatewayInfo::getSupportedGateways();
        if (count($gateways) == 1) {
            return $this->owner->redirect($this->owner->NextStepLink());
        }
        $data = [
            'OrderForm' => $this->owner->PaymentMethodForm(),
        ];
        if (Director::is_ajax()) {
            return $this->owner->renderWith('CheckoutStep', $data);
        }
        return $data;
    }

    public function contactdetails()
    {
        $form = $this->owner->ContactDetailsForm();
        if (
            ShoppingCart::curr()
            && Config::inst()->get("CheckoutStep_ContactDetails", "skip_if_logged_in")
        ) {
            if (Member::currentUser()) {
                if (!$form->getValidator()->validate()) {
                    return Controller::curr()->redirect($this->owner->NextStepLink());
                } else {
                    $form->clearMessage();
                }
            }
        }

        $data = [
            'OrderForm' => $form,
        ];

        if (Director::is_ajax()) {
            return $this->owner->renderWith('CheckoutStep', $data);
        }

        return $data;
    }

    public function membership()
    {
        //if logged in, then redirect to next step
        if (ShoppingCart::curr() && CheckoutStep_Membership::$skip_if_logged_in && Member::currentUser()) {
            Controller::curr()->redirect($this->owner->NextStepLink());
            return;
        }

        $data = [
            'Form'      => $this->owner->MembershipForm(),
            'LoginForm' => $this->owner->LoginForm(),
            'GuestLink' => $this->owner->NextStepLink(),
        ];

        if (Director::is_ajax()) {
            return $this->owner->customise(
                $data
            )->renderWith(
                ["CheckoutPage_membership"]
            ); //needed to make rendering work on index
        } else {
            return $this->owner->customise(
                $data
            )->renderWith(
                ["CheckoutPage_membership", "CheckoutPage", "Page"]
            ); //needed to make rendering work on index
        }


    }

    public function index(SS_HTTPRequest $request)
    {
        if (Director::is_ajax()) {
            $steps  = CheckoutPage::config()->steps;
            $action = $request->param('Action');

            if (CheckoutPage::config()->first_step && !$action) {
                return $this->owner->{CheckoutPage::config()->first_step}();
            }

            if (in_array($action, $steps)) {
                $className = $steps[$action];
                $data      = singleton($className)->{$action}();
                return $this->owner->renderWith('CheckoutStep', $data);
            }
        }

        if (CheckoutPage::config()->first_step) {
            return $this->owner->{CheckoutPage::config()->first_step}();
        }
        return [];
    }
}