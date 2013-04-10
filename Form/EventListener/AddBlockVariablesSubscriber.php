<?php

namespace Raindrop\PageBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\Container;


/**
 * Description of AddAssetsFieldSubscriber
 *
 * @author teito
 */
class AddBlockVariablesSubscriber implements EventSubscriberInterface {

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(
            FormEvents::POST_BIND => 'postBind',
            FormEvents::PRE_BIND => 'preBind'
        );
    }

    public function preBind(FormEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        unset($data['variables']);
        $event->setData($data);
    }
}

