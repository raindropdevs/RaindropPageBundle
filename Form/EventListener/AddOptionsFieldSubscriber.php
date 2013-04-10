<?php

namespace Raindrop\PageBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Event\DataEvent;

/**
 * Description of AddOptionsFieldSubscriber
 *
 * @author teito
 */
class AddOptionsFieldSubscriber implements EventSubscriberInterface {

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.bind
        // event and that the onBind method should be called.
        return array(
            FormEvents::PRE_BIND => 'preBind'
        );
    }

    /**
     * Skip data validation and erase 'options' content, all
     * data will be retrieved within 'postUpdate/Persist' into admin class.
     * @param \Symfony\Component\Form\Event\DataEvent $event
     */
    public function preBind(DataEvent $event) {
        $data = $event->getData();
        $data['options'] = array();
        $event->setData($data);
    }
}

?>
