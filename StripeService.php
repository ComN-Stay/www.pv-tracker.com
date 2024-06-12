<?php

namespace App\Service;

use Stripe\StripeClient;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;

class StripeService
{
    private $stripeSecret;
    private $entityManager;

    public function __construct(
        $stripeSecret,
        EntityManagerInterface $entityManager
    )
    {
        $this->stripeSecret = $stripeSecret;
        $this->entityManager = $entityManager;
    }
    
    public function checkout(
        $invoice, 
        $company, 
        $email
    )
    {
        header('Content-Type: application/json');
        Stripe::setApiKey($this->stripeSecret);
        Stripe::setApiVersion('2020-08-27');
        $stripe = new StripeClient($this->stripeSecret);

        if ($company->getStripeId() == null) {
            $customer = Customer::create([
                'email' => $email,
                'address' => [
                    'line1' => $email,
                    'line2' => $company->getAddress(),
                    'postal_code' => $company->getZipCode(),
                    'city' => ucwords(strtolower($company->getTown())),
                    'country' => $company->getFkCountry()->getIsoCode()
                ],
                'name' => $company->getName()
            ]);
            // on met à jour l'ID stripe du suser
            $company->setStripeId($customer->id);
            $this->entityManager->persist($company);
            $this->entityManager->flush();
        } else {
            $stripe->customers->update(
                $company->getStripeId(),
                [
                    'email' => $email,
                    'address' => [
                        'line1' => $email,
                        'line2' => $company->getAddress(),
                        'postal_code' => $company->getZipCode(),
                        'city' => ucwords(strtolower($company->getTown())),
                        'country' => $company->getFkCountry()->getIsoCode()
                    ]
                ]
            );

            $customer = $stripe->customers->retrieve(
                $company->getStripeId()
            );
        }

        $checkout_session = Session::create([
            'customer' => $customer->id,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $invoice->getFkProject()->getTitle(),
                    ],
                    'unit_amount' => round($invoice->getTotalAmount() * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'https://www.comnstay.fr/account/success',
            'cancel_url' => 'https://www.comnstay.fr/account/cancel',
        ]);
        $invoice->setStripePi($checkout_session->payment_intent);
        $this->entityManager->persist($company);
        $this->entityManager->flush();

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);
    }
}