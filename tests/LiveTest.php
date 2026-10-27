<?php

namespace unirateapi\craftunirate\tests;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use unirateapi\craftunirate\client\UniRateClient;

/**
 * Live free-tier smoke tests against api.unirateapi.com.
 *
 * Skipped unless UNIRATE_API_KEY is present in the environment, so the default
 * (offline) test run never touches the network. Run via `dev --live php ...`.
 */
#[Group('live')]
final class LiveTest extends TestCase
{
    private UniRateClient $client;

    protected function setUp(): void
    {
        $key = getenv('UNIRATE_API_KEY') ?: '';
        if ($key === '') {
            $this->markTestSkipped('UNIRATE_API_KEY not set — skipping live tests.');
        }
        $this->client = new UniRateClient($key);
    }

    public function testLiveRate(): void
    {
        $rate = $this->client->getRate('USD', 'EUR');
        self::assertGreaterThan(0, $rate);
    }

    public function testLiveConvert(): void
    {
        $result = $this->client->convert(100, 'USD', 'EUR');
        self::assertGreaterThan(0, $result);
    }

    public function testLiveCurrencies(): void
    {
        $currencies = $this->client->getCurrencies();
        self::assertNotEmpty($currencies);
        self::assertContains('USD', $currencies);
    }
}
