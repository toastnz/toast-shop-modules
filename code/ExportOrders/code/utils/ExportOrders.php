<?php

class ExportOrders extends Object
{
    /**
     * Process the cron job task
     *
     * @return bool
     * @throws ValidationException
     * @throws null
     */
    public function process() {
        // Check that the container folder exists
        $this->checkFolders();
        // Get the SiteConfig object
        $oSiteConfig = SiteConfig::current_site_config();
        // Set the path to the write file
        $sOrdersFilePath = BASE_PATH . '/exported_orders/NutriBullet_Orders_' . $oSiteConfig->ExportCountry . '.txt';
        // Open the file for writing
        $rOrdersFile = fopen($sOrdersFilePath, 'w');
        // Set the header row values
        $export_header = '"Order No","Date","Customer","Address1","Address2","Address3","Address4","Address5","Address6","Email","Phone","Stock Codes","Prices","Qty","Discount","Delivery Instructions","Shipping","Total","Status"' . "\r\n";
        // Write the header row to the file
        fwrite($rOrdersFile, $export_header);
        // Set the script start time. Also used to calculate the execution time at the end of the script.
        $tScriptStartTime = time();
        // Make the start date
        $sScriptStartDate = date('H:i a, D d M Y ', $tScriptStartTime);
        // Set the email data body string
        $sEmailDataBody = '';
        // Set the email data heading string
        $sEmailDataHeading = '<h2>NutriBullet ' . $oSiteConfig->ExportCountry . ' Orders Export Script</h2>';
        $sEmailDataHeading .= '<div style="font-size:12px;">NutriBullet  ' . $oSiteConfig->ExportCountry . '  orders export script run: ' . $sScriptStartDate . '</div>';
        // Create an hr divider style
        $sTextHr = '<div style="border-bottom:1px dotted #ccc;font-size:1px;margin-bottom:10px;margin-top:10px;">&nbsp;</div>';
        // Add a divider
        $sEmailDataBody .= $sTextHr;
        // Set the default number of exported orders to zero
        $iNumberOfExportedOrders = 0;
        // Get the Order records that have not been auto-exported
        $oOrders = Order::get()->filter(array(
            'AutoExported' => 0,
            'Status' => 'Paid'
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
                $sItemPrices = $oOrder->ItemPrices();
                $sItemsQty = $oOrder->ItemsQty();
                $sDiscount = $oOrder->getDiscount();
                $sNotes = str_replace('"', '', $oOrder->Notes); // Remove double quotes
                $sShippingTotal = $oOrder->ShippingTotal();
                $sTotal = $oOrder->Total;
                $sStatus = $oOrder->Status;
                // Add the Order details to the email content
                $sEmailDataBody .= '<div style="font-size:14px;"><strong>Name:</strong> ' . $sName . '</div>';
                $sEmailDataBody .= '<div style="font-size:14px;"><strong>Order No.</strong> ' . $sReference . '</div>';
                // Add a divider
                $sEmailDataBody .= $sTextHr;
                // Make the CSV row
                $export_row = "\"" . trim($sReference) . "\",\"" . trim($oDate) . "\",\"" . trim($sName) . "\",\"" . trim($sAddress1) . "\",\"" . trim($sAddress2) . "\",\"" . trim($sAddress3) . "\",\"" . trim($sAddress4) . "\",\"" . trim($sAddress5) . "\",\"" . trim($sAddress6) . "\",\"" . trim($sEmail) . "\",\"" . trim($sPhone) . "\",\"" . trim($sStockCodes) . "\",\"" . trim($sItemPrices) . "\",\"" . trim($sItemsQty) . "\",\"" . trim($sDiscount) . "\",\"" . trim($sNotes) . "\",\"" . trim($sShippingTotal) . "\",\"" . trim($sTotal) . "\",\"" . trim($sStatus) . "\"\r\n";
                // Write the text file data row
                fwrite($rOrdersFile, $export_row);
                // Increment the number of Orders
                $iNumberOfExportedOrders++;
                // Set the AutoExported status to 1
                if ($oOrder->Status == 'Paid') {
                    $oOrder->AutoExported = 1;
                    $oOrder->write();
                }
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
        if ($iNumberOfExportedOrders) {
            // Send email messages
            $oEmail = new Email($oSiteConfig->ExportEmailFrom, $oSiteConfig->ExportEmailTo, 'NutriBullet ' . $oSiteConfig->ExportCountry . ' website: Order Export Status', $sEmailData);
            // Set the file date
            $sFileDate = date('Ymdhis', time());
            // Attach the export CSVs
            $oEmail->attachFile($sOrdersFilePath, 'NutriBulletOrders' . $oSiteConfig->ExportCountry . '_' . $sFileDate . '.csv');
            // CC the email
            if ($oSiteConfig->ExportEmailCC) {
                $oEmail->bcc = $oSiteConfig->ExportEmailCC;
            }
            // Send the email
            $oEmail->send();
        }

        // Return true
        return true;

    }

    public function checkFolders() {
        $arrFolder = array(
            BASE_PATH . '/exported_orders/'
        );

        foreach ($arrFolder as $folder) {
            if (!file_exists($folder)) {
                Filesystem::makeFolder($folder);
            }
        }
    }

} 