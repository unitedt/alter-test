<?php

namespace App\Controller;

use App\Dao\AccountDaoInterface;
use App\Dto\TransferRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TransferController
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var AccountDaoInterface
     */
    private $accountDao;

    /**
     * TransferController constructor.
     * @param ValidatorInterface $validator
     * @param AccountDaoInterface $accountDao
     */
    public function __construct(ValidatorInterface $validator, AccountDaoInterface $accountDao)
    {
        $this->validator = $validator;
        $this->accountDao = $accountDao;
    }

    /**
     * Transfer money from one account to another
     *
     * @Route(
     *     methods="POST",
     *     name="transfer",
     *     path="accounts/transfer",
     *     defaults={"_api_item_operation_name"="transfer"},
     * )
     * @param TransferRequest $request
     * @return JsonResponse
     * @throws BadRequestHttpException
     */
    public function __invoke(TransferRequest $request)
    {
        $errors = $this->validator->validate($request);

        if (\count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        $result = $this->accountDao->transfer($request->getAccountFrom(), $request->getAccountTo(), $request->getAmount());

        return new JsonResponse($result);
    }

}

