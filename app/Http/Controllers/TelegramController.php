<?php

namespace App\Http\Controllers;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function send($message) {
        $userSetting = Setting::where('user_id', Auth::user()->id)->first();
        if ($userSetting->is_tbot_active) {
            $idChannel = $userSetting->tbot_channel_id;

            $response = Telegram::sendMessage([
                'chat_id' => $idChannel,
                'text' => $message,
            ]);

            $messageId = $response->getMessageId();
            Log::info($messageId);
        }
    }

    public function getFromBot() {
        $updates = Telegram::getWebhookUpdate();
        Log::info($updates);

        Log::info('Message: ' . $updates->message);

        $preparedMessage = $this->prepareMessage($updates);
        Setting::where('tbot_token', $preparedMessage['chatId'])->firstOr(function () {
            return 'Restricted';
        });
        $this->mainLogic($preparedMessage);

        return 'ok';
    }

    private function prepareMessage($sourceMessage)
    {
        if (isset($sourceMessage['message'])) {
            $preparedMessage = [
                'type' => 'textFromUser',
                'chatId' => $sourceMessage['message']['from']['id'],
                'text' => $sourceMessage['message']['text'],
            ];
        } elseif (isset($sourceMessage['channel_post'])) {
            $preparedMessage = [
                'type' => 'textFromChannel',
                'chatId' => $sourceMessage['channel_post']['sender_chat']['id'],
                'text' => $sourceMessage['channel_post']['text'],
            ];
        } elseif (isset($sourceMessage['callback_query'])) {
            $preparedMessage = [
                'type' => 'button',
                'chatId' => $sourceMessage['callback_query']['message']['chat']['id'],
                'text' => $sourceMessage['callback_query']['data'],
            ];
        } else {
            Log::info('Wrong message');
        }

        return $preparedMessage;
    }

    private function mainLogic($messageArray) {
        if ($messageArray['type'] === 'textFromUser' || $messageArray['type'] === 'textFromChannel') {
            switch ($messageArray['text']) {
                case '/help':
                    $message = "Нужна помощь?";
                    $reply_markup = json_encode(['inline_keyboard' => []]);
                    break;
                default:
                    $message = "Вот кнопки";
                    $reply_markup = json_encode(
                        [
                            'inline_keyboard' =>
                                [
                                    [
                                        [
                                            'text' => 'Button 1',
                                            'callback_data' => 'button1',
                                        ],
                                        [
                                            'text' => 'Button 2',
                                            'callback_data' => 'button2',
                                        ],
                                    ]
                                ],
                        ]
                    );

            }
        } elseif ($messageArray['type'] === 'button') {
            switch ($messageArray['text']) {
                case 'button1':
                    $message = "Нажата кнопка1";
                    $reply_markup = json_encode(['inline_keyboard' => []]);
                    break;
                case 'button2':
                    $message = "Нажата кнопка2";
                    $reply_markup = json_encode(['inline_keyboard' => []]);
                    break;
                default:
                    $message = "Хер пойми что нажато";
                    $reply_markup = json_encode(['inline_keyboard' => []]);
                    break;

            }
        }
        $response = Telegram::sendMessage([
            'chat_id' => $messageArray['chatId'],
            'text' => $message,
            'reply_markup' => $reply_markup,
        ]);
    }

    public function setWebHook() {
        $response = Telegram::setWebhook(['url' => env('TELEGRAM_WEBHOOK_URL')]);
        Log::info($response);
        return;
    }

    public function getMe() {
        $response = Telegram::bot('mybot')->getMe();
        Log::info($response);
        return;
    }

}
