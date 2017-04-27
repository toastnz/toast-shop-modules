<?php

class ExportOrders extends Object
{
    /**
     * Process the cron job task
     *
     * @param string $sRegion The orders region
     * @return bool
     * @throws ValidationException
     * @throws null
     */
    public function process($mRegion = false) {
        // Temp disable the Subsite filter
        Subsite::disable_subsite_filter();
        // Get the country value
        $sCountry = ($mRegion) ? $mRegion : 'nz' ;
        // Set the default Subsite ID
        $iSubsiteID = 0;
        $countryCode = 'nz';
        // Check which country is required
        switch ($sCountry) {
            // Main site
            case 'nz':
                break;
            // Subsites
            default:
                // Try to get the AUS Subsite
                if ($oSubsite = Subsite::get()->filter('CountryCode', $sCountry)->first()) {
                    // Set the AUS Subsite ID
                    $iSubsiteID = $oSubsite->ID;
                    $countryCode = 'au';
                }
                break;
        }
        // Get the SiteConfig for the Subsite
        $oSiteConfig = SiteConfig::get()->filter('SubsiteID', $iSubsiteID)->first();
        // Check that the container folder exists
        $this->checkFolders();
        // Set the path to the write file
        $sOrdersFilePath = BASE_PATH . '/exported_data/BulletBrands_Orders_' . $sCountry . '.txt';
        // Open the file for writing
        $rOrdersFile = fopen($sOrdersFilePath, 'w');
        // Set the header row values
        $sExportHeader = '"Order No","Date","Customer","Address1","Address2","Address3","City","Postal Code","Country","Email","Phone","Stock Codes","Prices","Discounted Prices","Qty","Discount Code","Discount Amount","Delivery Instructions","Shipping","Total","Status","Re-billing Status"' . "\r\n";
        // Write the header row to the file
        fwrite($rOrdersFile, $sExportHeader);
        // Set the script start time. Also used to calculate the execution time at the end of the script.
        $tScriptStartTime = time();
        // Make the start date
        $sScriptStartDate = date('H:i a, D d M Y ', $tScriptStartTime);
        // Set the email data body string
        $sEmailDataBody = '';
        // Set the email data heading string
        $sEmailDataHeading = '<h2>BulletBrands ' . $sCountry . ' Orders Export Script</h2>';
        $sEmailDataHeading .= '<div style="font-size:12px;">BulletBrands  ' . $sCountry . ' orders export script run: ' . $sScriptStartDate . '</div>';
        // Create an hr divider style
        $sTextHr = '<div style="border-bottom:1px dotted #ccc;font-size:1px;margin-bottom:10px;margin-top:10px;">&nbsp;</div>';
        // Add a divider
        $sEmailDataBody .= $sTextHr;
        // Set the default number of exported orders to zero
        $iNumberOfExportedOrders = 0;
        // Get the Order records that have not been auto-exported
        $oOrders = Order::get()->filter(array(
            'AutoExported' => 0,
            'Status' => 'Paid',
            'ShippingAddress.Country' => $countryCode
        ))->limit(1000);
        // If there are any
        if ($oOrders->exists()) {
            // Loop through the Orders
            foreach ($oOrders as $oOrder) {
                // Get the Order fields
                $sReference = $oOrder->Reference();
                $oDate = $oOrder->Placed;
                $sName = sprintf("%s %s", $oOrder->FirstName, $oOrder->Surname);
                $sAddress1 = $oOrder->getAddress1();
                $sAddress2 = $oOrder->getAddress2();
                $sAddress3 = $oOrder->getAddress3();
                $sAddress4 = $oOrder->getAddress4();
                $sAddress5 = $oOrder->getAddress5();
                $sAddress6 = $oOrder->getAddress6();
                $sEmail = $oOrder->Email;
                $sPhone = $oOrder->getPhone();
                $sStockCodes = $oOrder->StockCodes();
                $sItemPrices = $oOrder->ItemPrices(true);
                $sItemDiscountedPrices = $oOrder->ItemDiscountedPrices(true);
                $sItemsQty = $oOrder->ItemsQty();
                $sDiscountCode = $oOrder->getDiscountCode();
                $sDiscountAmount = $oOrder->getDiscountAmount();
                $sNotes = str_replace('"', '', $oOrder->Notes); // Remove double quotes
                $sShippingTotal = $oOrder->ShippingTotal();
                $sTotal = $oOrder->ExportTotal();
                $sStatus = $oOrder->Status;
                $sTokenStatus = $oOrder->TokenStatus;
                // Add the Order details to the email content
                $sEmailDataBody .= '<div style="font-size:14px;"><strong>Name:</strong> ' . $sName . '</div>';
                $sEmailDataBody .= '<div style="font-size:14px;"><strong>Order No.</strong> ' . $sReference . '</div>';
                // Add a divider
                $sEmailDataBody .= $sTextHr;
                // Make the CSV row
                $sExportRow = "\"" .
                              trim($sReference) . "\",\"" .
                              trim($oDate) . "\",\"" .
                              trim($sName) . "\",\"" .
                              trim($sAddress1) . "\",\"" .
                              trim($sAddress2) . "\",\"" .
                              trim($sAddress3) . "\",\"" .
                              trim($sAddress4) . "\",\"" .
                              trim($sAddress5) . "\",\"" .
                              trim($sAddress6) . "\",\"" .
                              trim($sEmail) . "\",\"" .
                              trim($sPhone) . "\",\"" .
                              trim($sStockCodes) . "\",\"" .
                              trim($sItemPrices) . "\",\"" .
                              trim($sItemDiscountedPrices) . "\",\"" .
                              trim($sItemsQty) . "\",\"" .
                              trim($sDiscountCode) . "\",\"" .
                              trim($sDiscountAmount) . "\",\"" .
                              trim($sNotes) . "\",\"" .
                              trim($sShippingTotal) . "\",\"" .
                              trim($sTotal) . "\",\"" .
                              trim($sStatus) . "\",\"" .
                              trim($sTokenStatus) . "\"\r\n";
                // Write the text file data row
                fwrite($rOrdersFile, $sExportRow);
                // Increment the number of Orders
                $iNumberOfExportedOrders++;
                // Set the AutoExported status to 1
//                if ($oOrder->Status == 'Paid') {
//                    $oOrder->AutoExported = 1;
//                    $oOrder->write();
//                }
            }
        }
        // Close the file
        fclose($rOrdersFile);
        // Add the number of exported cars
        $sEmailDataHeading .= '<div style="font-size:12px;">Exported <span style="font-size:16px;font-weight:bold;">' . $iNumberOfExportedOrders . '</span> orders.</div>';
        // Add the execution time to the heading
        $tFinishTime = time() - $tScriptStartTime;

//        echo "End: " . date('H:i a, D d M Y ', time());

        $sEmailDataHeading .= '<div style="font-size:12px;">Execution Time: ' . date('i', $tFinishTime) . ' minutes ' . date('s', $tFinishTime) . ' seconds</div>';
        // Set the email data content
        $sEmailData = $sEmailDataHeading . $sEmailDataBody;
        // Display the email data
        echo '<br />' . $sEmailData;
        // If there are new orders
        if ($iNumberOfExportedOrders && $oSiteConfig->ExportEmailFrom && $oSiteConfig->ExportEmailTo) {
            // Send email messages
            $oEmail = new Email($oSiteConfig->ExportEmailFrom, $oSiteConfig->ExportEmailTo, 'BulletBrands ' . $sCountry . ' website: Order Export Status', $sEmailData);
            // Set the file date
            $sFileDate = date('Ymdhis', time());
            // Attach the export CSVs
            $oEmail->attachFile($sOrdersFilePath, 'BulletBrandsOrders_' . $sCountry . '_' . $sFileDate . '.csv');
            // CC the email
            if ($oSiteConfig->ExportEmailCC) {
                $oEmail->bcc = $oSiteConfig->ExportEmailCC;
            }
            // Send the email
            $oEmail->send();
        }
        // Re-enable the Subsite filter
        Subsite::disable_subsite_filter(false);

        // Return true
        return true;
    }

    public function checkFolders() {
        $arrFolder = array(
            BASE_PATH . '/exported_data/'
        );

        foreach ($arrFolder as $folder) {
            if (!file_exists($folder)) {
                Filesystem::makeFolder($folder);
            }
        }
    }

}
