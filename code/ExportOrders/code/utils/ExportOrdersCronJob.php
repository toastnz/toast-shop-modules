<?php

class ExportOrdersCronJob extends ScheduledJob
{

    public function run($doCronJob, $params = "") {

        $region = null;
        if(isset($params['region'])) {
            $region = $params['region'];
        }

        $oExport = new ExportOrders();
        $oExport->process($region);
    }

} 