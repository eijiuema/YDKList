<?php

class YDKList {
	public $cards = [];

	function __construct() {
		require_once(ROOT . '/Model/Card.php');
	}

	function readYDK($ydk) {
		$lines = explode("\n", $ydk);
		foreach ($lines as $line) {
			if(ctype_digit(trim($line))) {
				if(!empty($line)) {
					$this->cards[] = new Card(trim(sprintf('%08d', $line)));
				}
			}
		}
	}

	function wikia_urls() {

		$urls = [];

		foreach($this->cards as $key => $card) {
			$urls[$key] = "http://yugioh.wikia.com/api/v1/Search/List/?query=". urlencode($card->serial) . "&limit=1";
		}

		return $urls;
	}

	function wikia_urlss() {
		$urls = [];

		foreach($this->cards as $key => $card) {
			if(isset($card->pack->name)) {
				$urls[$key] = "http://yugioh.wikia.com/api/v1/Search/List/?query=" . urlencode($card->pack->name) . "&limit=1";
			}
		}

		return $urls;
	}

	function yugiohprices_urls() {
		$urls = [];

		foreach($this->cards as $key => $card) {
			$urls[$key] = "http://yugiohprices.com/api/get_card_prices/" . urlencode($card->name);
		}

		return $urls;
	}

	function set_name($result, $key) {
		$name = json_decode($result)->items[0]->title;
		$wiki = json_decode($result)->items[0]->url;

		if(isset($this->cards[$key])) {
			$this->cards[$key]->name = $name;
			$this->cards[$key]->wiki = $wiki;
			$this->cards[$key]->image = "http://yugiohprices.com/api/card_image/" . urlencode($name);
			$this->cards[$key]->yugioh_prices = "http://yugiohprices.com/card_price?name=" . urlencode($name);
		}
	}

	function set_price($result, $key) {
		if(json_decode($result)->status == 'success') {
			$data = json_decode($result)->data;
		} else {
			return;
		}
	
		foreach($data as $dataa)
		{
			if(isset($dataa->price_data->data->prices->average))
			{
				$prints[] = $dataa;
			}
		}

		if(!empty($prints)) {
			usort($prints, function($a, $b) {
				if($a->price_data->data->prices->average == $b->price_data->data->prices->average) {
					return 0;
				}
	
				return $a->price_data->data->prices->average < $b->price_data->data->prices->average ? -1 : 1;
			});

			$this->cards[$key]->prices = $data[0]->price_data->data->prices;
			$this->cards[$key]->print_tag = $data[0]->print_tag;
			$this->cards[$key]->pack->name = $data[0]->name;
		}
	}

	function set_wiki($result, $key) {
		$data = json_decode($result)->items[0];
		$this->cards[$key]->pack->wiki = $data->url;
	}

	function rolling_curl($urls, $callback, $custom_options = null) {
	
		// make sure the rolling window isn't greater than the # of urls
		$rolling_window = count($urls);
	
		$master = curl_multi_init();
		$curl_arr = array();
		// add additional curl options here
		$std_options = [
			CURLOPT_RETURNTRANSFER => true,
		];
		$options = ($custom_options) ? ($std_options + $custom_options) : $std_options;
	
		// start the first batch of requests
		for ($i = 0; $i < $rolling_window; $i++) {
			$ch = curl_init();
			if(isset($urls[$i])) {
				$options[CURLOPT_URL] = $urls[$i];
				curl_setopt_array($ch, $options);
				curl_setopt($ch, \CURLOPT_PRIVATE, $i);
				curl_multi_add_handle($master, $ch);
			}
		}
	
		do {
			while(($execrun = curl_multi_exec($master, $running)) == CURLM_CALL_MULTI_PERFORM);
			if($execrun != CURLM_OK)
				break;
			// a request was just completed -- find out which one
			while($done = curl_multi_info_read($master)) {
				$info = curl_getinfo($done['handle']);
				if ($info['http_code'] == 200)  {
					$output = curl_multi_getcontent($done['handle']);
	
					// request successful.  process output using the callback function.
					switch($callback) {
						case 'name':
							$this->set_name($output, curl_getinfo($done['handle'], \CURLINFO_PRIVATE));
						break;
						case 'price':
							$this->set_price($output, curl_getinfo($done['handle'], \CURLINFO_PRIVATE));
						break;
						case 'wiki':
							$this->set_wiki($output, curl_getinfo($done['handle'], \CURLINFO_PRIVATE));
						break;
					}

					// start a new request (it's important to do this before removing the old one)
					$ch = curl_init();
					if(isset($urls[$i + 1])) {
						$options[CURLOPT_URL] = $urls[$i++];  // increment i
						curl_setopt_array($ch,$options);
						curl_setopt($ch, \CURLOPT_PRIVATE, $i);
						curl_multi_add_handle($master, $ch);
					}
					// remove the curl handle that just completed
					curl_multi_remove_handle($master, $done['handle']);
				} else {

				}
			}
		} while ($running);
		
		curl_multi_close($master);

		return true;
	}
}