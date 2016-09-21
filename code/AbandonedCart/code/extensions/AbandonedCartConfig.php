<?php

/**
 * Class AbandonedCartConfig
 *
 * @property string EnableAbandonedCart
 * @property string CartEmailContent
 * @property string CartEmailFooterContent
 * @property string CartEmailSubject
 * @property string CartEmailReplyTo
 *
 * @method Image CartEmailHeaderImage()
 * @method Image CartEmailFooterImage()
 *
 * @mixin ShopConfig
 */
class AbandonedCartConfig extends DataExtension
{
    private static $db = array(
        'EnableAbandonedCart' => 'Boolean',
        'CartEmailContent' => 'HTMLText',
        'CartEmailFooterContent' => 'HTMLText',
        'CartEmailSubject' => 'Varchar(255)',
        'CartEmailReplyTo' => 'Varchar(500)'
    );

    private static $has_one = array(
        'CartEmailHeaderImage' => 'Image',
        'CartEmailFooterImage' => 'Image'
    );

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        /** =========================================
         * @var HtmlEditorField $cartContent
         * @var HtmlEditorField $footerContent
         * @var CheckboxField $enableCart
         * @var UploadField $headerImage
         * @var UploadField $footerImage
        ===========================================*/

        if (Permission::check('ADMIN')) {

            if (!$fields->fieldByName('Root.Toast')) {
                $fields->addFieldToTab('Root', TabSet::create('Toast', 'Shop Settings'));
            }

            /** -----------------------------------------
             * Abandoned Cart
             * ----------------------------------------*/

            $fields->findOrMakeTab('Root.Toast.AbandonedCart', 'Abandoned Cart');

            $enableCart = CheckboxField::create('EnableAbandonedCart', 'Enable Abandoned Cart Emails?');
            $enableCart->addExtraClass('toast-checkbox');

            $cartContent = HtmlEditorField::create('CartEmailContent', 'Email Content');
            $cartContent->setRows(15);

            $footerContent = HtmlEditorField::create('CartEmailFooterContent', 'Email Footer Content');
            $footerContent->setRows(15);

            $headerImage = UploadField::create('CartEmailHeaderImage', 'Email Header');
            $headerImage->setFolderName('Uploads/toast')
                ->setDescription('Will be cropped to 600px * 360px');

            $footerImage = UploadField::create('CartEmailFooterImage', 'Email Footer');
            $footerImage->setFolderName('Uploads/toast')
                ->setDescription('Will be cropped to 600px * 260px');

            $fields->addFieldsToTab('Root.Toast.AbandonedCart', array(
                HeaderField::create('Abandoned Cart Reminders'),
                $enableCart,
                TextField::create('CartEmailSubject', 'Email Subject'),
                EmailField::create('CartEmailReplyTo', 'Email Reply-To'),
                LiteralField::create('', '<div class="message warning"><p>Use the shortcode [cart_items] to display a list of the products the customer left in their cart.</p></div>'),
                $cartContent,
                $footerContent,
                $headerImage,
                $footerImage
            ));
        }
    }

}
