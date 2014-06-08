<?php


namespace SoPhp\ServiceRegistry\PubSub\Event;


use SoPhp\ServiceRegistry\PubSub\SubscriberInterface;

class EjectEvent extends GoodbyeEvent {
    public function __construct()
    {
        parent::__construct();
        $this->setName(SubscriberInterface::EVENT_EJECT);
    }

} 