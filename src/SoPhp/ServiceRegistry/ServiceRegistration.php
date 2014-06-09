<?php


namespace SoPhp\ServiceRegistry;


use SoPhp\Rpc\Client;
use SoPhp\Rpc\ServiceInterface;

class ServiceRegistration {
    /** @var  string */
    protected $instanceId;
    /** @var  string */
    protected $processId;
    /** @var  ServiceInterface */
    protected $service;
    /** @var  string */
    protected $serviceName;
    /** @var  ServiceRegistry */
    protected $serviceRegistry;

    /**
     * @param string $serviceName
     * @param ServiceInterface $service
     * @param ServiceRegistry $serviceRegistry
     */
    function __construct($serviceName, ServiceInterface $service, ServiceRegistry $serviceRegistry)
    {
        $this->instanceId = uniqid('so-php-svc.',true);
        $this->setServiceName($serviceName);
        $this->setService($service);
        $this->setServiceRegistry($serviceRegistry);
        $this->setProcessId(getmypid());
    }

    /**
     * @return string
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }

    /**
     * @param string $instanceId
     * @return self
     */
    public function setInstanceId($instanceId)
    {
        $this->instanceId = $instanceId;
        return $this;
    }

    /**
     * @return string
     */
    public function getProcessId()
    {
        return $this->processId;
    }

    /**
     * @param string $processId
     * @return self
     */
    public function setProcessId($processId)
    {
        $this->processId = $processId;
        return $this;
    }

    /**
     * @return mixed|Client
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param ServiceInterface $service
     * @return self
     */
    public function setService(ServiceInterface $service)
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
     * @return ServiceRegistry
     */
    public function getServiceRegistry()
    {
        return $this->serviceRegistry;
    }

    /**
     * @param ServiceRegistry $serviceRegistry
     * @return self
     */
    public function setServiceRegistry($serviceRegistry)
    {
        $this->serviceRegistry = $serviceRegistry;
        return $this;
    }

    /**
     *
     */
    public function unregister()
    {
        $this->getServiceRegistry()->unregister($this);
    }

    /**
     * @return Entry
     */
    public function toEntry(){
        $entry = new Entry();
        $entry->setInstanceId($this->getInstanceId());
        $entry->setProcessId($this->getProcessId());
        $entry->setEndpoint($this->getService()->getEndpoint());
        $entry->setServiceName($this->getServiceName());
        return $entry;
    }
}