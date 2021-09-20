<?php
//参加時の処理
if ($EventType == "join") {
    $response_format_text = [
    "type" => "text",
    "text" =>
      "こんにちは。二次元画像検索β版です。\nkonachan.comを使用しています。\n使い方はhelpと打ってください。",
  ];
    hatsugen();
    exit();
}
//画像をランダムで1枚投稿する。
if ($text == "randomimage") {
    $xml =
    "https://konachan.com/post.xml?tags=order%3Arandom+rating:safe&limit=1";
    $xmlData = simplexml_load_file($xml);
    $imgurl = $xmlData->post[0]->attributes()->sample_url;
    $tumburl = $xmlData->post[0]->attributes()->preview_url;
    $response_format_text = [
    "type" => "image",
    "originalContentUrl" => "$imgurl",
    "previewImageUrl" => "$tumburl",
  ];
    hatsugen();
//指定した単語のR18画像をランダムで1枚投稿する。
} elseif (strpos($text, "r18search:") !== false) {
    $searchtext = str_replace("r18search:", "", $text);
    $xml = "https://konachan.com/post.xml?tags=$searchtext+order%3Arandom+rating:questionableplus&limit=1";
    $xmlData = simplexml_load_file($xml);
    //検索結果がない場合の処理。なにもないよ！
    if ($xmlData == "") {
        $response_format_text = [
      "type" => "text",
      "text" => "Nobody here but us chickens!",
    ];
        hatsugen();
        exit();
    }
    $imgurl = $xmlData->post[0]->attributes()->sample_url;
    $tumburl = $xmlData->post[0]->attributes()->preview_url;
    $response_format_text = [
    "type" => "image",
    "originalContentUrl" => "$imgurl",
    "previewImageUrl" => "$tumburl",
  ];
    hatsugen();
//指定した単語の全年齢画像をランダムで1枚投稿する。
} elseif (strpos($text, "search:") !== false) {
    $searchtext = str_replace("search:", "", $text);
    $xml = "https://konachan.com/post.xml?tags=$searchtext+order%3Arandom+rating:safe&limit=1";
    $xmlData = simplexml_load_file($xml);
    //検索結果がない場合の処理。なにもないよ！
    if ($xmlData == "") {
        $response_format_text = [
      "type" => "text",
      "text" => "Nobody here but us chickens!",
    ];
        hatsugen();
        exit();
    }
    $imgurl = $xmlData->post[0]->attributes()->sample_url;
    $tumburl = $xmlData->post[0]->attributes()->preview_url;
    $response_format_text = [
    "type" => "image",
    "originalContentUrl" => "$imgurl",
    "previewImageUrl" => "$tumburl",
  ];
    hatsugen();
//helpと言われたときに使い方を発言する。
} elseif ($text == "help") {
    $response_format_text = [
    "type" => "text",
    "text" =>
      "randomimageって打つと適当に画像ひっぱってくるよ。\nsearch:検索結果文字列（英語）で検索したやつをランダムで一個だすよ。\n例:)search:fate/Fstay_night\nスペースは\"_\"（アンダーバー）にしてね。\n続きはhelp2と打ってね。",
  ];
    hatsugen();
} elseif ($text == "help2") {
    $response_format_text = [
    "type" => "text",
    "text" =>
      "ワイルドカードの*を使うとその単語を含んだものを検索します。\n例:)search:*haruhi*でharuhiを含んだ検索をするよ。\nr18search:で18禁画像を検索するよ。",
  ];
    hatsugen();
}
?>	 ?>