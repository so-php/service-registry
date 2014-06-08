<?php


namespace SoPhp\ServiceRegistry\PubSub\Event;


use SoPhp\Amqp\EndpointDescriptor;
use SoPhp\ServiceRegistry\PubSub\SubscriberInterface;

class RegisterEvent extends Event {
    const PARAM_ENDPOINT = 'endpoint';
    const PARAM_SERVICE_NAME = 'serviceName';

    /**
     * @return EndpointDescriptor
     */
    public function getEndpoint()
    {
        $ep = $this->getParam(self::PARAM_ENDPOINT);
        if($ep instanceof EndpointDescriptor){
           return $ep;
        }
        if(is_object($ep)){
            EndpointDescriptor::fromStdClass($ep);
        }
    }

    /**
     * @param EndpointDescriptor $endpoint
     * @return self
     */
    public function setEndpoint(EndpointDescriptor $endpoint)
    {
        $this->setParam(self::PARAM_ENDPOINT, $endpoint);
        return $this;
    }

    public function getServiceName(){
        return $this->getParam(self::PARAM_SERVICE_NAME);
    }

    public function setServiceName($serviceName){
        $this->setParam(self::PARAM_SERVICE_NAME, $serviceName);
    }

    /**
     * @param string $serviceName
     * @param EndpointDescriptor $endpoint
     */
    public function __construct($serviceName, EndpointDescriptor $endpoint)
    {
        parent::__construct();
        $this->setName(SubscriberInterface::EVENT_REGISTER);
        $this->setServiceName($serviceName);
        $this->setEndpoint($endpoint);
    }

} 