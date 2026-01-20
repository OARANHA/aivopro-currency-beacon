# Currency Beacon plugin for AiVoPro

This plugin provides a [CurrencyBeacon](https://currencybeacon.com/) integration for AiVoPro. Plugin is available publicly with MIT license for a reference implementation of a plugin for AiVoPro.

## Features

- Real-time exchange rates for over 168 world currencies
- Historical exchange rates dating back to 1996
- Currency conversion functionality
- Bank-level 256-bit SSL encryption
- Support for fiat and cryptocurrencies

## Installation

Packed version of the plugin is available in the [releases](https://github.com/heyaikeedo/plugins-currency-beacon/releases) section to install through the AiVoPro UI.

## Configuration

To use this plugin, you need to:

1. Obtain an API key from [CurrencyBeacon Developer Portal](https://currencybeacon.com/account/dashboard)
2. Configure the API key in AiVoPro settings
3. Select CurrencyBeacon as your default currency provider

## API Documentation

For detailed API documentation, visit [currencybeacon.com/api-documentation](https://currencybeacon.com/api-documentation).

### Key API Endpoints

- **Latest Exchange Rates**: `GET https://api.currencybeacon.com/v1/latest`
- **Historical Rates**: `GET https://api.currencybeacon.com/v1/historical`
- **Currency Conversion**: `GET https://api.currencybeacon.com/v1/convert`
- **Supported Currencies**: `GET https://api.currencybeacon.com/v1/currencies`

## Authentication

CurrencyBeacon uses API keys to control access. Pass your API key as a Bearer token in the Authorization header:

```bash
curl -X GET "https://api.currencybeacon.com/v1/latest?base=USD" \
  -H "Authorization: Bearer YOUR_ACCESS_KEY"
```

## License

MIT License - See [LICENSE](LICENSE) file for details.

## Support

For support and issues, visit:
- Documentation: https://currencybeacon.com/api-documentation
- Issues: https://github.com/heyaikeedo/plugins-currency-beacon/issues
- Email: contato@28facil.com.br

---

## Changelog

### Version 2.0.0 (2026-01-19)

- **Updated to CurrencyBeacon API v1.0**
- **Changed authentication method from query parameter to Bearer token**
- **Updated API base URL to https://api.currencybeacon.com/v1**
- **Added currency conversion endpoint support**
- **Improved error handling and caching**
- **Fixed compatibility with AiVoPro**
- **Updated documentation links**

### Version 1.0.0 (2024-07-14)

- Initial release