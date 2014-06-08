<?php


namespace SoPhp\ServiceRegistry\PubSub\Event;


use SoPhp\Amqp\EndpointDescriptor;
use SoPhp\ServiceRegistry\PubSub\SubscriberInterface;

class UnregisterEvent extends RegisterEvent {

    public function __construct($serviceName, EndpointDescriptor $endpoint)
    {
        parent::__construct($serviceName, $endpoint);
        $this->setName(SubscriberInterface::EVENT_UNREGISTER);
    }


} 