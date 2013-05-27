<?php

namespace Raindrop\PageBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Description of AddAssetsFieldSubscriber
 *
 * @author teito
 */
class AddAssetsFieldSubscriber implements EventSubscriberInterface
{
    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_BIND => 'preBind'
        );
    }

    public function preBind(FormEvent $event)
    {
        $data = $event->getData();

        $jss = array_filter($data['javascripts'], function ($js) {
            return !empty($js);
        });

        $css = array_filter($data['stylesheets'], function ($cs) {
            return !empty($cs);
        });

        $data['javascripts'] = $jss;
        $data['stylesheets'] = $css;

        $event->setData($data);
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

        // add existing fields
        $jss = $data->getJavascripts();
        if (!empty($jss)) {
            foreach ($jss as $js) {
                $form->add($this->factory->createNamed('javascripts', 'multiple_text', $js, array(
                    'required' => false,
                    'attr' => array(
                        'class' => 'span5'
                    )
                )));
            }
        }

        $form->add($this->factory->createNamed('javascripts', 'multiple_text', null, array(
            'required' => false,
            'attr' => array(
                'class' => 'span5'
            )
        )));

        // add existing fields
        $csss = $data->getJavascripts();
        if (!empty($csss)) {
            foreach ($csss as $css) {
                $form->add($this->factory->createNamed('stylesheets', 'multiple_text', $css, array(
                    'required' => false,
                    'attr' => array(
                        'class' => 'span5'
                    )
                )));
            }
        }

        $form->add($this->factory->createNamed('stylesheets', 'multiple_text', null, array(
            'required' => false,
            'attr' => array(
                'class' => 'span5'
            )
        )));

    }
}
