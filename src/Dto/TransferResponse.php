<?php

namespace App\Dto;

use App\Entity\Account;

class TransferResponse implements \JsonSerializable
{
    /**
     * @var Account
     */
    private $accountFrom;

    /**
     * @var Account
     */
    private $accountTo;

    /**
     * TransferResponse constructor.
     * @param Account $accountFrom
     * @param Account $accountTo
     */
    public function __construct(Account $accountFrom, Account $accountTo)
    {
        $this->accountFrom = $accountFrom;
        $this->accountTo = $accountTo;
    }

    /**
     * @return Account
     */
    public function getAccountFrom(): Account
    {
        return $this->accountFrom;
    }

    /**
     * @return Account
     */
    public function getAccountTo(): Account
    {
        return $this->accountTo;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'accountFrom' => $this->getAccountFrom(),
            'accountTo' => $this->getAccountTo(),
        ];
    }
}