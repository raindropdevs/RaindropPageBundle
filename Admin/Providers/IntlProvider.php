<?php

namespace Raindrop\PageBundle\Admin\Providers;

class IntlProvider
{
    protected $i18nProvider;

    public function __construct($i18nProvider)
    {
        $this->i18nProvider = $i18nProvider;
    }

    public function getCultures($country)
    {
        return $this->i18nProvider->getCultures($country);
    }

    public function getCountries()
    {
        return $this->i18nProvider->getAllowedCountriesAndInternationalCountries();
    }
}
