<?php

namespace unirateapi\craftunirate\models;

use craft\base\Model;
use craft\behaviors\EnvAttributeParserBehavior;

/**
 * Plugin settings. The API key and base URL support environment variables and
 * aliases (e.g. `$UNIRATE_API_KEY`), resolved via the EnvAttributeParserBehavior.
 */
class Settings extends Model
{
    /**
     * UniRate API key. Free-tier keys are issued at https://unirateapi.com.
     * Supports `$ENV_VAR` references so the key is never committed.
     */
    public string $apiKey = '$UNIRATE_API_KEY';

    /**
     * API base URL — override only for testing or a self-hosted proxy.
     */
    public string $baseUrl = 'https://api.unirateapi.com';

    /**
     * Request timeout in seconds.
     */
    public int $timeout = 15;

    public function behaviors(): array
    {
        return [
            'parser' => [
                'class' => EnvAttributeParserBehavior::class,
                'attributes' => ['apiKey', 'baseUrl'],
            ],
        ];
    }

    protected function defineRules(): array
    {
        return [
            [['apiKey', 'baseUrl'], 'string'],
            [['baseUrl'], 'required'],
            [['timeout'], 'integer', 'min' => 1],
        ];
    }
}
