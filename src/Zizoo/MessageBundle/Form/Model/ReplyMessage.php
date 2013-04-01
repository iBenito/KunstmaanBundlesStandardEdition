<?php

namespace Zizoo\MessageBundle\Form\Model;

use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\FormModel\AbstractMessage as AbstractMessage;

class ReplyMessage extends AbstractMessage
{
    /**
     * The thread we reply to
     *
     * @var ThreadInterface
     */
    protected $thread;

    protected $messageType;
    
    /**
     * @return ThreadInterface
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * @param  ThreadInterface
     * @return null
     */
    public function setThread(ThreadInterface $thread)
    {
        $this->thread = $thread;
    }
    
    public function setMessageType($type){
        $this->messageType = $type;
    }
    
    public function getMessageType(){
        return $this->messageType;
    }
}
