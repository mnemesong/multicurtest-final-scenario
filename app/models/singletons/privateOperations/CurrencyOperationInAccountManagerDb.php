<?php

namespace app\models\singletons\privateOperations;

use app\models\activeRecords\CurrencyOperationInAccountAR;
use Pantagruel74\MulticurtestCurrencyManager\value\AmountInCurrencyVal;
use Pantagruel74\MulticurtestPrivateOperationsService\managers\CurrencyOperationManagerInterface;
use Pantagruel74\MulticurtestPrivateOperationsService\records\CurrencyOperationInAccountRequestRecInterface;
use Pantagruel74\MulticurtestPrivateOperationsService\values\AmountInCurrencyValInterface;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;
use yii\db\ActiveQueryInterface;

class CurrencyOperationInAccountManagerDb implements
    CurrencyOperationManagerInterface
{
    /**
     * @return ActiveQueryInterface
     */
    private function getQuery(): ActiveQueryInterface
    {
        return CurrencyOperationInAccountAR::find();
    }

    /**
     * @param string $accId
     * @param string $curId
     * @param int|null $afterTimestamp
     * @return CurrencyOperationInAccountRequestRecInterface[]
     */
    public function getAllOperationsAfter(
        string $accId,
        string $curId,
        ?int $afterTimestamp
    ): array {
        $q = $this->getQuery()
            ->andWhere(['accId' => $accId])
            ->andWhere(['curId' => $curId]);
        if(!is_null($afterTimestamp)) {
            $q = $q->andWhere(['>=', 'timestamp', $afterTimestamp]);
        }
        return $q->all();
    }

    /**
     * @param string $accId
     * @param AmountInCurrencyValInterface $amount
     * @return CurrencyOperationInAccountRequestRecInterface
     */
    public function createReplenishmentOperation(
        string $accId,
        AmountInCurrencyValInterface $amount
    ): CurrencyOperationInAccountRequestRecInterface {
        Assert::isAOf($amount, AmountInCurrencyVal::class);
        /* @var AmountInCurrencyVal $amount */
        Assert::true($amount->getDecades() > 0,
            "Replanishment amount should be more then zero");
        return new CurrencyOperationInAccountAR([
            'uuid' => Uuid::uuid4()->toString(),
            'amountDecades' => $amount->getDecades(),
            'amountDotPosition' => $amount->getDotPosition(),
            'curId' => $amount->getCurId(),
            'accId' => $accId,
            'timestamp' => (new \DateTime('now'))->getTimestamp(),
            'opType' => CurrencyOperationInAccountAR::OP_TYPE_REPLENISHMENT,
            'confirmed' => false,
            'declined' => false,
        ]);
    }

    /**
     * @param string $accId
     * @param AmountInCurrencyValInterface $amount
     * @return CurrencyOperationInAccountRequestRecInterface
     */
    public function createCashOperationInProcessing(
        string $accId,
        AmountInCurrencyValInterface $amount
    ): CurrencyOperationInAccountRequestRecInterface {
        Assert::isAOf($amount, AmountInCurrencyVal::class);
        /* @var AmountInCurrencyVal $amount */
        Assert::true($amount->getDecades() > 0,
            "Cashing amount should be more then zero");
        return new CurrencyOperationInAccountAR([
            'uuid' => Uuid::uuid4()->toString(),
            'amountDecades' => $amount->reverse()->getDecades(),
            'amountDotPosition' => $amount->getDotPosition(),
            'curId' => $amount->getCurId(),
            'accId' => $accId,
            'timestamp' => (new \DateTime('now'))->getTimestamp(),
            'opType' => CurrencyOperationInAccountAR::OP_TYPE_CASHING,
            'confirmed' => false,
            'declined' => false,
        ]);
    }

    /**
     * @param string $accId
     * @param AmountInCurrencyValInterface $amount
     * @return CurrencyOperationInAccountRequestRecInterface
     */
    public function createWriteOffCaseConversion(
        string $accId,
        AmountInCurrencyValInterface $amount
    ): CurrencyOperationInAccountRequestRecInterface {
        Assert::isAOf($amount, AmountInCurrencyVal::class);
        /* @var AmountInCurrencyVal $amount */
        Assert::true($amount->getDecades() > 0,
            "Write off amount should be more then zero");
        return new CurrencyOperationInAccountAR([
            'uuid' => Uuid::uuid4()->toString(),
            'amountDecades' => $amount->reverse()->getDecades(),
            'amountDotPosition' => $amount->getDotPosition(),
            'curId' => $amount->getCurId(),
            'accId' => $accId,
            'timestamp' => (new \DateTime('now'))->getTimestamp(),
            'opType' => CurrencyOperationInAccountAR::OP_TYPE_CUSTOMER_CONV_WRITE_OFF,
            'confirmed' => false,
            'declined' => false,
        ]);
    }

    /**
     * @param string $accId
     * @param AmountInCurrencyValInterface $amount
     * @return CurrencyOperationInAccountRequestRecInterface
     */
    public function createWriteInCaseConversion(
        string $accId,
        AmountInCurrencyValInterface $amount
    ): CurrencyOperationInAccountRequestRecInterface {
        Assert::isAOf($amount, AmountInCurrencyVal::class);
        /* @var AmountInCurrencyVal $amount */
        Assert::true($amount->getDecades() > 0,
            "Write in amount should be more then zero");
        return new CurrencyOperationInAccountAR([
            'uuid' => Uuid::uuid4()->toString(),
            'amountDecades' => $amount->getDecades(),
            'amountDotPosition' => $amount->getDotPosition(),
            'curId' => $amount->getCurId(),
            'accId' => $accId,
            'timestamp' => (new \DateTime('now'))->getTimestamp(),
            'opType' => CurrencyOperationInAccountAR::OP_TYPE_CUSTOMER_CONV_WRITE_IN,
            'confirmed' => false,
            'declined' => false,
        ]);
    }

    /**
     * @param string[] $operations
     * @return void
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function saveNewOperations(array $operations): void
    {
        Assert::allIsAOf($operations, CurrencyOperationInAccountAR::class);
        /* @var CurrencyOperationInAccountAR[] $operations */
        $t = \Yii::$app->db->beginTransaction();
        try {
            foreach ($operations as $op) {
                $op->saveStrictly();
            }
            $t->commit();
        } catch (\Throwable $e) {
            $t->rollBack();
            throw $e;
        }
    }

    /**
     * @param string[] $operationIds
     * @return void
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function confirmOperations(array $operationIds): void
    {
        Assert::allString($operationIds,
            "Expect parameter array of operation ids");
        $ops = CurrencyOperationInAccountAR::find()
            ->andWhere(["in", 'uuid', $operationIds])
            ->all();
        /* @var CurrencyOperationInAccountAR[] $ops */
        $t = \Yii::$app->db->beginTransaction();
        try {
            foreach ($ops as $op) {
                Assert::true($op->declined != true,
                    "Can't confirm declined operation " . $op->uuid);
                $op->confirmed = true;
                $op->saveStrictly();
            }
            $t->commit();
        } catch (\Throwable $e) {
            $t->rollBack();
            throw $e;
        }
    }

    /**
     * @param array $operationIds
     * @return void
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function declineOperations(array $operationIds): void
    {
        Assert::allString($operationIds,
            "Expect parameter array of operation ids");
        $ops = CurrencyOperationInAccountAR::find()
            ->andWhere(["in", 'uuid', $operationIds])
            ->all();
        /* @var CurrencyOperationInAccountAR[] $ops */
        $t = \Yii::$app->db->beginTransaction();
        try {
            foreach ($ops as $op) {
                Assert::true($op->confirmed != true,
                    "Can't decline confirmed operation " . $op->uuid);
                $op->declined = true;
                $op->saveStrictly();
            }
            $t->commit();
        } catch (\Throwable $e) {
            $t->rollBack();
            throw $e;
        }
    }
}