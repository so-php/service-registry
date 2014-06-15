<?php


namespace SoPhp\ServiceRegistry\AbstractFactory;


use SoPhp\Rpc\Proxy\ProxyAbstract;
use SoPhp\Rpc\Proxy\ProxyBuilder;
use SoPhp\ServiceRegistry\CircuitBreaker\CircuitBreaker;
use SoPhp\ServiceRegistry\RegistryService;
use SoPhp\ServiceRegistry\ServiceRegistryAwareInterface;
use SoPhp\ServiceRegistry\ServiceRegistryAwareTrait;
use SoPhp\ServiceRegistry\ServiceRegistryInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

class ServiceRegistry implements AbstractFactoryInterface, ServiceRegistryAwareInterface {
    use ServiceRegistryAwareTrait;

    public function __construct(ServiceRegistryInterface $serviceRegistry)
    {
        $this->setServiceRegistry($serviceRegistry);
    }

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return count($this->getServiceRegistry()->queryForName($name)) > 0 || count($this->getServiceRegistry()->queryForName($requestedName));
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $registrations = $this->getServiceRegistry()->queryForName($name);
        if(empty($registrations)){
            $registrations = $this->getServiceRegistry()->queryForName($requestedName);
        }
        if(empty($registrations)){
            return null;
        }

        // TODO implement different instance picking strategies
        $registration = $registrations[rand(0, count($registrations)-1)];

        $serviceWrapper = new RegistryService($registration->getService(), $registration);
        $circuitBreaker = new CircuitBreaker($serviceWrapper, $registration->getServiceName());
        $circuitBreaker->setServiceLocator($serviceLocator);

        $builder = new ProxyBuilder();
        if(interface_exists($requestedName)){
            $builder->setImplements($requestedName);
        }
        $proxyClass = $builder->build();
        /** @var ProxyAbstract $instance */
        $instance = new $proxyClass();
        $instance->__setService($circuitBreaker);

        return $instance;
    }
}