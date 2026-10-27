<?php

namespace unirateapi\craftunirate\services;

use craft\base\Component;
use craft\helpers\App;
use unirateapi\craftunirate\client\UniRateClient;
use unirateapi\craftunirate\Plugin;

/**
 * Thin Craft service + Twig variable (`craft.unirate`) over the dependency-free
 * UniRate client. Only free-tier endpoints are exposed — rate, convert,
 * currencies, and VAT rates — so a template call never trips a Pro 403.
 */
class Rates extends Component
{
    private ?UniRateClient $client = null;

    /**
     * Inject a pre-built client (used in tests). Overrides the settings-derived one.
     */
    public function setClient(UniRateClient $client): void
    {
        $this->client = $client;
    }

    private function client(): UniRateClient
    {
        if ($this->client === null) {
            $settings = Plugin::getInstance()->getSettings();
            $apiKey = (string) App::parseEnv($settings->apiKey);
            $baseUrl = (string) App::parseEnv($settings->baseUrl);
            $this->client = new UniRateClient($apiKey, $baseUrl, $settings->timeout);
        }

        return $this->client;
    }

    /**
     * Exchange rate from one currency to another, e.g. `craft.unirate.rate('USD', 'EUR')`.
     */
    public function rate(string $from, string $to): float
    {
        return $this->client()->getRate($from, $to);
    }

    /**
     * Convert an amount between currencies, e.g. `craft.unirate.convert(100, 'USD', 'EUR')`.
     */
    public function convert(float $amount, string $from, string $to): float
    {
        return $this->client()->convert($amount, $from, $to);
    }

    /**
     * List every supported currency code, e.g. `craft.unirate.currencies()`.
     *
     * @return string[]
     */
    public function currencies(): array
    {
        return $this->client()->getCurrencies();
    }

    /**
     * VAT rates, optionally for a single country code, e.g. `craft.unirate.vatRates('DE')`.
     *
     * @return array<string,mixed>
     */
    public function vatRates(?string $country = null): array
    {
        return $this->client()->getVatRates($country);
    }
}
