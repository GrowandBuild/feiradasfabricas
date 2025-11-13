<?php

namespace App\Services\Shipping\Contracts;

interface ShippingProviderInterface
{
    /**
     * Request shipping quotes for given origin/destination and package data.
     *
     * Expected $origin and $destination shape:
     *  [ 'cep' => '01234000', 'city' => optional, 'state' => optional ]
     * $packages: array of packages each with [weight, length, height, width, value(optional)]
     * Return: array of associative arrays (quotes) with keys:
     *  provider, service_code, service_name, price (float), delivery_time (int days), delivery_time_text, error|null
     */
    public function quote(array $origin, array $destination, array $packages): array;

    /**
     * Track shipment by provider tracking code.
     * Return: [ success => bool, events => [...], raw => mixed, error? ]
     */
    public function track(string $trackingCode): array;

    /**
     * Create a shipment order at the provider.
     * Return: [ success => bool, tracking_code? => string, label_url? => string, error? ]
     */
    public function create(array $shipmentData): array;

    /**
     * Provider machine name (e.g. 'correios').
     */
    public function getName(): string;
}
