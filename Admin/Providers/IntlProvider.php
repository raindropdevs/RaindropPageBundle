<?php

namespace Raindrop\PageBundle\Admin\Providers;

class IntlProvider {

    protected $countryProvider, $cultureProvider;

    public function __construct($cultureProvider, $countryProvider) {
        $this->cultureProvider = $cultureProvider;
        $this->countryProvider = $countryProvider;
    }

    public function getCulture() {
        $this->cultureProvider->provide();
    }

    public function getCountry() {
        $this->countryProvider->provide();
    }

    public function provide() {
        return array(
            'culture' => $this->getCulture(),
            'country' => $this->getCountry(),
        );
    }
}
