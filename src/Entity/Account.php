<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Account
 *
 * @ApiResource(
 *  collectionOperations={
 *     "post"={"method"="POST"},
 *     "transfer"={
 *          "route_name"="transfer",
 *          "path"="accounts/transfer",
 *          "method"="POST",
 *          "input_formats"={"json"={"application/json"}},
 *          "swagger_context"={
 *              "operationId" = "transfer",
 *              "summary" = "Transfer money from one account to another",
 *              "description" = "Transfer money from one account to another",
 *              "consumes" = "application/json",
 *              "produces" = "application/json",
 *              "parameters" = {
 *                  {
 *                      "in" = "body",
 *                      "description" = "Transfer data",
 *                      "schema" = {
 *                          "type" = "object",
 *                          "required" = { "accountFrom", "accountTo", "amount" },
 *                          "properties" = {
 *                              "accountFrom" = {
 *                                  "type" = "integer",
 *                                  "description" = "Sender Account id",
 *                                  "example" = 1,
 *                              },
 *                              "accountTo" = {
 *                                  "type" = "integer",
 *                                  "description" = "Receiver Account id",
 *                                  "example" = 3,
 *                              },
 *                              "amount" = {
 *                                  "type" = "string",
 *                                  "description" = "Amount to transfer",
 *                                  "example" = "20.00",
 *                              },
 *                          }
 *                      }
 *                  },
 *              }
 *          },
 *     }
 *  },
 *  itemOperations={
 *     "post"={"method"="GET"},
 *  }
 * )
 * @ORM\Entity
 */
class Account implements \JsonSerializable
{
    /**
     * @var int The entity Id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var float Amount
     * (N.B.: it expects string on JSON input, to avoid float precision errors)
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, options={"check":"CHECK(amount >= 0)"})
     * @Assert\NotBlank
     * @Assert\PositiveOrZero
     */
    private $amount = 0;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Account
     */
    public function setId(int $id): Account
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return Account
     */
    public function setAmount(float $amount): Account
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'amount' => $this->getAmount(),
        ];
    }
}


