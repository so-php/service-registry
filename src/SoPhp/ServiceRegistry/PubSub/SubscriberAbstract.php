<?php


namespace SoPhp\ServiceRegistry\PubSub;


use SoPhp\PubSub\Event;
use SoPhp\PubSub\PubSubInterface;

abstract class SubscriberAbstract implements SubscriberInterface {
    /**
     * @param PubSubInterface $pubSub
     */
    public function subscribe(PubSubInterface $pubSub){
        $pubSub->subscribe(self::EVENT_HELLO, array($this, 'onHello'));
        $pubSub->subscribe(self::EVENT_HELLO, array($this, 'onGoodbye'));
        $pubSub->subscribe(self::EVENT_HELLO, array($this, 'onEject'));
        $pubSub->subscribe(self::EVENT_HELLO, array($this, 'onRegister'));
        $pubSub->subscribe(self::EVENT_HELLO, array($this, 'onUnregister'));
        $pubSub->subscribe(self::EVENT_HELLO, array($this, 'onSync'));
    }
}