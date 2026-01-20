<?php

declare(strict_types=1);

namespace AiVoPro\CurrencyBeacon;

use AiVoPro\Infrastructure\Currency\Application\RateProviderCollectionInterface;
use AiVoPro\Infrastructure\Plugin\PluginInterface;
use AiVoPro\Infrastructure\Router\Mapper;
use AiVoPro\Infrastructure\View\ViewLoaderInterface;

class Plugin implements PluginInterface
{
    public function __construct(
        private ViewLoaderInterface $loader,
        private Mapper $mapper,
        private RateProviderCollectionInterface $collection
    ) {}

    public function boot(): void
    {
        // Add plugin templates path to view loader
        $this->loader->addPath(__DIR__, 'currency-beacon');

        // Add path to router mapper to scan for routes
        // in current directory
        $this->mapper->addPath(__DIR__);

        // Add rate provider to collection
        $this->collection
            ->add(CurrencyBeacon::LOOKUP_KEY, CurrencyBeacon::class);
    }
}