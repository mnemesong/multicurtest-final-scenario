<?php

namespace app\controllers\web;

use Pantagruel74\Yii2Controllers\WebController;

class SiteController extends WebController
{

    /**
     * @return \string[][]
     */
    public function permissions(): array
    {
        return [
            'index' => ['*'],
            'about' => ['*'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }

    /**
     * @return string
     */
    public function actionAbout(): string
    {
        return $this->render('about');
    }
}
