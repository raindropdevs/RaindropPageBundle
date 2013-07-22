<?php

namespace Raindrop\PageBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Description of AddTagSubscriber
 *
 * @author teito
 */
class AddTagsSubscriber implements EventSubscriberInterface
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

    /**
     * Cleanup tags
     * @param \Symfony\Component\Form\FormEvent $event
     * @return type
     */
    public function preBind(FormEvent $event)
    {
        $data = $event->getData();

        unset($data['tags']);

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

        // add existing tags
        $tags = $data->getTags();
        $values = array();

        if (count($tags)) {
            foreach ($tags as $tag) {
                $values []= $tag->getName();
            }
        }

        $form->add($this->factory->createNamed('tags', 'multiple_text', $values, array(
            'property_path' => false,
            'required' => false,
            'attr' => array(
                'class' => 'span5 pageTag'
            )
        )));
    }
}

?>
