<?php

namespace App\Form;

use App\Entity\Order;
use App\Form\EventListener\ClearCartListenerType;
use App\Form\EventListener\RemoveCartItemListenerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CartType
 * @package App\Form
 */
class CartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $builder
//            ->add('items', CollectionType::class, [
//                'entry_type' => CartItemType::class
//            ])
//            ->add('save', SubmitType::class)
//            ->add('clear', SubmitType::class);

        $builder->add('clear');
        $builder->add('add', SubmitType::class, [
            'label' => 'Add to cart'
        ]);

//        $builder->addEventSubscriber(new ClearCartListenerType());
//        $builder->addEventSubscriber(new RemoveCartItemListenerType());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
