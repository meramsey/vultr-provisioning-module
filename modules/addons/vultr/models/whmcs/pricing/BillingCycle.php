<?php
namespace MGModule\vultr\models\whmcs\pricing;

/**
 * Description of BillingCycle
 */
class BillingCycle
{
    //Product and Addons
    const FREE = 'free';
    const ONE_TIME = 'onetime';
    const MONTHLY = 'monthly';
    const QUARTERLY = 'quarterly';
    const SEMI_ANNUALLY = 'semiannually';
    const ANNUALLY = 'annually';
    const BIENNIALLY = 'biennially';
    const TRIENNIALLY = 'triennially';

    //Domains
    const YEAR = 'YEAR';
    const YEARS_2 = 'YEARS_2';
    const YEARS_3 = 'YEARS_3';
    const YEARS_4 = 'YEARS_4';
    const YEARS_5 = 'YEARS_5';
    const YEARS_6 = 'YEARS_6';
    const YEARS_7 = 'YEARS_7';
    const YEARS_8 = 'YEARS_8';
    const YEARS_9 = 'YEARS_9';
    const YEARS_10 = 'YEARS_10';

    public static function convertPeriodToString($period)
    {
        if ($period == 1) {
            return 'YEAR';
        }

        if ($period > 1 && $period <= 10) {
            return 'YEARS_' . $period;
        }

        throw new \MGModule\vultr\mgLibs\exceptions\System('Inalid period: ' . $period);
    }
}
