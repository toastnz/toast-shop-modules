<?php

/**
 * Class RegistrationPage
 *
 * @property string MailTo
 *
 * @method ManyManyList Products
 */
class RegistrationPage extends Page
{
    private static $singular_name = 'Registration Page';
    private static $plural_name = 'Registraion Pages';
    private static $description = 'Displays a form for users to register their product and optionally enter a redemption code.';
    private static $icon = 'toast-shop-modules/code/Registration/images/registration-page.png';

    private static $db = [
        'MailTo'             => 'Varchar(500)',
        'SuccessMessage'     => 'Text',
        'CodeSuccessMessage' => 'Text',
    ];

    private static $many_many = [
        'Products' => 'Product'
    ];

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        /** =========================================
         * @var FieldList $fields
         * ========================================*/

        $fields = parent::getCMSFields();

        /** -----------------------------------------
         * Form
         * ----------------------------------------*/

        $products = Product::get()->map()->toArray();

        $fields->addFieldsToTab('Root.Form', [
            HeaderField::create('', 'Form'),
            EmailField::create('MailTo', 'Notification Email')
                ->setDescription('Messages from the form will be sent here.'),
            TextareaField::create('SuccessMessage', 'Success Message'),
            TextareaField::create('CodeSuccessMessage', 'Success Message - with redemption code')
                ->setDescription('If the customer entered a redemption code, this message will display upon submission. The shortcode [product_list] will be replaced with a comma separated list of the linked products.'),
            LiteralField::create('', '<div class="message warning"><p>Choose products that will be available for selection on the registration form.</p></div>'),
            CheckboxSetField::create('Products', 'Available Products', $products)
        ]);

        return $fields;
    }
}

/**
 * Class RegistrationPage_Controller
 *
 * @method RegistrationPage data
 */
class RegistrationPage_Controller extends Page_Controller
{
    private static $allowed_actions = [
        'RegistrationForm'
    ];

    public function RegistrationForm()
    {
        $data['Products'] = $this->data()->Products()->map()->toArray();

        return RegistrationForm::create($this, 'RegistrationForm', $data);
    }

    public function getSuccess()
    {
        return $this->request->getVar('success') == 1;
    }

    public function getSuccessfulMessage()
    {
        /** =========================================
         * @var Promotion $promotion
         * ========================================*/

        $message = '';

        if ($this->getSuccess()) {
            if ($promotionID = Session::get('Promotion.ID')) {
                $promotion = Promotion::get()->byID($promotionID);
                if ($promotion && $promotion->exists()) {
                    $message = $promotion->SuccessMessage;

                    if ($this->CodeSuccessMessage) {
                        $message .= '<br><br>' . $this->CodeSuccessMessage;
                    }

                    $products = $promotion->Products();

                    if ($products && $products->exists()) {
                        $message = str_replace('[product_list]', implode(', ', $products->column('Title')), $message);

                    }
                }
            } elseif ($this->SuccessMessage) {
                $message = $this->SuccessMessage;
            }
        }

        Session::clear('Promotion.ID');

        return $message;
    }
}
