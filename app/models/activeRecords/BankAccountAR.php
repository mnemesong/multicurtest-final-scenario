<?php

namespace app\models\activeRecords;

use app\models\traits\SaveStrictlyARTrait;
use Webmozart\Assert\Assert;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;

/**
 * @property string $uuid
 * @property string $mainCurId
 *
 * @property CurrencyInBankAccountAR[] $currenciesInAccount
 * @property CurrencySummaryInAccountAR[] $summariesInAccount
 */
class BankAccountAR extends ActiveRecord implements
    \Pantagruel74\MulticurtestBankManagementService\records\BankAccountRecInterface,
    \Pantagruel74\MulticurtestPrivateOperationsService\records\BankAccountRecInterface,
    \Pantagruel74\MulticurtestAccountAdministrationsService\records\BankAccountRecInterface
{
    use SaveStrictlyARTrait;

    public array $curIds = [];

    public static function tableName(): string
    {
        return "{{%bank_accounts}}";
    }

    public function rules(): array
    {
        return [
            [["uuid", "mainCurId"], "required"],
            ["mainCurId", "string", "length" => [2, 8]],
        ];
    }

    public function init()
    {
        parent::init();
        $this->curIds = array_map(
            fn(CurrencyInBankAccountAR $c) => $c->curId,
            $this->currenciesInAccount
        );
    }

    public function removeCurrencyIds(array $curIds): self
    {
        Assert::allString($curIds,
            "Currencies are should be array of strings");
        $c = clone $this;
        $c->curIds = array_filter(
            $this->curIds,
            fn(string $cid) => !in_array($cid, $curIds)
        );
        return $c;
    }

    public function changeMainCurrency(string $curId): self
    {
        Assert::inArray($curId, $this->curIds, "Currency " . $curId
            . " are not defined for account " . $this->uuid);
        $c = clone $this;
        $c->mainCurId = $curId;
        return $c;
    }

    public function getMainCurId(): string
    {
        return $this->mainCurId;
    }

    public function addCurrencies(array $curs): self
    {
        $c = clone $this;
        $c->curIds = array_unique(array_merge(
            $c->curIds,
            $curs
        ));
        return $c;
    }

    /**
     * @return string[]
     */
    public function getCurrencies(): array
    {
        return $this->curIds;
    }

    public function withMainCurrency(string $cur): self
    {
        return $this->changeMainCurrency($cur);
    }

    public function getId(): string
    {
        return $this->uuid;
    }

    public function getCurrencyIds(): array
    {
        return $this->getCurrencies();
    }

    public function addCurrencyIds(array $curIds): self
    {
        return $this->addCurrencies($curIds);
    }

    public function getSummariesInAccount(): ActiveQuery
    {
        return $this->hasMany(CurrencySummaryInAccountAR::class,
            ["accountUuid" => "uuid"]);
    }

    public function getLastSummary(string $curId): ?CurrencySummaryInAccountAR
    {
        $thisCurSummaries = array_values(array_filter(
            $this->summariesInAccount,
            fn(CurrencySummaryInAccountAR $s) => ($s->curId === $curId)
        ));
        if(empty($thisCurSummaries)) {
            return null;
        }
        return array_reduce(
            $thisCurSummaries,
            fn(CurrencySummaryInAccountAR $acc, CurrencySummaryInAccountAR $el)
                => ($acc->timestamp < $el->timestamp) ? $el : $acc,
            $thisCurSummaries[0]
        );
    }

    public function getLastSummaryTimestamp(string $curId): ?int
    {
        $lastSummary = $this->getLastSummary($curId);
        return empty($lastSummary) ? null : $lastSummary->timestamp;
    }

    public function getMainCurrency(): string
    {
        return $this->getMainCurId();
    }

    public function getCurrenciesInAccount(): ActiveQueryInterface
    {
        return $this->hasMany(CurrencyInBankAccountAR::class, ["bankAccUuid" => "uuid"]);
    }

    public function saveWithCurrency(bool $transactional): void
    {
        $saveFunc = function() {
            $this->saveStrictly();
            CurrencyInBankAccountAR::deleteAll(['bankAccUuid' => $this->uuid]);
            foreach ($this->curIds as $curId) {
                (new CurrencyInBankAccountAR([
                    'curId' => $curId,
                    'bankAccUuid' => $this->uuid,
                ]))->saveStrictly();
            }
        };
        if($transactional) {
            $t = \Yii::$app->db->beginTransaction();
            try {
                $saveFunc();
                $t->commit();
            } catch (\Throwable $e) {
                $t->rollBack();
                throw $e;
            }
        } else {
            $saveFunc();
        }
    }
}