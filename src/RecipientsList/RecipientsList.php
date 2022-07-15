<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * https://www.d3data.de
 *
 * @copyright (C) D3 Data Development (Inh. Thomas Dartsch)
 * @author    D3 Data Development - Daniel Seifert <support@shopmodule.com>
 * @link      https://www.oxidmodule.com
 */

declare(strict_types=1);

namespace D3\LinkmobilityClient\RecipientsList;

use D3\LinkmobilityClient\Client;
use D3\LinkmobilityClient\Exceptions\ExceptionMessages;
use D3\LinkmobilityClient\Exceptions\RecipientException;
use D3\LinkmobilityClient\ValueObject\Recipient;
use Iterator;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;

class RecipientsList implements RecipientsListInterface, Iterator
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $recipients = [];

    public function __construct(Client $client)
    {
        $this->setClient($client);
    }

    /**
     * @return PhoneNumberUtil
     */
    protected function getPhoneNumberUtil(): PhoneNumberUtil
    {
        return PhoneNumberUtil::getInstance();
    }

    /**
     * @param Recipient $recipient
     *
     * @return RecipientsListInterface
     */
    public function add(Recipient $recipient): RecipientsListInterface
    {
        $phoneUtil = $this->getPhoneNumberUtil();
        try {
            $phoneNumber = $phoneUtil->parse($recipient->get(), $recipient->getCountryCode());

            if (false === $phoneUtil->isValidNumber($phoneNumber)) {
                throw new RecipientException(ExceptionMessages::INVALID_RECIPIENT_PHONE);
            } elseif (
                false === in_array(
                    $phoneUtil->getNumberType($phoneNumber),
                    [
                        PhoneNumberType::MOBILE,
                        PhoneNumberType::FIXED_LINE_OR_MOBILE
                    ]
                )
            ) {
                throw new RecipientException(ExceptionMessages::NOT_A_MOBILE_NUMBER);
            }

            $this->recipients[ md5(serialize($recipient)) ] = $recipient;
        } catch (NumberParseException $e) {
            $this->client->getLoggerHandler()->getLogger()->info($e->getMessage(), [$recipient]);
        } catch (RecipientException $e) {
            $this->client->getLoggerHandler()->getLogger()->info($e->getMessage(), [$recipient]);
        }

        return $this;
    }

    /**
     * @return RecipientsListInterface
     */
    public function clearRecipents(): RecipientsListInterface
    {
        $this->recipients = [];

        return $this;
    }

    /**
     * @return array
     */
    public function getRecipients(): array
    {
        return array_values(
            array_map(
                function (Recipient $recipient) {
                    return $recipient->getFormatted();
                },
                $this->recipients
            )
        );
    }

    /**
     * @return array
     */
    public function getRecipientsList(): array
    {
        return $this->recipients;
    }

    /**
     * @return false|mixed
     */
    public function current()
    {
        return current($this->recipients);
    }

    /**
     * @return false|mixed|void
     */
    public function next()
    {
        return next($this->recipients);
    }

    public function key()
    {
        return key($this->recipients);
    }

    public function rewind()
    {
        reset($this->recipients);
    }

    public function valid(): bool
    {
        return current($this->recipients) instanceof Recipient;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     *
     * @return RecipientsList
     */
    public function setClient(Client $client): RecipientsList
    {
        $this->client = $client;

        return $this;
    }
}
