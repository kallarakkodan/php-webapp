<?php

namespace WebApp\BootstrapTheme;

class SearchFilterBarRenderer extends \WebApp\DefaultTheme\FormRenderer {

	public function __construct($theme, $component) {
		parent::__construct($theme, $component);
		$this->theme->addFeature(BootstrapTheme::SEARCH_FILTER);
	}

}

