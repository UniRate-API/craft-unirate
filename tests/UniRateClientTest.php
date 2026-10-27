<?php

namespace unirateapi\craftunirate\tests;

use PHPUnit\Framework\TestCase;
use unirateapi\craftunirate\client\UniRateClient;
use unirateapi\craftunirate\client\UniRateException;

final class UniRateClientTest extends TestCase
{
    /**
     * Build a client whose transport returns a canned [status, body], and capture the URL it was called with.
     *
     * @param array{0:int,1:string} $response
     */
    private function clientReturning(array $response, ?string &$captured = null): UniRateClient
    {
        $transport = function (string $url) use ($response, &$captured): array {
            $captured = $url;

            return $response;
        };

        return new UniRateClient('test-key', 'https://api.unirateapi.com', 15, $transport);
    }

    public function testGetRate(): void
    {
        $client = $this->clientReturning([200, '{"rate": 0.85}'], $url);
        self::assertSame(0.85, $client->getRate('usd', 'eur'));
        self::assertStringContainsString('/api/rates?', $url);
        self::assertStringContainsString('from=USD', $url);
        self::assertStringContainsString('to=EUR', $url);
        self::assertStringContainsString('api_key=test-key', $url);
    }

    public function testConvert(): void
    {
        $client = $this->clientReturning([200, '{"result": 85.0}'], $url);
        self::assertSame(85.0, $client->convert(100, 'USD', 'EUR'));
        self::assertStringContainsString('/api/convert?', $url);
        self::assertStringContainsString('amount=100', $url);
    }

    public function testGetCurrencies(): void
    {
        $client = $this->clientReturning([200, '{"currencies": ["USD","EUR","GBP"]}']);
        self::assertSame(['USD', 'EUR', 'GBP'], $client->getCurrencies());
    }

    public function testGetVatRatesForCountry(): void
    {
        $client = $this->clientReturning([200, '{"country":"DE","standard_rate":19}'], $url);
        self::assertSame(['country' => 'DE', 'standard_rate' => 19], $client->getVatRates('de'));
        self::assertStringContainsString('country=DE', $url);
    }

    public function testGetVatRatesAllOmitsCountryParam(): void
    {
        $client = $this->clientReturning([200, '{"rates":[]}'], $url);
        $client->getVatRates();
        self::assertStringNotContainsString('country=', $url);
    }

    public function testBaseUrlTrailingSlashTrimmed(): void
    {
        $client = new UniRateClient('k', 'https://api.unirateapi.com/', 15, function (string $url) use (&$captured): array {
            $captured = $url;
            return [200, '{"rate":1.0}'];
        });
        $client->getRate('USD', 'USD');
        self::assertStringContainsString('https://api.unirateapi.com/api/rates?', $captured);
        self::assertStringNotContainsString('com//api', $captured);
    }

    public function testMissingApiKeyThrows(): void
    {
        $client = new UniRateClient('', 'https://api.unirateapi.com', 15, static function (): array {
            return [200, '{}'];
        });
        $this->expectException(UniRateException::class);
        $client->getRate('USD', 'EUR');
    }

    public function testUnauthorizedThrows(): void
    {
        $client = $this->clientReturning([401, 'Unauthorized']);
        $this->expectException(UniRateException::class);
        $this->expectExceptionCode(401);
        $client->getRate('USD', 'EUR');
    }

    public function testNotFoundThrows(): void
    {
        $client = $this->clientReturning([404, 'Not found']);
        $this->expectException(UniRateException::class);
        $this->expectExceptionCode(404);
        $client->getRate('USD', 'ZZZ');
    }

    public function testRateLimitThrows(): void
    {
        $client = $this->clientReturning([429, 'Too many requests']);
        $this->expectException(UniRateException::class);
        $this->expectExceptionCode(429);
        $client->getRate('USD', 'EUR');
    }

    public function testServerErrorThrows(): void
    {
        $client = $this->clientReturning([500, 'boom']);
        $this->expectException(UniRateException::class);
        $this->expectExceptionCode(500);
        $client->getRate('USD', 'EUR');
    }

    public function testInvalidJsonThrows(): void
    {
        $client = $this->clientReturning([200, '<html>not json</html>']);
        $this->expectException(UniRateException::class);
        $client->getRate('USD', 'EUR');
    }
}
