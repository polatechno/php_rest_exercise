<?php

namespace app\resource;

class PhoneBookItem extends \app\models\PhoneBookItem
{
    public function fields()
    {
        return ['id', 'first_name', 'last_name', 'phone_number', 'country_code', 'timezone_name'];
    }

    public function extraFields()
    {
        return [  'inserted_on', 'updated_on'];
    }

}