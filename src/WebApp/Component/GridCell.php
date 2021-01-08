<?php

namespace WebApp\Component;

class GridCell extends Div {

	protected $gridSizes;

	public function __construct($parent, $content = NULL) {
		parent::__construct($parent, $content);
		$this->gridSizes = array();
	}

	public function getGridSizes() {
		return $this->gridSizes;
	}

	public function addSize($class, $span = 0) {
		$this->gridSizes[$class] = $span;
		return $this;
	}
}
