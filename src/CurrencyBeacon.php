<?php

declare(strict_types=1);

namespace AiVoPro\CurrencyBeacon;

use AiVoPro\Infrastructure\CommandBus\Dispatcher;
use AiVoPro\Infrastructure\Currency\Application\CurrencyCode;
use AiVoPro\Infrastructure\Currency\Application\RateProviderInterface;
use AiVoPro\Option\Application\Commands\SaveOptionCommand;
use Easy\Container\Attributes\Inject;
use Easy\Http\Message\RequestInterface;
use Easy\Http\ResponseInterface;
use Psr\Http\Client\ClientInterface;
use RuntimeException;

class CurrencyBeacon implements RateProviderInterface
{
    public const LOOKUP_KEY = 'currency-beacon';
    public const API_BASE_URL = 'https://api.currencybeacon.com/v1';

    private ?array $rates = null;
    private ?int $updatedAt = null;

    public function __construct(
        private ClientInterface $client,
        private Dispatcher $dispatcher,
        #[Inject('option.currency_beacon.api_key')]
        private ?string $apiKey = null,
        #[Inject('option.currency_beacon')]
        ?string $option = null,
    ) {
        if ($option) {
            $data = json_decode($option, true);
            $this->updatedAt = $data['updated_at'] ?? null;
            $this->rates = $data['rates'] ?? null;
        }
    }

    public function getTitle(): string
    {
        return 'Currency Beacon';
    }

    public function getLookupKey(): string
    {
        return self::LOOKUP_KEY;
    }

    public function getRate(CurrencyCode $from, CurrencyCode $to): float
    {
        $rates = $this->getRates();

        if (!isset($rates[$to->value]) || !isset($rates[$from->value])) {
            throw new RuntimeException(sprintf(
                'Currency code not supported: %s or %s',
                $from->value,
                $to->value
            ));
        }

        return $rates[$to->value] / $rates[$from->value];
    }

    private function getRates(): array
    {
        if (
            $this->rates
            && $this->updatedAt
            && $this->updatedAt + 3600 * 4 >= time()
        ) {
            return $this->rates;
        }

        $rates = $this->fetchRates();
        if (empty($rates)) {
            throw new RuntimeException('Failed to fetch currency rates from CurrencyBeacon API');
        }

        $cmd = new SaveOptionCommand(
            'currency_beacon',
            json_encode([
                'updated_at' => time(),
                'rates' => $rates,
            ])
        );
        $this->dispatcher->dispatch($cmd);
        return $rates;
    }

    private function fetchRates(): array
    {
        if (!$this->apiKey) {
            throw new RuntimeException('CurrencyBeacon API key is not configured');
        }

        try {
            $response = $this->client->sendRequest('GET', self::API_BASE_URL . '/latest', [
                'api_key' => $this->apiKey,
                'base' => 'USD'
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new RuntimeException(sprintf(
                    'CurrencyBeacon API returned status code: %d',
                    $response->getStatusCode()
                ));
            }

            $body = json_decode($response->getBody()->getContents(), true);
            if (!isset($body['response']['rates']) || !is_array($body['response']['rates'])) {
                throw new RuntimeException('Invalid response from CurrencyBeacon API');
            }

            $rates = [];
            foreach ($body['response']['rates'] as $code => $rate) {
                $rates[$code] = $rate;
            }

            return $rates;
        } catch (\Exception $e) {
            throw new RuntimeException(
                sprintf('Failed to fetch rates from CurrencyBeacon: %s', $e->getMessage()),
                0,
                $e
            );
        }
    }

    public function convert(float $amount, CurrencyCode $from, CurrencyCode $to): float
    {
        $rate = $this->getRate($from, $to);
        return $amount * $rate;
    }
}