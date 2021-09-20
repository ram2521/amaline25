<?php

require_once "./phpQuery-onefile.php";

$html = file_get_contents(
    "https://www.goodsmile.info/ja/product/5579/矢澤にこ.html"
);

//HTMLを全文取得
$dom = phpQuery::newDocument($html);

//imgタグの一覧を取得
foreach ($dom->find("img") as $img) {
    $img = $img->getAttribute("src");
    echo "<img src=" . $img . "><br>";
}
echo phpQuery::newDocument($html)
  ->find("img")
  ->text("src");
