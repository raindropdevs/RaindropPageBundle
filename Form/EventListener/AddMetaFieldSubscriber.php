<?php

namespace Raindrop\PageBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Description of AddMetaFieldSubscriber
 *
 * @author teito
 */
class AddMetaFieldSubscriber implements EventSubscriberInterface
{
    private $factory, $http_metas;

    public function __construct(FormFactoryInterface $factory, $http_metas)
    {
        $this->factory = $factory;
        $this->http_metas = $http_metas;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // During form creation setData() is called with null as an argument
        // by the FormBuilder constructor. You're only concerned with when
        // setData is called with an actual Entity object in it (whether new
        // or fetched with Doctrine). This if statement lets you skip right
        // over the null condition.
        if (null === $data) {
            return;
        }

        foreach ($this->http_metas as $type => $keys) {
            $name = 'metas_' . $type;
            $getter = 'getMetas' . Container::camelize($type);

            $form->add($this->factory->createNamed(
                $name,
                'sonata_type_immutable_array',
                $data ? $data->$getter() : array(),
                array(
                    'required' => false,
                    'keys' => $keys,
                    'attr' => array(
                        'class' => 'raindropMeta'
                    )
                )
            ));
        }
    }
}
