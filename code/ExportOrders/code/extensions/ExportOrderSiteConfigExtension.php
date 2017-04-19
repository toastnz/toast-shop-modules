<?php

/**
 * Class ExportOrderSiteConfigExtension
 */
class ExportOrderSiteConfigExtension extends DataExtension
{
    private static $db = [
        'ExportEmailFrom' => 'Varchar(255)',
        'ExportEmailTo' => 'Varchar(255)',
        'ExportEmailCC' => 'Varchar(255)'
    ];

    public function updateCMSFields(FieldList $fields) {

        if (!$fields->fieldByName('Root.Settings')) {
            $fields->addFieldToTab('Root', TabSet::create('Settings'));
        }
        /** -----------------------------------------
         * Data Exports
         * -------------------------------------------*/

        // Check the Subsite and set the default order export email address
        // NOTE: Used for the field description hints only.
        switch (Subsite::currentSubsiteID()) {
            case '0':
            default:
                $sOrderExportEmailTo = (defined('DEFAULT_NZ_ORDER_EXPORT_EMAIL_TO')) ? DEFAULT_NZ_ORDER_EXPORT_EMAIL_TO : '';
                break;
            case '1':
                $sOrderExportEmailTo = (defined('DEFAULT_AUS_ORDER_EXPORT_EMAIL_TO')) ? DEFAULT_AUS_ORDER_EXPORT_EMAIL_TO : '';
                break;
            case '2':
                $sOrderExportEmailTo = (defined('DEFAULT_UK_ORDER_EXPORT_EMAIL_TO')) ? DEFAULT_UK_ORDER_EXPORT_EMAIL_TO : '';
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

        // Get the website country
        $sCountry = (defined('SS_AUSTRALIA')) ? 'AUS' : 'NZ' ;
        // Add the fields to the Data Exports tab
        $fields->addFieldsToTab('Root.DataExports', array(
            HeaderField::create('', 'Export Email Addresses'),
            TextField::create('ExportEmailFrom')->setRightTitle($sDefaultOrderExportEmailFrom),
            TextField::create('ExportEmailTo')->setRightTitle($sDefaultOrderExportEmailTo),
            TextField::create('ExportEmailCC', 'Export CC')->setRightTitle('admin@toast.co.nz')
        ));

    }

}
