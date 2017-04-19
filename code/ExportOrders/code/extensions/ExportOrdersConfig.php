<?php

/**
 * Class ExportOrdersConfig
 *
 * @property SiteConfig $owner
 */
class ExportOrdersConfig extends DataExtension
{
    private static $db = [
        'ExportEmailFrom' => 'Varchar(255)',
        'ExportEmailTo' => 'Varchar(255)',
        'ExportEmailCC' => 'Varchar(255)'
    ];

    public function updateCMSFields(FieldList $fields)
    {

        if (Permission::check('ADMIN')) {

            if (!$fields->fieldByName('Root.Toast')) {
                $fields->addFieldToTab('Root', TabSet::create('Toast', 'Shop Settings'));
            }

            /** -----------------------------------------
             * Sign-up Modal
             * ----------------------------------------*/

            $fields->findOrMakeTab('Root.Toast.Exports', 'Exports');

            switch (Subsite::currentSubsiteID()) {
                case '0':
                default:
                    $sOrderExportEmailTo = (defined('DEFAULT_NZ_ORDER_EXPORT_EMAIL_TO')) ? DEFAULT_NZ_ORDER_EXPORT_EMAIL_TO : '';
                    break;
                case '1':
                    $sOrderExportEmailTo = (defined('DEFAULT_AUS_ORDER_EXPORT_EMAIL_TO')) ? DEFAULT_AUS_ORDER_EXPORT_EMAIL_TO : '';
                    break;
            }
            // If the site is in dev mode
            if (!Director::isLive()) {
                $sDefaultOrderExportEmailTo = (defined('DEFAULT_DEV_ORDER_EXPORT_EMAIL_TO')) ? DEFAULT_DEV_ORDER_EXPORT_EMAIL_TO : '';
                // Live mode, use the regional email address
            } else {
                $sDefaultOrderExportEmailTo = $sOrderExportEmailTo;
            }
            $sDefaultOrderExportEmailFrom = (defined('DEFAULT_ORDER_EXPORT_EMAIL_FROM')) ? DEFAULT_ORDER_EXPORT_EMAIL_FROM : '';

            // Add the fields to the Data Exports tab
            $fields->addFieldsToTab('Root.Toast.Exports', [
                HeaderField::create('', 'Export Email Addresses'),
                EmailField::create('ExportEmailFrom')->setRightTitle($sDefaultOrderExportEmailFrom),
                EmailField::create('ExportEmailTo')->setRightTitle($sDefaultOrderExportEmailTo),
                EmailField::create('ExportEmailCC', 'Export CC')->setRightTitle('admin@toast.co.nz')
            ]);

        }
    }
}
