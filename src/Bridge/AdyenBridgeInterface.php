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

interface AdyenBridgeInterface
{
    const TEST_ENVIRONMENT = 'test';
    const LIVE_ENVIRONMENT = 'live';
    const AUTHORISATION = 'AUTHORISATION';
    const AUTHORISED = 'AUTHORISED';
    const REFUSED = 'REFUSED';
    const PENDING = 'PENDING';
    const CAPTURE = 'CAPTURE';
    const CANCELLED = 'CANCELLED';
    const CANCELLATION = 'CANCELLATION';
    const CANCEL_OR_REFUND = 'CANCEL_OR_REFUND';
    const ERROR = 'ERROR';
    const NOTIFICATION_OF_CHARGEBACK = 'NOTIFICATION_OF_CHARGEBACK';
    const CHARGEBACK = 'CHARGEBACK';
    const CHARGEBACK_REVERSED = 'CHARGEBACK_REVERSED';
    const REFUND_FAILED = 'REFUND_FAILED';
    const CAPTURE_FAILED = 'CAPTURE_FAILED';
    const EXPIRE = 'EXPIRE';
    const REFUND = 'REFUND';
    const REFUNDED_REVERSED = 'REFUNDED_REVERSED';

    public function createSession(array $params);

    public function verifyRequest(array $query, array $details);
}
