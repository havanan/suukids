<?php

namespace App\Helpers;


use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use App\Models\FcmToken;

class FirebaseHelper {
    public static function sendMessageTo($tokens, $message) {
        try {
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60*20);

            $notificationBuilder = new PayloadNotificationBuilder('Thông báo');
            $notificationBuilder->setBody($message)
                                ->setSound('default');

            $dataBuilder = new PayloadDataBuilder();

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();

            $downstreamResponse = FCM::sendTo($tokens, $option, $notification);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();

            // return Array - you must remove all this tokens in your database
            $deletesTokens = $downstreamResponse->tokensToDelete();
            FcmToken::query()->whereIn('token', $deletesTokens)->delete();

            // return Array (key:token, value:error) - in production you should remove from your database the tokens present in this array
            // $downstreamResponse->tokensWithError();
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
        }
    }

}