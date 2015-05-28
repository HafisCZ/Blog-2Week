<?php

  $xml = ("http://www.novinky.cz/rss2/vase-zpravy/");

  $xmlDoc = new DOMDocument();
  $xmlDoc->load($xml);

  $channel = $xmlDoc->getElementsByTagName('channel')->item(0);
  $channel_title = $channel->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
  $channel_link = $channel->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
  $channel_desc = $channel->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;

  echo("<br/><h2><div style='border-bottom:1px solid black'><a href='" . $channel_link . "'>" . $channel_title . "</a><br/>" . $channel_desc ."</div></h2>");

  $x = $xmlDoc->getElementsByTagName('item');
  for ($i = 0; $i <= 4; $i++) {
    $item_title=$x->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
    $item_link=$x->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
    $item_desc=$x->item($i)->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;
  
    $i2 = $i + 1;
    echo("<br/><div><b>" . $i2  . ". <a href='" . $item_link . "'>" . $item_title . "</a></b><br/>" . $item_desc . "</div>");
  }

?> 