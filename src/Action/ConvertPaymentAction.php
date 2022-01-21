<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Action;

use BitBag\SyliusAdyenPlugin\Bridge\AdyenBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Convert;
use Payum\Core\Request\RenderTemplate;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class ConvertPaymentAction implements ActionInterface, ApiAwareInterface, GenericTokenFactoryAwareInterface
{
    /**
     * @var AdyenBridgeInterface
     */
    protected $api;

    /**
     * {@inheritDoc}
     */
    public function setApi($api): void
    {
        if (false === $api instanceof AdyenBridgeInterface) {
            throw new UnsupportedApiException(sprintf('Not supported. Expected %s instance to be set as api.', AdyenBridgeInterface::class));
        }

        $this->api = $api;
    }

    /**
     * @param null|GenericTokenFactoryInterface $genericTokenFactory
     *
     * @return void
     */
    public function setGenericTokenFactory(GenericTokenFactoryInterface $genericTokenFactory = null): void
    {
        $this->tokenFactory = $genericTokenFactory;
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed|Convert $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        $token = $request->getToken();

        $notifyToken = $this->tokenFactory->createCaptureToken(
            $token->getGatewayName(),
            $token->getDetails(),
            'http://afterUrl'
        );

        $details = [];
        $details['reference'] = $order->getNumber() . "-" . $payment->getId();
        $details['amount'] = [
            'value' => $payment->getAmount(),
            'currency' => $payment->getCurrencyCode()
        ];
        $details['countryCode'] = null !== $order->getShippingAddress() ? $order->getShippingAddress()->getCountryCode() : null;
        $details['returnUrl'] = $notifyToken->getTargetUrl();

        $sessions = $this->api->createSession($details);

        $details['sessions'] = $sessions;
        $request->setResult($details);

        $template = new RenderTemplate('@BitBagSyliusAdyenPlugin/payment.html.twig', [
            'data' => $details['sessions'],
            'returnOk' => $details['returnUrl'],
        ]);

        $this->gateway->execute($template);

        throw new HttpResponse($template->getResult());

    }

    /**
     * {@inheritdoc}
     */
    public function supports($request): bool
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() === 'array'
        ;
    }
}
