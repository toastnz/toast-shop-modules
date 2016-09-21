<?php

/**
 * Class SignUpModalExtension
 *
 * @property ContentController $owner
 */
class SignUpModalExtension extends Extension
{
    private static $allowed_actions = array(
        'SignUpForm'
    );

    public function SignUpForm()
    {
        /** =========================================
         * @var SignUpForm $form
         * ========================================*/

        $form = SignUpForm::create($this->owner, 'SignUpForm');

        return $form;
    }

    public function onAfterInit()
    {
        /** -----------------------------------------
         * Custom Styles
         * ----------------------------------------*/

        $styles = <<<CSS
.field.honeypot {
    display: none;
}
CSS;

        Requirements::customCSS($styles, 'ToastFrontendStyles');
    }

    /**
     * Template helper
     *
     * @return bool
     */
    public function DisplayModal()
    {
        if (SiteConfig::current_site_config()->EnableSignUpModal) {
            if (Cookie::get('Toast_promo_modal')) {
                return false;
            }

            if (!Director::isDev()) {
                Cookie::set('Toast_promo_modal', true, 30);
            }
            return true;
        }

        return false;
    }
}
