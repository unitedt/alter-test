<?php

namespace App\Dao;

use App\Dto\TransferResponse;
use App\Entity\Account;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;

class AccountDao implements AccountDaoInterface
{
    private const TABLE_NAME = 'account';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * AccountDao constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $accountFrom
     * @param int $accountTo
     * @param string $amount
     * @return TransferResponse
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function transfer(int $accountFrom, int $accountTo, string $amount): TransferResponse
    {
        $conn = $this->entityManager->getConnection();

        $conn->beginTransaction();

        try {
            $rowCount = $conn->executeUpdate('UPDATE ' . self::TABLE_NAME . ' SET amount = amount - ? WHERE id = ?',
                [$amount, $accountFrom]);

            if (0 === $rowCount) {
                throw new \InvalidArgumentException('accountFrom id: ' . $accountFrom . ' not exists!');
            }

            $rowCount = $conn->executeUpdate('UPDATE ' . self::TABLE_NAME . ' SET amount = amount + ? WHERE id = ?',
                [$amount, $accountTo]);

            if (0 === $rowCount) {
                throw new \InvalidArgumentException('accountTo id: ' . $accountTo . ' not exists!');
            }

            $result = $this->getAccounts($accountFrom, $accountTo);
        }
        catch (DBALException $e) {
            # generally for check constraint violation
            $conn->rollBack();
            $result = $this->getAccounts($accountFrom, $accountTo);
            $result->setMessage($e->getMessage());
            return $result;

        }
        catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }

        $conn->commit();

        return $result;
    }

    /**
     * @param int $accountFrom
     * @param int $accountTo
     * @return TransferResponse
     * @throws \Exception
     */
    private function getAccounts(int $accountFrom, int $accountTo): TransferResponse
    {
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('SELECT id, amount FROM ' . self::TABLE_NAME . ' WHERE id IN (?, ?)');
        $stmt->execute([$accountFrom, $accountTo]);

        $ret = [];

        while ($row = $stmt->fetch()) {
            $account = new Account();
            $account->setId($row['id']);
            $account->setAmount($row['amount']);
            $ret[$account->getId()] = $account;
        }

        return new TransferResponse($ret[$accountFrom] ?? null, $ret[$accountTo] ?? null);
    }
}
