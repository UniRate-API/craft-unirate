# UniRate Currency for Craft CMS

Live currency **exchange rates**, **conversion**, **currency lists**, and **VAT rates** in your [Craft CMS](https://craftcms.com) 5 templates, powered by the [UniRate API](https://unirateapi.com).

- **Zero third-party runtime dependencies** — a tiny built-in client over PHP's bundled cURL + JSON. Nothing extra is pulled into your project.
- Free-tier endpoints only (rate / convert / currencies / VAT), so a template call never trips a paid-plan error.
- API key is environment-variable aware (`$UNIRATE_API_KEY`) — no secrets in your project config.

## Requirements

Craft CMS 5.0.0+ and PHP 8.2+.

## Installation

From the Craft **Plugin Store**, search for "UniRate Currency" and install, or from your terminal:

```bash
composer require unirate-api/craft-unirate
php craft plugin/install unirate
```

## Configuration

Get a free API key at [unirateapi.com](https://unirateapi.com), then set it in
**Settings → Plugins → UniRate Currency**. The key field accepts an environment
variable reference, so the recommended setup is to add to your `.env`:

```
UNIRATE_API_KEY=your-key-here
```

and leave the setting as `$UNIRATE_API_KEY`.

## Usage

The plugin exposes a `craft.unirate` Twig variable:

```twig
{# Exchange rate #}
{{ craft.unirate.rate('USD', 'EUR') }}

{# Convert an amount #}
{{ craft.unirate.convert(100, 'USD', 'EUR') }}

{# All supported currency codes #}
{% for code in craft.unirate.currencies() %}
  {{ code }}
{% endfor %}

{# VAT rates for a country #}
{% set vat = craft.unirate.vatRates('DE') %}
```

Each call throws a catchable `UniRateException` on an API or transport error, so
wrap calls in `{% apply … %}`/`try` where a missing rate should not break a page.

## License

[MIT](LICENSE.md). Built by [UniRate](https://unirateapi.com).
