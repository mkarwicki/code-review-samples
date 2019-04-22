<?php

namespace Lib\MobileApp;

use Illuminate\Support\Collection;
use Lib\Dates;
use Lib\ResultPages\BodyText\TextRender;
use Orm\Model\Auctions\Participants;
use Orm\Model\Users\DevicesMessages;
use Orm\Model\Users\Users;

class Messages {
    /**
     * @param string$message
     * @param array $userIds
     * @param array $customVariables
     */
    public static function addMessageToQueue($type, array $userIds, $message, $title = '', $redirectUrl = '', array $additionalData = [], array $customVariables = []) {
        if(empty($userIds)) {
            return;
        }

        Users::whereIn('id', $userIds)
            ->has('devices')
            ->chunk(1000, function($users) use($message, $title, $customVariables, $type, $redirectUrl, $additionalData) {
                foreach($users as $user) {
                    foreach($user->devices as $device) {
                        $setting = $device->settings->where('slug', $type)->first();

                        if(!empty($setting) && $setting['enabled'] == false) {
                            continue;
                        }

                        $generatedMessage = self::renderMessageContent($user, $message, $title, $customVariables);
                        self::createMessage($type, $device->id, $generatedMessage['message'], $generatedMessage['title'], $redirectUrl, $additionalData);
                    }
                }
            });
    }

    /**
     * @param string $message
     * @param array $customVariables
     * @return string
     */
    public static function renderMessageContent($user, $message, $title = '', array $customVariables = []) {
        $variables = array_merge([
            'user' => $user
        ], $customVariables);

        return [
            'title' => TextRender::render($title, $variables),
            'message' => TextRender::render($message, $variables),
        ];
    }

    /**
     * @param array $message
     * @param int $deviceId
     * @param string $title
     */
    public static function createMessage($type, $deviceId, $message, $title = '', $redirectUrl = '', $additionalData = []) {
        DevicesMessages::create([
            'message_type' => $type,
            'title' => $title,
            'message' => $message,
            'device_id' => $deviceId,
            'redirect_url' => $redirectUrl,
            'additional_data' => $additionalData,
            'status' => DevicesMessages::STATUS_TO_SEND,
            'created_at' => Dates::nowDBTime()
        ]);
    }

    /**
     * @param int $limit
     */
    public static function sendMessages($limit = 100) {
        $toSend = DevicesMessages::where('status', '=', DevicesMessages::STATUS_TO_SEND)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        $toSend->load('device');

        if($toSend->count() == 0) {
            return;
        }

        foreach($toSend as $message) {
            $setting = $message->device->settings->where('slug', $message->message_type)->first();

            if(!empty($setting) && $setting['enabled'] == false) {
                $message->delete();
                continue;
            }

            $result = self::sendMessage($message);

            if($result['success'] == 1) {
                $message->status = DevicesMessages::STATUS_SENT;
                $message->date_sent = Dates::nowDBTime();
            }
            else {
                $message->status = DevicesMessages::STATUS_ERROR;
            }

            $message->updated_at = Dates::nowDBTime();
            $message->save();
        }
    }

    /**
     * @param string|array $message
     * @param int $deviceId
     * @return mixed
     */
    private static function sendMessage($messageModel) {
        if(!empty($messageModel->redirect_url)) {
            $redirectType = 'browser';
            $redirectPath = $messageModel->redirect_url;
        }
        else {
            $redirectType = 'in_app';
            $redirectPath = '';
        }

        $additionalData = $messageModel->additional_data;
        $additionalData['unread_auctions'] = Participants::where('listing_id', '=', $messageModel->device->user->listing->id)
            ->new()
            ->count();
        $additionalData['unread_messages'] = $messageModel->device->user->listing->emails()->NotDisplayed()->count();

        $badgeSum =  $additionalData['unread_auctions']  + $additionalData['unread_messages'] ;

        if($messageModel->device->platform == 'ios'){
            $msg = array
            (
                'title' => $messageModel->title,
                'body' => $messageModel->message,
                'sound' => 'default',
                'badge' => $badgeSum
            );
            $fields = array
            (
                'registration_ids' => [$messageModel->device->device_id],
                'content_available' => true,
                'priority'=> 'high',
                'notification' => $msg,

                'data' => [
                    'slug' => $messageModel->message_type,
                    'redirect_type' => $redirectType,
                    'redirect_path' => $redirectPath,
                    'additional_data' => $additionalData
                ],
                'payload'=> [
                    'aps' => [
                        'sound'=>'default',
                        'badge'=>$badgeSum
                    ],
                    'notId' => $messageModel->id
                ]
            );
        }else {
            $msg = array
            (
                'message' => $messageModel->title,
                'title' => $messageModel->message,
                'slug' => $messageModel->message_type,
                'redirect_type' => $redirectType,
                'redirect_path' => $redirectPath,
                'vibrate' => 1,
                'sound' => 1,
                'badge' => $badgeSum,
                'additional_data' => $additionalData,
            );
            $fields = [
                'registration_ids' => [$messageModel->device->device_id],
                'data' => $msg
            ];
        }

        $headers = [
            'Authorization: key=' . env('FIREBASE_API_KEY'),
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }
}
