<?php


namespace SoPhp\ServiceRegistry;


use SoPhp\Amqp\EndpointDescriptor;
use SoPhp\Rpc\Server;
use SoPhp\ServiceRegistry\PubSub\Event\RegisterEvent;

class Entry {
    /** @var  int */
    protected $processId;
    /** @var  string */
    protected $serviceName;
    /** @var  EndpointDescriptor */
    protected $endpoint;
    /** @var  Server */
    protected $rpcServer;

    /**
     * @param RegisterEvent $e
     */
    public static function fromRegisterEvent(RegisterEvent $e){
        $entry = new self();
        $entry->setProcessId($e->getProcessId());
        $entry->setServiceName($e->getServiceName());
        $entry->setEndpoint($e->getEndpoint());
    }

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
     * @return int
     */
    public function getProcessId()
    {
        return $this->processId;
    }

    /**
     * @param int $processId
     * @return self
     */
    public function setProcessId($processId)
    {
        $this->processId = $processId;
        return $this;
    }

    /**
     * @return Server|null
     */
    public function getRpcServer()
    {
        return $this->rpcServer;
    }

    /**
     * @param Server $rpcServer
     * @return self
     */
    public function setRpcServer($rpcServer)
    {
        $this->rpcServer = $rpcServer;
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
        $entry->setServiceName($data['serviceName']);
        return $entry;
    }
} 