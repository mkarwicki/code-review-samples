<?php

chdir(__DIR__ . '/../');
include ('./cli-functions.php');

$app = \Slim\Slim::getInstance();


if(ENVIRONMENT != 'development') {
    $sentry = $app->sentry;

    $countTotal = \Orm\Model\Orders\Orders::where('gateway_id', '=', 'BrainTree')->where('subscription_id', '<>', '')->count();
    $count = 0;
    $sentry->captureMessage("Braintree update: subscriptions dynamic descriptor name to be updated:". $countTotal,[],['level' => 'info','extra'=> ['total'=>$countTotal]]);

    echo "Braintree subscriptions dynamic descriptor name to be updated: $countTotal\n\r";

    \Orm\Model\Orders\Orders::where('gateway_id', '=', 'BrainTree')->where('subscription_id', '<>', '')->chunk(50, function($orders) use($countTotal,$sentry) {
        global $count;
        $brainTree = new \Lib\Gateway\BrainTree();
        foreach($orders as $order):
            if(strlen($order)>0){
                $brainTree->updateSubscriptionDynamicDescriptorName($order->subscription_id, $order->id);
                $count++;
            }
        endforeach;
        echo "Processed $count of $countTotal\n\r";

        $sentry->captureMessage("Braintree update: Processed $count of $countTotal",[],['level' => 'info']);
    });
    $status = $count.' Subscriptions Dynamic Descriptors updated.';
    $sentry->captureMessage("Braintree update: ".$status,[],['level' => 'info']);
    echo $status;
    echo "\r\n";
}
