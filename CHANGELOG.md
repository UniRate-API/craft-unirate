# Release Notes for UniRate Currency

## 0.1.1 - 2026-11-15

### Changed
- Documentation URL now points to https://unirateapi.com.

## 0.1.0 - 2026-10-27

### Added
- Initial release.
- `craft.unirate` Twig variable with `rate()`, `convert()`, `currencies()`, and `vatRates()` helpers.
- Control-panel settings for the UniRate API key (environment-variable aware), base URL, and request timeout.
- Dependency-free HTTP client (PHP cURL + JSON) — no third-party runtime packages.
