<?php

namespace BAGArt\SelectelStorage;

use OpenCloud\Common\Service\ServiceBuilder;
use OpenCloud\ObjectStore\Resource\Container;

class SelectelOpenStack extends \OpenCloud\OpenStack
{
    protected $allowed_secret_keys = [
        //default
        'username' => null,
        'password' => null,
        'tenantId' => null,
        'tenantName' => null,
        //extra
        'tempUrlSecret' => null,

        //auto build
        'container' => null,

        //selectel preset
        'region' => 'ru-1',
        'serviceName' => 'swift',

        //'endpoint' => null,
    ];

    public function __construct($url, array $secret, array $options = array())
    {
        $secret = array_intersect_key($secret, $this->allowed_secret_keys);

        parent::__construct($url, $secret, $options);
    }

    public function getSecret($key = null)
    {
        return parent::getSecret()[$key] ?? $this->allowed_secret_keys[$key] ?? null;
    }

    protected function buildService($service_name = null, $region = null, $urltype = null): SelectelService
    {
        return ServiceBuilder::factory(
            $this,
            SelectelService::class,
            [
                'name' => $service_name ?? $this->getSecret('serviceName'),
                'region' => $region ?? $this->getSecret('region'),
                'urlType' => $urltype
            ]
        );
    }

    /**
     * @param string|null $service_name
     * @param string|null $region
     * @param string|null $urltype
     * @return SelectelService|\OpenCloud\ObjectStore\Service
     */
    public function objectStoreService($service_name = null, $region = null, $urltype = null)
    {
        $service = $this->buildService($service_name, $region, $urltype);

        $tempUrlSecret = $this->getSecret('tempUrlSecret');

        if ($tempUrlSecret) {
            $service->getAccount()->presetTempUrlSecret($tempUrlSecret);

//            (function () use ($tempUrlSecret) {
//                $this->tempUrlSecret = $tempUrlSecret;
//            })->call($service->getAccount());
        }

        return $service;
    }

    public function buildContainer(
        $container_name = null,
        $service_name = null,
        $region = null,
        $urltype = null
    ): Container {
        return $this
            ->objectStoreService($service_name, $region, $urltype)
            ->getContainer(
                $container_name ?? $this->getSecret('container')
            );
    }
}