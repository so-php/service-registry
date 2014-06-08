<?php


namespace SoPhp\ServiceRegistry\PubSub\Event;
use \SoPhp\PubSub\Event as PubSubEvent;

class Event extends PubSubEvent {
    const PARAM_PROCESS_ID = 'processId';

    public function __construct(){
        parent::__construct(null);
        $this->setProcessId(getmypid());
    }

    /**
     * @return int
     */
    public function getProcessId(){
        return $this->getParam(self::PARAM_PROCESS_ID);
    }

    /**
     * @param int $value
     */
    public function setProcessId($value){
        $this->setParam(self::PARAM_PROCESS_ID, $value);
    }
}