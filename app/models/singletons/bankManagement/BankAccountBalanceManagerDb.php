<?php

namespace app\models\singletons\bankManagement;

use app\models\activeRecords\CurrencyOperationInAccountAR;
use app\models\activeRecords\DescriptionInCurrencyOperationInAccountAR;
use Pantagruel74\MulticurtestBankManagementService\managers\BankAccountBalanceManagerInterface;
use Pantagruel74\MulticurtestBankManagementService\values\AmountInCurrencyValInterface;
use Pantagruel74\MulticurtestCurrencyManager\value\AmountInCurrencyVal;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class BankAccountBalanceManagerDb implements BankAccountBalanceManagerInterface
{
    /**
     * @param string $accId
     * @param string $curId
     * @return AmountInCurrencyValInterface
     */
    public function calcFrozenBalanceInCurrencyInAccount(
        string $accId,
        string $curId
    ): AmountInCurrencyValInterface {
        $rec = \Yii::$app->privateOperationsService
            ->getFrozenBalanceInCurrencyAccount($accId, $curId);
        Assert::isAOf($rec, AmountInCurrencyVal::class);
        /* @var AmountInCurrencyVal $rec */
        return $rec;
    }

    /**
     * @param string $accountId
     * @param AmountInCurrencyValInterface $amountInCurrencyVal
     * @param string $description
     * @return void
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function addAndConfirmBalanceCorrectionOperation(
        string $accountId,
        AmountInCurrencyValInterface $amountInCurrencyVal,
        string $description
    ): void {
        $t = \Yii::$app->db->beginTransaction();
        try {
            $uuid =Uuid::uuid4()->toString();
            (new CurrencyOperationInAccountAR([
                'uuid' => $uuid,
                'amountDecades' => $amountInCurrencyVal->getDecades(),
                'amountDotPosition' => $amountInCurrencyVal->getDotPosition(),
                'curId' => $amountInCurrencyVal->getCurId(),
                'accId' => $accountId,
                'timestamp' => (new \DateTime('now'))->getTimestamp(),
                'opType' => CurrencyOperationInAccountAR::OP_TYPE_BANK_CONV_OPERATION,
                'confirmed' => true,
                'declined' => false,
            ]))->saveStrictly();
            (new DescriptionInCurrencyOperationInAccountAR([
                'curOpInAccUuid' => $uuid,
                'desc' => $description
            ]))->saveStrictly();
            $t->commit();
        } catch (\Throwable $e) {
            $t->rollBack();
            throw $e;
        }
    }

    /**
     * @param string $accId
     * @param string $curId
     * @param int|null $afterTimestamp
     * @return void
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function declineAllOperationsInProcessAfter(
        string $accId,
        string $curId,
        ?int $afterTimestamp
    ): void {
        $opsQ = CurrencyOperationInAccountAR::find()
            ->andWhere(['curId' => $curId])
            ->andWhere(['accId' => $accId])
            ->andWhere(['confirmed' => false])
            ->andWhere(['declined' => false]);
        if(!is_null($afterTimestamp)) {
            $opsQ = $opsQ
                ->andWhere([">=", 'timestamp', $afterTimestamp]);
        }
        $operations = array_map(
            fn(CurrencyOperationInAccountAR $op) => $op->asDeclined(),
            $opsQ->all()
        );
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
}