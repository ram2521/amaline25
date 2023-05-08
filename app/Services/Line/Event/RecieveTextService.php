<?php

namespace App\Services\Line\Event;

use LINE\LINEBot;
use DB;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use Illuminate\Support\Facades\Log;

use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use Unirest\Request as unirest;

class RecieveTextService
{
    /**
     * @var LineBot
     */
    private $bot;

    /**
     * Follow constructor.
     * @param LineBot $bot
     */
    public function __construct(LineBot $bot)
    {
        $this->bot = $bot;
    }

    /**
     * 登録
     * @param TextMessage $event
     * @return CarouselTemplateBuilder
     */
    public function execute(TextMessage $event)
    {
        Log::info("■■■■■Amazon 商品情報取得開始■■■■■");
        Log::info($event->getText());
        // Amazon APIから商品情報を取得する。
        $headers = [
      "Accept" => "application/json",
      // 'Cache-Control' => 'no-cache',
      "x-rapidapi-host" => "amazon-price1.p.rapidapi.com",
      "x-rapidapi-key" => "8c530fc461mshf56028a639290a1p131053jsn1399caac06e4",
    ];
        $query = [
      "marketplace" => "JP",
      "keywords" => urlencode($event->getText()),
    ];
        // $query = array('marketplace' => 'JP', 'keywords' => $event->getText());
        $response = unirest::get(
            "https://amazon-price1.p.rapidapi.com/search",
            $headers,
            $query
        );
        $amazonList = $response->body;
        Log::info($amazonList);
        Log::info("■■■■■Amazon 商品情報取得終了■■■■■");

        // カルーセルメッセージ作成
        $columns = [];

        for ($i = 0; $i <= 9; $i++) {
            $amazon = $amazonList[$i];
            Log::info("■■■■■Amazon" . $amazon->title . "■■■■■");

            // カルーセルに付与するボタンを作る
            $action = new UriTemplateActionBuilder(
                "クリック",
                $amazon->detailPageURL
            );
            // カルーセルのカラムを作成する
            $title =
        strlen($amazon->title) >= 15
          ? mb_substr($amazon->title, 0, 14)
          : $amazon->title;
            $column = new CarouselColumnTemplateBuilder(
                $title,
                "価格：" . $amazon->price,
                $amazon->imageUrl,
                [$action]
            );
            $columns[] = $column;
        }
        $carousel = new CarouselTemplateBuilder($columns);
        $carousel_message = new TemplateMessageBuilder("テストです。", $carousel);
        return $carousel_message;
    }
}
