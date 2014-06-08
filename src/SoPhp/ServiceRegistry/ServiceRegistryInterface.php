<?php


namespace SoPhp\ServiceRegistry;


use SoPhp\Amqp\EndpointDescriptor;

interface ServiceRegistryInterface {
    /**
     * @param string $serviceName
     * @param mixed $instance
     * @return EndpointDescriptor
     */
    public function register($serviceName, $instance);

    /**
     * @param string $serviceName
     * @param null|mixed $instance
     */
    public function unregister($serviceName, $instance = null);

    /**
     * @return EndpointDescriptor[]
     */
    public function query();

    /**
     * @param $serviceName
     * @return EndpointDescriptor[]
     */
    public function queryForName($serviceName);
} 