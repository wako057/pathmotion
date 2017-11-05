<?php
/**
 * Created by IntelliJ IDEA.
 * User: wako0
 * Date: 05/11/2017
 * Time: 14:22
 */

class JewelerFactory extends Factory{
    const MinPriceSimulation = 500;
    const MaxPriceSImulation = 1500;

    private static function getNbMinute() {

        $to_time = strtotime("2017-10-05 08:45:00");
        $from_time = strtotime("2017-10-05 16:00:00");
        $back =round(abs($to_time - $from_time) / 60,2);
        return $back;
    }

    public static function createYesterdayPriceArray()
    {
        $back = array();
        $delta = self::getNbMinute();

        for ($cpt = 0;$cpt < $delta; $cpt++) {
            $price = mt_rand() % (JewelerFactory::MaxPriceSImulation - JewelerFactory::MinPriceSimulation) + JewelerFactory::MinPriceSimulation;
            $back[$cpt] = $price;
        }
        
        return $back;
    }
}