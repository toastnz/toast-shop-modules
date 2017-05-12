<?php

/**
 * Class CheckoutStepsControllerExtension
 */
class CheckoutStepsControllerExtension extends Extension
{
    /** @var CheckoutPage_Controller */
    protected $owner;

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
                'Title' => $step,
                'IsCurrent' => $this->owner->IsCurrentStep($step),
                'Link' => Controller::join_links(Director::absoluteBaseURL(), CheckoutPage::find_link(), $step)
            ]));
        }

        return $this->owner->renderWith('CheckoutSteps', ['Steps' => $list])->forTemplate();
    }


    public function index(SS_HTTPRequest $request)
    {
        if (Director::is_ajax()) {
            $steps = CheckoutPage::config()->steps;
            $action = $request->param('Action');

            if (CheckoutPage::config()->first_step && !$action) {
                return $this->owner->{CheckoutPage::config()->first_step}();
            }

            if (in_array($action, $steps)) {
                $className = $steps[$action];
                $data = singleton($className)->{$action}();
                return $this->owner->renderWith('CheckoutStep', $data);
            }
        }

        if (CheckoutPage::config()->first_step) {
            return $this->owner->{CheckoutPage::config()->first_step}();
        }
        return [];
    }
}