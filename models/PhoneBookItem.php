<?php

namespace app\models;

use GuzzleHttp\Client;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;


require __DIR__ . '/../vendor/autoload.php';

/**
 * This is the model class for table "phone_book_item".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone_number
 * @property string $country_code
 * @property string $timezone_name
 * @property string $inserted_on
 * @property string $updated_on
 */
class PhoneBookItem extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'phone_book_item';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['inserted_on', 'updated_on'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_on'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'phone_number'], 'required'],
            [['inserted_on', 'updated_on'], 'safe'],
            [['first_name', 'last_name', 'timezone_name'], 'string', 'max' => 50],
            [['phone_number'], 'string', 'max' => 17],

            //Phone number format: +12 220|223|224|225 xxxxxxxxx
            ['phone_number', 'match', 'pattern' => '/^(\+12) (220|223|224|225) [0-9]{9}$/', 'message' => 'Invalid phone number.(Required format : +12 220|223|224|225 xxxxxxxxx)'],
            [['country_code'], 'string', 'max' => 2],
            ['country_code', 'checkIsValidCountryCode'],
            ['timezone_name', 'checkIsValidTimezoneName']
        ];
    }

    //checks if countries available in cache, otherwise fetches from the external api and saves in cache
    //cache expire duration: 30 minutes for this example
    public function checkIsValidCountryCode()
    {
        $countries = Yii::$app->cache->getOrSet('countries', function () {
            return $this->fetchCountryCode();
        }, 60 * 30);

        if (!isset($countries[$this->country_code])) {
            $this->addError('country_code', 'Country code is not valid!');
        }
    }

    public function fetchCountryCode()
    {
        $countries = [];
        $httpClient = new Client();
        $URL_COUNTRIES_API = 'https://api.hostaway.com/countries';
        $response = $httpClient->request('GET', $URL_COUNTRIES_API);
        if ($response->getStatusCode() == 200) {
            $decodedJson = json_decode($response->getBody(), true);
            if ($decodedJson['status'] == true) {
                $countries = $decodedJson['result'];
                Yii::$app->cache->set("countries", $countries);
                return $countries;
            } else {
                Yii::error('Get countries ' . $URL_COUNTRIES_API . ' response not succes');
            }
        } else {
            Yii::error('Error in fetching countries ' . $URL_COUNTRIES_API . ': ' . $response->getStatusCode() . $response->getStatusCode());

        }
        return $countries;
    }


    //checks if timezones available in cache, otherwise fetches from the external api and saves in cache
    //cache expire duration: 30 minutes for this example
    public function checkIsValidTimezoneName()
    {
        $timezones = Yii::$app->cache->getOrSet('timezones', function () {
            return $this->fetchTimezones();
        }, 60 * 30);

        if (!isset($timezones[$this->timezone_name])) {
            $this->addError('timezone_name', 'Timezone name is not valid!');
        }
    }

    public function fetchTimezones()
    {
        $timezones = [];
        $httpClient = new Client();
        $url_timezone_api = 'https://api.hostaway.com/timezones';
        $response = $httpClient->request('GET', $url_timezone_api);
        if ($response->getStatusCode() == 200) {
            $decodedJson = json_decode($response->getBody(), true);
            if ($decodedJson['status'] == true) {
                $timezones = $decodedJson['result'];
                Yii::$app->cache->set("timezones", $timezones);
                return $timezones;
            }
        }
        return $timezones;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone_number' => 'Phone Number',
            'country_code' => 'Country Code',
            'timezone_name' => 'Timezone Name',
            'inserted_on' => 'Inserted On',
            'updated_on' => 'Updated On',
        ];
    }
}
