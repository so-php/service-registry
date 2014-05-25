<?php


namespace SoPhp\ServiceRegistry;


use SoPhp\PubSub\PubSubInterface;

/**
 * Class ServiceRegistry
 * @package SoPhp\ServiceRegistry
 * TODO use event objects w/ pubsub
 */
class ServiceRegistry {
    /** @var string  */
    protected $instanceId;
    /** @var  PubSubInterface */
    protected $pubSub;

    /**
     * @return PubSubInterface
     */
    public function getPubSub()
    {
        return $this->pubSub;
    }

    /**
     * @param PubSubInterface $pubSub
     * @return self
     */
    public function setPubSub($pubSub)
    {
        $this->pubSub = $pubSub;
        return $this;
    }

    public function __construct(PubSubInterface $pubSub){
        $this->instanceId = uniqid();
        $this->setPubSub($pubSub);

        $pubSub->publish('hello'); // we don't care about our hello, so we can event it now before we sub
        $pubSub->subscribe('hello', array($this, 'onHello'));
        $pubSub->subscribe('goodbye', array($this, 'onGoodbye'));
        $pubSub->subscribe('eject', array($this, 'onEject'));
        $pubSub->subscribe('register', array($this, 'onRegister'));
        $pubSub->subscribe('unregister', array($this, 'onUnregister'));
        $pubSub->subscribe('sync', array($this, 'onSync'));
    }

    public function __destruct(){
        $this->getPubSub()->publish('goodbye');
    }

    /**
     * @return string
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }

    public function register() {
        $this->getPubSub()->publish('register');// todo
    }

    public function unregister() {

    }

    public function getInstance() {

    }
} 