<?php

namespace Apps\Cp\Controller\MobileAppStats;

use Apps\Cp\Controller;
use Orm\Model\Users\Devices as DevicesModel;
use Orm\Model\Users\DevicesMessages;

class MobileAppStats extends Controller {



    public function indexAction() {
        $this->render('MobileAppStats/Index', [
            'section_title' => "Mobile App Stats",
            'stats' => $this->mobileAppStatsData(),
            'breadcrumb' => [
                ["text" => "Reporting"],
                ["link" => $this->app->urlFor("cp.mobile_app_stats"), "text" => "Mobile App Stats"],
            ],
        ]);
    }


    public function mobileAppStatsData(){
        $totalUsers = DevicesModel::query()
           ->distinct()
           ->count('user_id') ;
        $totalIOS = DevicesModel::query()
           ->where('platform', '=', 'ios')
           ->distinct()
           ->count('user_id') ;
        $totalAndroid = DevicesModel::query()
           ->where('platform', '=', 'android')
           ->distinct()
           ->count('user_id') ;
        $totalPushMessages = DevicesMessages::query()
            ->count() ;
        $totalPushMessagesSent = DevicesMessages::query()
            ->where('status', '=', 2)
            ->count();
        return [
            'total_users'=>$totalUsers,
            'total_devices_ios'=>$totalIOS,
            'total_devices_android'=>$totalAndroid,
            'total_push_messages'=>$totalPushMessages,
            'total_push_messages_sent'=>$totalPushMessagesSent,
        ];
    }



}
