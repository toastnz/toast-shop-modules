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
             * Data Exports
             * -------------------------------------------*/

            // Check the Subsite and set the default order export email address
            // NOTE: Used for the field description hints only.

            $fields->findOrMakeTab('Root.Toast.Exports', 'Exports');

            $sOrderExportEmailFrom = Config::inst()->get('Email', 'admin_email');
            $sOrderExportEmailTo = Config::inst()->get('Email', 'admin_email');

            if (class_exists('Subsite')) {
                switch (Subsite::currentSubsiteID()) {
                    case '0':
                    default:
                        $sOrderExportEmailFrom = (defined('DEFAULT_NZ_ORDER_EXPORT_EMAIL_FROM')) ? DEFAULT_NZ_ORDER_EXPORT_EMAIL_FROM : $sOrderExportEmailFrom;
                        $sOrderExportEmailTo = (defined('DEFAULT_NZ_ORDER_EXPORT_EMAIL_TO')) ? DEFAULT_NZ_ORDER_EXPORT_EMAIL_TO : $sOrderExportEmailTo;
                        break;
                    case '1':
                        $sOrderExportEmailFrom = (defined('DEFAULT_AUS_ORDER_EXPORT_EMAIL_FROM')) ? DEFAULT_AUS_ORDER_EXPORT_EMAIL_FROM : $sOrderExportEmailFrom;
                        $sOrderExportEmailTo = (defined('DEFAULT_AUS_ORDER_EXPORT_EMAIL_TO')) ? DEFAULT_AUS_ORDER_EXPORT_EMAIL_TO : $sOrderExportEmailTo;
                        break;
                }
            }

            // If the site is in dev mode
            if (!Director::isLive()) {
                $sDefaultOrderExportEmailTo = (defined('DEFAULT_DEV_ORDER_EXPORT_EMAIL_TO')) ? DEFAULT_DEV_ORDER_EXPORT_EMAIL_TO : '';
            // Live mode, use the regional email address
            } else {
                $sDefaultOrderExportEmailTo = $sOrderExportEmailTo;
            }

            // Add the fields to the Data Exports tab
            $fields->addFieldsToTab('Root.Toast.Exports', [
                HeaderField::create('', 'Export Email Addresses'),
                EmailField::create('ExportEmailFrom')->setRightTitle($sOrderExportEmailFrom),
                EmailField::create('ExportEmailTo')->setRightTitle($sDefaultOrderExportEmailTo),
                EmailField::create('ExportEmailCC', 'Export CC')->setRightTitle('admin@toast.co.nz')
            ]);

        }
    }
}
