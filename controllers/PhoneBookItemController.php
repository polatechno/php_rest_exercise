<?php

namespace app\controllers;


use app\resource\PhoneBookItem;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;

class PhoneBookItemController extends ActiveController
{
    public $modelClass = PhoneBookItem::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['only'] = ['create', 'update', 'delete'];
        $behaviors['authenticator']['authMethods'] = [
            HttpBearerAuth::class
        ];

        return $behaviors;

    }

}