<?php

namespace components\startPageStrategy;

use Pantagruel74\Yii2Controllers\interfaces\StartPageStrategyInterface;
use yii\base\Component;
use yii\web\User;

class StartPageStrategy extends Component implements StartPageStrategyInterface
{

    public function getStartPageByUser(User $user): string
    {
        return '/site/index';
    }
}