<?php


namespace SoPhp\ServiceRegistry;


interface ServiceRegistryInterface {
    /**
     * @param string $serviceName
     * @param mixed $instance
     * @return ServiceRegistration
     */
    public function register($serviceName, $instance);

    /**
     * @param ServiceRegistration $registration
     */
    public function unregister(ServiceRegistration $registration);

    /**
     * @return ServiceRegistration[]
     */
    public function query();

    /**
     * @param string $serviceName
     * @return ServiceRegistration[]
     */
    public function queryForName($serviceName);
} 