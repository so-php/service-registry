<?php


namespace SoPhp\ServiceRegistry\PubSub;


use SoPhp\PubSub\Event;
use \SoPhp\ServiceRegistry\PubSub\Event\Event as SREvent;

class Logger extends SubscriberAbstract {

    /**
     * @param Event $e
     */
    public function onHello(Event $e)
    {
        $this->log($e);
    }

    /**
     * @param Event $e
     */
    public function onGoodbye(Event $e)
    {
        $this->log($e);
    }

    /**
     * @param Event $e
     */
    public function onEject(Event $e)
    {
        $this->log($e);
    }

    /**
     * @param Event $e
     */
    public function onRegister(Event $e)
    {
        $this->log($e);
    }

    /**
     * @param Event $e
     */
    public function onUnregister(Event $e)
    {
        $this->log($e);
    }

    /**
     * @param Event $e
     */
    public function onSync(Event $e)
    {
        $this->log($e);
    }

    /**
     * @param Event $e
     */
    protected function log(Event $e){
        /** @var SREvent $e */
        $e = SREvent::fromJson($e->toJson());
        echo sprintf("\t[ServiceRegistry:%i] %s @ %s : %s\n",$e->getProcessId(), $e->getName(), $e->getDateTime()->format('Y-m-d H:i:s'), json_encode($e->getParams()));
    }
}