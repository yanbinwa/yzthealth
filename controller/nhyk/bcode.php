<?php 

$borcode = new Bcode();
//条形码内容
$text = request::get('text');
$borcode->getBarcode($text);