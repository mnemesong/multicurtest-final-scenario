<?php

namespace app\models\traits;

use Webmozart\Assert\Assert;
use yii\db\ActiveRecord;

trait SaveStrictlyARTrait
{
    public function saveStrictly(): void
    {
        /* @var ActiveRecord $this */
        $saveResult = $this->save();
        Assert::true($saveResult, "Invalid save of record "
            . get_class($this) . ": "
            . implode(". ", $this->getFirstErrors()));
    }
}