<?php

namespace app;

use League\Csv\Reader;


class Login
{
    const INVALID_PASSWORD_EXIT_CODE = 0;
    const DOES_NOT_EXIST_EXIT_CODE = -1;
    const VALID_USER_EXIT_CODE = 1;

    private static $userData;

    private static function getGeoIpData($ip = '78.154.128.159')
    {
        $xml = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=" . $ip);
        return [
            'city' => $xml->geoplugin_city->__toString(),
            'country' => $xml->geoplugin_countryName->__toString(),
            'in_eu' => $xml->geoplugin_inEU->__toString(),
            'latitude' => $xml->geoplugin_latitude->__toString(),
            'longitude' => $xml->geoplugin_longitude->__toString(),
            'currency_code' => $xml->geoplugin_currencyCode->__toString(),
            'currency_converter' => $xml->geoplugin_currencyConverter->__toString()
        ];
    }

    public static function getData()
    {
        return self::$userData;
    }

    private static function sanitizeData(&$data)
    {
        unset($data['exit_code'], $data['offset']);
    }

    private static function getMessage($code)
    {
        switch ($code) {
            case self::INVALID_PASSWORD_EXIT_CODE:
                return "<div class=\"alert alert-danger\" role=\"alert\">
                          Invalid password!
                        </div>";
            case self::VALID_USER_EXIT_CODE:
                return "<div class=\"alert alert-success\" role=\"alert\">
                          Successful login!
                        </div>";
            case self::DOES_NOT_EXIST_EXIT_CODE:
                return "<div class=\"alert alert-danger\" role=\"alert\">
                         This user doesn't exist!
                        </div>";
            default:
                return "";
        }
    }

    public static function login($username, $password)
    {
        if (!isset($username, $password)) {
            return self::getMessage(self::INVALID_PASSWORD_EXIT_CODE);
        }

        if (isset($username, $password) && (!$username || !$password)) {
            return self::getMessage(self::INVALID_PASSWORD_EXIT_CODE);
        }

        $updatableData = array_merge([
            'http_cookie' => $_SERVER['HTTP_COOKIE'],
            'http_user_agent' => str_replace(',', '', $_SERVER['HTTP_USER_AGENT']),
            'remote_addr' => $_SERVER['REMOTE_ADDR'],
            'latest_request_time' => date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME'])
        ], self::getGeoIpData());

        self::$userData = array_merge(self::getUser($username, $password), $updatableData);
        if (self::$userData['exit_code'] === self::VALID_USER_EXIT_CODE) {
            if (self::$userData['invalid_password_attempts'] != 0) {
                self::$userData['invalid_password_attempts'] = 0;
            }
            self::updateUser(self::$userData);
            return self::getMessage(self::VALID_USER_EXIT_CODE);
        } elseif (self::$userData['exit_code'] === self::INVALID_PASSWORD_EXIT_CODE) {
            self::$userData['invalid_password_attempts']++;
            self::updateUser(self::$userData);
            return self::getMessage(self::INVALID_PASSWORD_EXIT_CODE);
        } else {
            return self::getMessage(self::DOES_NOT_EXIST_EXIT_CODE);
        }
    }

    private static function updateUser($newData)
    {
        $filename = ROOT . '/users/user.csv';
        $line_i_am_looking_for = $currentUser['offset'] + 1;
        $lines = file($filename, FILE_IGNORE_NEW_LINES);
        self::sanitizeData($newData);
        $lines[$line_i_am_looking_for] = implode(',', $newData);
        file_put_contents($filename, implode("\n", $lines));
    }

    private static function getUser($username, $password)
    {
        $reader = Reader::createFromPath(ROOT . '/users/user.csv', 'r');
        $reader->setHeaderOffset(0);
        $records = $reader->getRecords();
        foreach ($records as $offset => $record) {

            // Valid password
            if ($record['username'] == $username && ($record['password'] == $password)) {
                return array_merge($record, ['exit_code' => self::VALID_USER_EXIT_CODE, 'offset' => $offset]);
            } elseif ($record['username'] == $username) {
                return array_merge($record, ['exit_code' => self::INVALID_PASSWORD_EXIT_CODE, 'offset' => $offset]);
            }
        }
        return ['exit_code' => self::DOES_NOT_EXIST_EXIT_CODE];
    }

}