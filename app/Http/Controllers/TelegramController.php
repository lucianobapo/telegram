<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use pimax\FbBotApp;
use pimax\Messages\Message;
use pimax\Messages\MessageButton;
use pimax\Messages\StructuredMessage;
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


            if (isset($fields['message']['contact'])) $this->checkContact($fields);

            if (isset($fields['message']['text'])){
                if ($fields['message']['text']=='Voltar ao Início' || $fields['message']['text']=='/start')
                    $this->sendGreetings($fields);

                elseif ($fields['message']['text']=='Solicitar Cardápio' || $fields['message']['text']=='/cardapio')
                    $this->sendMenu($fields);

                else $this->commandFail($fields);
            }


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

    private function checkContact($fields)
    {
//        $keyboard = [
//            ['Solicitar Cardápio'],
//            ['Fazer um Pedido'],
//            ['Voltar ao Início'],
//        ];
//
//        $reply_markup = $this->telegram->replyKeyboardMarkup([
//            'keyboard' => $keyboard,
//            'resize_keyboard' => true,
//            'one_time_keyboard' => true
//        ]);

        $response = $this->telegram->sendMessage([
            'chat_id' => $fields['message']['chat']['id'],
            'text' => 'Encontramos: '.$fields['message']['contact']['first_name'].' - ID:'.$fields['message']['contact']['user_id'],
//            'reply_markup' => $reply_markup
        ]);
        $messageId = $response->getMessageId();
        logger($messageId);
    }

    private function commandFail($fields)
    {
        $sendContact =  (object) [
            'text' => 'Enviar Meu Contato',
            'request_contact' => true,
            'request_location' => false
        ];

        $keyboard = [
            [$sendContact],
            ['Solicitar Cardápio'],
            ['Voltar ao Início'],
        ];

        $reply_markup = $this->telegram->replyKeyboardMarkup([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

        $response = $this->telegram->sendMessage([
            'chat_id' => $fields['message']['chat']['id'],
            'text' => 'Ops! Infelizmente não entendemos seu pedido, por favor, escolha uma das opções abaixo:',
            'reply_markup' => $reply_markup
        ]);
        $messageId = $response->getMessageId();
        logger($messageId);
    }

    public function messengerWebHook(Request $request){
        logger($request);
        if (isset($request['hub_verify_token']) && $request['hub_verify_token']==env('MESSENGER_BOT_VERIFY_TOKEN')){
            return $request['hub_challenge'];
        }
        if (isset($request['object']) && $request['object']=='page'){
            foreach ($request['entry'][0]['messaging'] as $message){
                // Skipping delivery messages
                if (!empty($message['delivery'])) {
                    continue;
                }
                // skip the echo of my own messages
                if (isset($message['message']['is_echo']) && ($message['message']['is_echo'] == "true")) {
                    continue;
                }
                $command = "";
                // When bot receive message from user
                if (!empty($message['message'])) {
                    if(isset($message['message']['text'])) $command = $message['message']['text'];
                    if(isset($message['message']['attachments'])) {
                        foreach ($message['message']['attachments'] as $attachment){
                            if($attachment['type']=='location'){
                                $command = 'location';
                                $coordinates = [
                                    'lat' => $attachment['payload']['coordinates']['lat'],
                                    'long' => $attachment['payload']['coordinates']['long'],
                                ];
                            }
                        }

                    }
                // When bot receive button click from user
                } else if (!empty($message['postback'])) {
                    $command = $message['postback']['payload'];
                }

                // Handle command
                switch ($command) {
                    // When bot receive "text"
                    case 'location':
                        $this->sendFacebookMessage($message['sender']['id'], implode(',',$coordinates));
                        break;
                    case 'Mostrar Cardápio':
                        $this->sendFacebookMessage($message['sender']['id'], 'Cardápio:');
                        break;
                    case 'Fazer Pedido':
                        $this->sendFacebookMessage($message['sender']['id'], 'Pedido:');
                        break;

                    // Other message received
                    default:
                        if (!empty($command)) // otherwise "empty message" wont be understood either
                            $this->sendFacebookMessage($message['sender']['id'], 'Desculpe, não entendi sua mensagem.');
                }
            }
        }
    }

    private function sendFacebookMessage($id, $string)
    {
//        $options = [
//            'json' => [
//                'access_token' => env('MESSENGER_BOT_PAGE_ACCESS_TOKEN'),
//                'recipient' => [
//                    'id' => $id,
//                ],
//                'message' => [
////                    'text' => $string,
//                    'attachment' => [
//                        'type'=>'template',
//                        'payload'=>[
//                            'template_type'=>'button',
////                            'text' => 'Escolha uma opção:',
//                            'text' => $string,
//                            'buttons'=>[
//                                [
//                                    'type' => 'postback',
//                                    'title' => 'Mostrar Cardápio',
//                                    'payload' => 'Mostrar Cardápio',
//                                ],
//                                [
//                                    'type' => 'postback',
//                                    'title' => 'Fazer Pedido',
//                                    'payload' => 'Fazer Pedido',
//                                ],
//                            ],
//                        ],
//                    ],
//                ],
//            ]
//        ];
//        $client = new Client(['base_uri' => env('MESSENGER_BOT_PAGE_URL')]);
//        $res = $client->request('POST', 'me/messages', $options);
//        logger($res->getStatusCode()) ;
//        logger($res->getBody()) ;

        // Make Bot Instance
        $bot = new FbBotApp(env('MESSENGER_BOT_PAGE_ACCESS_TOKEN'));
        $bot->send(new Message($id, $string));

        $message = new StructuredMessage($id,
            StructuredMessage::TYPE_BUTTON,
            [
                'text' => 'Choose category',
                'buttons' => [
                    new MessageButton(MessageButton::TYPE_POSTBACK, 'Fazer Pedido'),
                    new MessageButton(MessageButton::TYPE_POSTBACK, 'Mostrar Cardápio'),
//                    new MessageButton(MessageButton::TYPE_POSTBACK, 'Third button')
                ]
            ]
        );
//        logger($message->getData());
        $bot->send($message);
    }
}
