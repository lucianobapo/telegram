<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api;

class TelegramController extends Controller
{
    protected $telegram;

    public function __construct() {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
//        dd($this->telegram->getUpdates());
    }

    public function getUpdates()
    {
        $updates = $this->telegram->getUpdates();

        $last_updates[] = array_last($updates);

        return response()->json($last_updates);
    }

    public function getSendMessage()
    {
        return view('send-message');
    }

    public function getWebHook(){
        $response = $this->telegram->getWebhookUpdates();
        dd($response);
    }

    public function setWebHook(){
        $response = $this->telegram->setWebhook([
            'url' => 'https://telegram.ilhanet.com/api/callback-web-hook/'.env('TELEGRAM_BOT_TOKEN')
        ]);
        dd($response);
    }

    public function postWebHook(Request $request, $token){
        $fields = $request->all();
//        logger($token);
        logger($fields);
        if ($token==env('TELEGRAM_BOT_TOKEN')){
            if ($fields['message']['text']=='Voltar ao Início' || $fields['message']['text']=='/start')
                $this->sendGreetings($fields);

            if ($fields['message']['text']=='Solicitar Cardápio' || $fields['message']['text']=='/cardapio')
                $this->sendMenu($fields);

        }

    }

    public function postSendMessage(Request $request)
    {
        $rules = [
            'message' => 'required'
        ];

        $this->validate($request, $rules);

        $sendContact =  (object) [
            'text' => 'Enviar Meu Contato',
            'request_contact' => true,
            'request_location' => false
        ];

        $sendLocation=  (object) [
            'text' => 'Enviar Minha Localização',
            'request_contact' => false,
            'request_location' => true
        ];

        $keyboard = [
            [$sendContact],
            [$sendLocation],
            ['1', '2', '3'],
            ['0']
        ];

//        dd($keyboard);

        $reply_markup = $this->telegram->replyKeyboardMarkup([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

        $response = $this->telegram->sendMessage([
            'chat_id' => env('TELEGRAM_BOT_CHAT_ID'),
            'text' => $request->only('message')['message'],
            'reply_markup' => $reply_markup
        ]);

        $messageId = $response->getMessageId();
        return redirect('/send-message')
            ->with('status', 'success')
            ->with('message', $messageId);
    }

    private function sendGreetings($fields)
    {
        $sendContact =  (object) [
            'text' => 'Enviar Meu Contato',
            'request_contact' => true,
            'request_location' => false
        ];

        $keyboard = [
            [$sendContact],
            ['Solicitar Cardápio']
        ];

        $reply_markup = $this->telegram->replyKeyboardMarkup([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

        $response = $this->telegram->sendMessage([
            'chat_id' => $fields['message']['chat']['id'],
            'text' => 'Olá, seja bem-vindo! Para começarmos gostaríamos te identificar em nossos registros. Por favor, nos envie seu contato.',
            'reply_markup' => $reply_markup
        ]);
        $messageId = $response->getMessageId();
        logger($messageId);
    }

    private function sendMenu($fields)
    {
        $sendContact =  (object) [
            'text' => 'Enviar Meu Contato',
            'request_contact' => true,
            'request_location' => false
        ];

        $keyboard = [
            [$sendContact],
            ['Voltar ao Início'],
        ];

        $reply_markup = $this->telegram->replyKeyboardMarkup([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

        $response = $this->telegram->sendMessage([
            'chat_id' => $fields['message']['chat']['id'],
            'text' => 'Menu solicitado.',
            'reply_markup' => $reply_markup
        ]);
        $messageId = $response->getMessageId();
        logger($messageId);
    }
}
