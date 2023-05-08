<?php

namespace App\Http\Controllers\Api;

use App\Services\Line\Event\RecieveLocationService;
use App\Services\Line\Event\RecieveTextService;
use App\Services\Line\Event\FollowService;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use Illuminate\Http\Request;
use LINE\LINEBot;
use Unirest\Request as unirest;


class LineBotController
{

  public function amazon(Request $request)
  {
    $headers = array(
      'Accept' => 'application/json',
      'x-rapidapi-host' => 'amazon-price1.p.rapidapi.com',
      'x-rapidapi-key' => 'fbe44705f9msh83c879eadc47af3p1d0335jsn1f92ff681d04'
    );
    $query = array('marketplace' => 'ES', 'keywords' => 'book');
    $response = unirest::get('https://amazon-price1.p.rapidapi.com/search', $headers, $query);
    var_dump($response->body);
  }

  /**
   * callback from LINE Message API(webhook)
   * @param Request $request
   * @throws LINEBot\Exception\InvalidSignatureException
   */
  public function callback(Request $request)
  {
    /** @var LINEBot $bot */
    $bot = app("line-bot");

    $signature =  $_SERVER["HTTP_" . LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
    if (
      !LINEBot\SignatureValidator::validateSignature(
        $request->getContent(),
        env("LINE_CHANNEL_SECRET"),
        $signature
      )
    ) {
      logger()->info("recieved from difference line-server");
      abort(400);
    }

    $events = $bot->parseEventRequest($request->getContent(), $signature);

    /** @var LINEBot\Event\BaseEvent $event */
    foreach ($events as $event) {
      $reply_token = $event->getReplyToken();
      // $reply_message =
      //   "その操作はサポートしてません。.[" .
      //   get_class($event) .
      //   "][" .
      //   $event->getType() .
      //   "]";

      switch (true) {
          //友達登録＆ブロック解除
        case $event instanceof LINEBot\Event\FollowEvent:
          $service = new FollowService($bot);
          $reply_message = $service->execute($event)
            ? "友達登録されたからLINE ID引っこ抜いたわー"
            : "友達登録されたけど、登録処理に失敗したから、何もしないよ";

          break;
          //メッセージの受信
        case $event instanceof LINEBot\Event\MessageEvent\TextMessage:
          $service = new RecieveTextService($bot);
          $carousel_message = $service->execute($event);
          $reply_message = new MultiMessageBuilder();
          $reply_message->add($carousel_message);

          $bot->replyMessage($reply_token, $reply_message);
          break;

          //位置情報の受信
        case $event instanceof LINEBot\Event\MessageEvent\LocationMessage:
          $service = new RecieveLocationService($bot);
          $reply_message = $service->execute($event);
          break;

          //選択肢とか選んだ時に受信するイベント
        case $event instanceof LINEBot\Event\PostbackEvent:
          break;
          //ブロック
        case $event instanceof LINEBot\Event\UnfollowEvent:
          break;
        default:
          // $body = $event->getEventBody();
          logger()->warning(
            "Unknown event. [" . get_class($event) . "]",
            compact("body")
          );
      }
      // カルーセルテスト

      // $bot->replyText($reply_token, $reply_message);
    }
  }
}