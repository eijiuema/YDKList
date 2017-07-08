<?php
define("ROOT", 'Application');

if(isset($_POST['ydk'])) {

	$start = microtime(true);

	require_once(ROOT . '/Controller/YDKList.php');

	$ydklist = new YDKList();
	$ydklist->readYDK($_POST['ydk']);

	if($ydklist->rolling_curl($ydklist->wikia_urls(), 'name')) {
		if($ydklist->rolling_curl($ydklist->yugiohprices_urls(), 'price')) {
			if($ydklist->rolling_curl($ydklist->wikia_urlss(), 'wiki')) {
				$cards = $ydklist->cards;
				$end = microtime(true);
				$runtime = $end - $start;

				$total = new \stdClass;
				$total->highest = 0;
				$total->average = 0;
				$total->lowest = 0;
				foreach($cards as $card) {
					if(isset($card->prices)) {
						$total->highest += $card->prices->high;
						$total->average += $card->prices->average;
						$total->lowest += $card->prices->low;
					}
				}
				require_once(ROOT . '/View/table.php');
			}
		}
	}
} else {
	require_once(ROOT . '/View/template.php');
}