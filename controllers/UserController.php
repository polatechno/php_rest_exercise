<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 17.11.2019
 * Time: 1:45
 */

namespace app\controllers;


use app\models\LoginForm;
use app\models\RegistrationForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['register', 'login' ],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['?'],
                ],
            ]
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'register' => ['POST'],
                'login' => ['POST']
            ],
        ];

        return $behaviors;
    }

    public function actionLogin()
    {
        $model = \Yii::createObject(LoginForm::className());
        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $model->login()) {
            return ['access_token' => Yii::$app->user->identity->getAuthKey()];
        } else {
            $model->validate();
            return $model;
        }
    }


    public function actionRegister()
    {

        $model = \Yii::createObject(RegistrationForm::className());
        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $model->register()) {
            return ['success' => true];
        } else {
            $model->validate();
            return $model;
        }
    }


}
