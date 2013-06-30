<?php
// src/Acme/DemoBundle/Form/EventListener/AddNameFieldSubscriber.php
namespace Raindrop\PageBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;

class AddRouteFieldSubscriber implements EventSubscriberInterface
{
    private $factory, $options;

    public function __construct(FormFactoryInterface $factory, $options = array())
    {
        $this->factory = $factory;
        $this->options = $options;
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

        $url = $this->options['url'];

        if ($data->getRoute()) {
            // obj is not new so retrieve route
            $route = $data->getRoute();
            if ($route) {
                $url = $route->getPath();
            }
        }

        $form->add($this->factory->createNamed('url', 'text', $url, array(
            'mapped' => false,
            'data' => $url,
            'attr' => array(
                'class' => 'span7 raindropPageBundleUrl'
            ),
        )));
    }
}
