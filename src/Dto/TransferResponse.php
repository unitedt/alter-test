<?php

namespace App\Dto;

use App\Entity\Account;

class TransferResponse implements \JsonSerializable
{
    /**
     * @var string
     */
    private $message = "OK";

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
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
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
            'message' => $this->getMessage(),
            'result' => [
                'accountFrom' => $this->getAccountFrom(),
                'accountTo' => $this->getAccountTo(),
            ]
        ];
    }
}