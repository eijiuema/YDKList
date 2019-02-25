<?php

class Card {
	public $serial;
	public $name;
	public $wiki;
	public $yugioh_prices;
	public $print_tag;
	public $pack;
	public $prices;
	public $image;

	function __construct($serial)
	{
		$this->serial = sprintf('%08d', $serial);
		$this->pack = new \stdClass;
	}
}