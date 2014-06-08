<?php


namespace SoPhp\ServiceRegistry\PubSub\Event;


use SoPhp\ServiceRegistry\PubSub\SubscriberInterface;

class GoodbyeEvent extends Event {
    public function __construct()
    {
        parent::__construct();
        $this->setName(SubscriberInterface::EVENT_GOODBYE);
    }

} 