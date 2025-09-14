<?php

use Safe4Work\Core\Events\EventDispatcher;

// Register event listener
EventDispatcher::add_filter_listener(
    'leantime.domain.connector.services.providers.loadProviders.providerList',
    function (mixed $payload) {

        $provider = app()->make(\Leantime\Domain\CsvImport\Services\CsvImport::class);
        $payload[$provider->id] = $provider;

        return $payload;
    }
);
