<?php

namespace App\Controller;

use App\Entity\Order;
use App\Storage\CartSessionStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CheckoutController
 * @package App\Controller
 */
#[Route('/checkout', name: 'checkout.')]
class CheckoutController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, CartSessionStorage $cartSessionStorage): Response
    {
        $form = $this->createFormBuilder()
            ->add('city_delivery', null , [
                'required' => true,
                'label' => 'City',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('postcode_delivery', null , [
                'required' => true,
                'label' => 'Post code',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('street_delivery', null , [
                'required' => true,
                'label' => 'Street',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('shipping_method', ChoiceType::class , [
                'choices' => [
                    'Pick up in store' => 'store_pickup',
                    'Courier' => 'courier'
                ],
                'required' => true,
                'label' => 'Shipping method',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('payment_method', ChoiceType::class , [
                'choices' => [
                    'Cash on delivery' => 'cash_on_delivery',
                    'Transfer' => 'transfer'
                ],
                'required' => true,
                'label' => 'Payment method',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('Proceed', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-lg btn-primary mt-2'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $cart = $cartSessionStorage->getCart();

            $cart->setCity($data['city_delivery']);
            $cart->setPostCode($data['postcode_delivery']);
            $cart->setStreet($data['street_delivery']);
            $cart->setShippingMethod($data['shipping_method']);
            $cart->setPaymentMethod($data['payment_method']);

            $cart->setStatus(Order::STATUS_NEW_ORDER);

            $em = $this->getDoctrine()->getManager();
            $em->persist($cart);
            $em->flush();

            return $this->redirect($this->generateUrl('checkout.success'));
        }

        return $this->render('checkout/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/success', name: 'success')]
    public function logout(): Response
    {
        return $this->render('checkout/success.html.twig', []);
    }
}
