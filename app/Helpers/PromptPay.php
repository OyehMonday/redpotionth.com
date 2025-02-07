<?php

namespace App\Helpers;

use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;
use App\Helpers\CRC16CCITT;


class PromptPay
{
    const ID_PAYLOAD_FORMAT = '00';
    const ID_POI_METHOD = '01';
    const ID_MERCHANT_INFORMATION_BOT = '29';
    const ID_TRANSACTION_CURRENCY = '53';
    const ID_TRANSACTION_AMOUNT = '54';
    const ID_COUNTRY_CODE = '58';
    const ID_CRC = '63';

    const PAYLOAD_FORMAT_EMV_QRCPS_MERCHANT_PRESENTED_MODE = '01';
    const POI_METHOD_STATIC = '11';
    const POI_METHOD_DYNAMIC = '12';
    const MERCHANT_INFORMATION_TEMPLATE_ID_GUID = '00';
    const BOT_ID_MERCHANT_PHONE_NUMBER = '01';
    const BOT_ID_MERCHANT_TAX_ID = '02';
    const BOT_ID_MERCHANT_EWALLET_ID = '03';
    const GUID_PROMPTPAY = 'A000000677010111';
    const TRANSACTION_CURRENCY_THB = '764';
    const COUNTRY_CODE_TH = 'TH';

    public static function generatePayload($target, $amount = null)
    {
        $target = self::sanitizeTarget($target);
        $targetType = strlen($target) >= 15 ? self::BOT_ID_MERCHANT_EWALLET_ID : (strlen($target) >= 13 ? self::BOT_ID_MERCHANT_TAX_ID : self::BOT_ID_MERCHANT_PHONE_NUMBER);

        $data = [
            self::f(self::ID_PAYLOAD_FORMAT, self::PAYLOAD_FORMAT_EMV_QRCPS_MERCHANT_PRESENTED_MODE),
            self::f(self::ID_POI_METHOD, $amount ? self::POI_METHOD_DYNAMIC : self::POI_METHOD_STATIC),
            self::f(self::ID_MERCHANT_INFORMATION_BOT, self::serialize([
                self::f(self::MERCHANT_INFORMATION_TEMPLATE_ID_GUID, self::GUID_PROMPTPAY),
                self::f($targetType, self::formatTarget($target))
            ])),
            self::f(self::ID_COUNTRY_CODE, self::COUNTRY_CODE_TH),
            self::f(self::ID_TRANSACTION_CURRENCY, self::TRANSACTION_CURRENCY_THB),
        ];

        if ($amount !== null) {
            array_push($data, self::f(self::ID_TRANSACTION_AMOUNT, self::formatAmount($amount)));
        }

        $dataToCrc = self::serialize($data) . self::ID_CRC . '04';
        array_push($data, self::f(self::ID_CRC, self::crc16($dataToCrc)));

        return self::serialize($data);
    }

    private static function f($id, $value)
    {
        return implode('', [$id, substr('00' . strlen($value), -2), $value]);
    }

    private static function serialize($xs)
    {
        return implode('', $xs);
    }

    private static function sanitizeTarget($str)
    {
        return preg_replace('/[^0-9]/', '', $str);
    }

    private static function formatTarget($target)
    {
        $str = self::sanitizeTarget($target);
        if (strlen($str) >= 13) {
            return $str;
        }

        $str = preg_replace('/^0/', '66', $str);
        $str = '0000000000000' . $str;

        return substr($str, -13);
    }

    private static function formatAmount($amount)
    {
        return number_format($amount, 2, '.', '');
    }

    private static function crc16($data)
    {
        $crc16 = new CRC16CCITT();
        $crc16->update($data);
        return strtoupper(bin2hex($crc16->finish()));
    }
}
