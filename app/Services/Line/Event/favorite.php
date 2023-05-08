<?php
"contents"=> [
 [
   "type"=> "button",
   "style"=> "link",
   "height"=> "sm",
   "action"=> [
	 "type"=> "uri",
	 "label"=> "食べログをみる",
	 "uri"=> $taberogu->{'url'}
   ]
 ],
 [
   "type"=> "button",
   "style"=> "link",
   "height"=> "sm",
   "action"=> [
	 "type"=> "postback",
	 "label"=> "お気に入りに登録する",
	 "data"=> $taberogu->{'name'}.'|'.$taberogu->{'service'}.'|'.$taberogu->{'street'}.'|'.$taberogu->{'image_url'}.'|'.$taberogu->{'url'}.'|'.$distance.'|'.$taberogu->{'rating'},
	 "text"=> "お気に入りに登録する"
   ]
 ],