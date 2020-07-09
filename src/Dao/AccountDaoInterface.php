<?php

namespace App\Dao;

use App\Dto\TransferResponse;

interface AccountDaoInterface
{
    /**
     * @param int $accountFrom
     * @param int $accountTo
     * @param string $amount
     * @return TransferResponse
     */
    public function transfer(int $accountFrom, int $accountTo, string $amount): TransferResponse;
}

