<?php


namespace SoPhp\ServiceRegistry\PubSub;


use SoPhp\PubSub\Event;

interface SubscriberInterface {
    const EVENT_HELLO = 'hello';
    const EVENT_GOODBYE = 'goodbye';
    const EVENT_EJECT = 'eject';
    const EVENT_REGISTER = 'register';
    const EVENT_UNREGISTER = 'unregister';
    const EVENT_SYNC = 'sync';

    /**
     * @param Event $e
     */
    public function onHello(Event $e);

    /**
     * @param Event $e
     */
    public function onGoodbye(Event $e);

    /**
     * @param Event $e
     */
    public function onEject(Event $e);

    /**
     * @param Event $e
     */
    public function onRegister(Event $e);

    /**
     * @param Event $e
     */
    public function onUnregister(Event $e);

    /**
     * @param Event $e
     */
    public function onSync(Event $e);
} 