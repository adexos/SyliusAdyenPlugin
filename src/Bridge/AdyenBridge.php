<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bridge;

use Adyen\Client;
use Adyen\Environment;
use Adyen\Service\Checkout;
use Adyen\Util\HmacSignature;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\HttpClientInterface;

final class AdyenBridge implements AdyenBridgeInterface
{
    private array $defaultOptions = [
        'env' => 'test',
        'apiKey' => 'AQEohmfxLInLaBdFw0m/n3Q5qf3VaY9IFZxZlXCpy15xhRNlRebeBI+Y9xDBXVsNvuR83LVYjEgiTGAH-x/Ww7p/Im8roaetP9QisnnRAx4fbS4pagNfOaS/7HEQ=-pHQB[Zm]vv99X~Sb',
        'merchantAccount' => 'Adexos'

    ];
    private ArrayObject $options;
    private ?Client $api = null;

    /**
     * @param array               $options
     *
     * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
     * @throws \Payum\Core\Exception\LogicException if a sandbox is not boolean
     */
    public function __construct(array $options)
    {
        $options = ArrayObject::ensureArrayObject($options);
        $options->defaults($this->defaultOptions);

        $this->options = $options;
    }

    public function createSession(array $params)
    {
        $service = new Checkout($this->getApi());
        $params['merchantAccount'] = $this->options['merchantAccount'];
        return $service->sessions($params);
    }

    private function getApi(): Client
    {
        if (null === $this->api) {
            $this->api = new Client();
            $this->api->setEnvironment($this->getEnvironment());
            $this->api->setXApiKey($this->options->get('apiKey'));
        }

        return $this->api;
    }

    /**
     * @return string
     */
    private function getEnvironment(): string
    {
        return $this->options->get('env', 'test') === 'test' ? Environment::TEST : Environment::LIVE;
    }


    public function verifyRequest(array $query, array $details)
    {
        return false;
    }
}
