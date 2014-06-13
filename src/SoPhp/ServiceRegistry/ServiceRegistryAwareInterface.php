<?php


namespace SoPhp\ServiceRegistry;


interface ServiceRegistryAwareInterface {
    /**
     * @param ServiceRegistryInterface $serviceRegistry
     */
    public function setServiceRegistry(ServiceRegistryInterface $serviceRegistry);

    /**
     * @return ServiceRegistryInterface
     */
    public function getServiceRegistry();
} 