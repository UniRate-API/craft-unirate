<?php

namespace unirateapi\craftunirate;

use Craft;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use craft\web\twig\variables\CraftVariable;
use unirateapi\craftunirate\models\Settings;
use unirateapi\craftunirate\services\Rates;
use yii\base\Event;

/**
 * UniRate Currency — live exchange rates, conversion, currency lists, and VAT
 * rates in Craft templates via the UniRate API (https://unirateapi.com).
 *
 * @property-read Rates $rates
 * @method Settings getSettings()
 */
class Plugin extends BasePlugin
{
    public static ?Plugin $plugin = null;

    public bool $hasCpSettings = true;

    public string $schemaVersion = '1.0.0';

    public static function config(): array
    {
        return [
            'components' => [
                'rates' => Rates::class,
            ],
        ];
    }

    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        // Expose `craft.unirate.*` in Twig templates.
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event): void {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('unirate', $this->rates);
            }
        );
    }

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('unirate/settings', [
            'settings' => $this->getSettings(),
        ]);
    }
}
