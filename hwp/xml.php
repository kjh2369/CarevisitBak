<?
	//XML
	$dom = new DOMDocument("1.0");
	header("Content-Type: text/plain");
	$searchPage = mb_convert_encoding($htmlUTF8Page, 'HTML-ENTITIES', "UTF-8");
	@$dom->loadHTML($searchPage);

	$root = $dom->createElement("today_works");
	$dom->appendChild($root);

	$item = $dom->createElement("today_list");

	$item->setAttribute("MCODE",          "TEST1"); #0
	$item->setAttribute("MKIND",          "TEST2"); #1

	$root->appendChild($item );
	$text = $dom->createTextNode("TEST");
	$item->appendChild($text);

	echo $dom->saveXML();
?>