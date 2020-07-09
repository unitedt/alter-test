<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints as Assert;

class TransferRequest
{
    /**
     * @var int
     * @Assert\NotBlank()
     */
    private $accountFrom;

    /**
     * @var int
     * @Assert\NotBlank()
     */
    private $accountTo;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\PositiveOrZero
     */
    private $amount;

    /**
     * TransferRequest constructor.
     * @param RequestStack $requestStack
     * @throws BadRequestHttpException
     * @throws \LogicException
     */
    public function __construct(RequestStack $requestStack)
    {
        $request = $requestStack->getCurrentRequest();

        if (null === $request) {
            throw new BadRequestHttpException('Empty current request');
        }

        $data = json_decode($request->getContent(), true);
        $this->accountFrom = $data['accountFrom'] ?? null;
        $this->accountTo = $data['accountTo'] ?? null;
        $this->amount = $data['amount'] ?? null;
    }

    /**
     * @return int
     */
    public function getAccountFrom(): int
    {
        return $this->accountFrom;
    }

    /**
     * @return int
     */
    public function getAccountTo(): int
    {
        return $this->accountTo;
    }

    /**
     * @return float
     */
    public function getAmount(): string
    {
        return $this->amount;
    }



}