<?php

namespace App\Form\EventListener;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class ClearCartListenerType
 * @package App\Form\EventListener
 */
class ClearCartListenerType extends AbstractType
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [FormEvents::POST_SUBMIT => 'postSubmit'];
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $cart = $form->getData();

        if (!$cart instanceof Order) {
            return;
        }

        if (!$form->get('clear')->isClicked()) {
            return;
        }

        $cart->removeItems();
    }
}
