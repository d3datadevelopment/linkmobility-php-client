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
use D3\LinkmobilityClient\ValueObject\Recipient;

interface RecipientsListInterface
{
    /**
     * @deprecated unused client parameter will remove
     * @param Client $client
     */
    public function __construct(Client $client);

    public function add(Recipient $recipient): RecipientsListInterface;

    public function clearRecipents(): RecipientsListInterface;

    public function getRecipients(): array;

    public function getRecipientsList(): array;
}
