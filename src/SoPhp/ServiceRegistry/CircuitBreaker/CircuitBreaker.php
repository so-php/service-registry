<?php


namespace SoPhp\ServiceRegistry\CircuitBreaker;


use SoPhp\Amqp\EndpointDescriptor;
use SoPhp\Rpc\Exception\Server\TimeoutException;
use SoPhp\Rpc\Proxy\ProxyAbstract;
use SoPhp\Rpc\ServiceInterface;
use SoPhp\ServiceRegistry\CircuitBreaker\Exception\MaxRetryException;
use SoPhp\ServiceRegistry\CircuitBreaker\Exception\ServiceUnavailableException;
use SoPhp\ServiceRegistry\RegistryService;
use SoPhp\ServiceRegistry\ServiceRegistration;
use SoPhp\ServiceRegistry\ServiceRegistryInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;

class CircuitBreaker implements ServiceInterface, ServiceLocatorAwareInterface {
    use ServiceLocatorAwareTrait;
    /** @var  ServiceInterface */
    protected $service;
    /** @var  string */
    protected $serviceName;

    /**
     * @param ServiceInterface $service
     * @param string $serviceName
     */
    function __construct(ServiceInterface $service, $serviceName)
    {
        $this->setService($service);
        $this->setServiceName($serviceName);
    }


    /**
     * @return ServiceInterface
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param ServiceInterface $service
     * @return self
     */
    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @param string $serviceName
     * @return self
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
        return $this;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function call($name, $arguments)
    {
        $count = 0;
        do {
            try {
                return $this->getService()->call($name, $arguments);
            } catch (TimeoutException $e) {
                $this->reacquire();
            }
        } while($count++ < 5);
        throw new MaxRetryException("Attempt to call `$name` on `{$this->getServiceName()}` failed too many times.");
    }

    /**
     * @return EndpointDescriptor
     */
    public function getEndpoint()
    {
        return $this->getService()->getEndpoint();
    }

    /**
     * @param EndpointDescriptor $endpoint
     */
    public function setEndpoint(EndpointDescriptor $endpoint)
    {
        $this->getService()->setEndpoint($endpoint);
    }


    protected function reacquire()
    {
        $this->attemptUnregister();
        try {
            $newService = $this->getServiceLocator()->get($this->getServiceName());
            $this->extractService($newService);
        } catch (\Exception $e) {
            throw new ServiceUnavailableException("Could not get an instance of '{$this->getServiceName()}'", 0, $e);
        }
    }

    protected function attemptUnregister()
    {
        $service = $this->getService();
        if($service instanceof RegistryService){
            $service->getServiceRegistration()->unregister();
        }
    }

    protected function extractService($service)
    {
        if($service instanceof ProxyAbstract) {
            $this->extractService($service->__getService());
        } else if($service instanceof CircuitBreaker) {
            $this->extractService($service->getService());
        } else {
            // should be a RegistryService
            $this->setService($service);
        }
    }
}