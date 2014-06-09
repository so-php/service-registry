<?php


namespace SoPhp\ServiceRegistry;


use SoPhp\Amqp\EndpointDescriptor;
use SoPhp\Rpc\Server;

class Entry {
    /** @var  string */
    protected $processId;
    /** @var  string */
    protected $instanceId;
    /** @var  string */
    protected $serviceName;
    /** @var  EndpointDescriptor */
    protected $endpoint;

    /**
     * @return EndpointDescriptor
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param EndpointDescriptor $endpoint
     * @return self
     */
    public function setEndpoint(EndpointDescriptor $endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
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
     * @return array
     */
    public function toStorageArray(){
        return array(
            'processId' => $this->getProcessId(),
            'instanceId' => $this->getInstanceId(),
            'serviceName' => $this->getServiceName(),
            'endpoint' => $this->getEndpoint()->toJson()
        );
    }

    /**
     * @param array $data
     * @return Entry
     */
    public static function fromStorageArray(array $data){
        $entry = new self();
        $entry->setEndpoint(EndpointDescriptor::fromJson($data['endpoint']));
        $entry->setProcessId($data['processId']);
        $entry->setInstanceId($data['instanceId']);
        $entry->setServiceName($data['serviceName']);
        return $entry;
    }
} 