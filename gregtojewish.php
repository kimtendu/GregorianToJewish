<?php

/*
Plugin Name: Gregtojewish
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Shortcode for revert gregorian to jewish date
Version: 1.0
Author: kimtendu@gmail.com
Author URI: http://levy.kim
License: A "Slug" license name e.g. GPL2
*/


class GregorianToJewish
{


    private $jewishDay;
    private $jewishMonth;
    private $jewishYear;
    private $showHeb = true;
    private $lang;

    /**
     * GregorianToJewish constructor.
     * Add shortcode
     */
    function __construct()
    {
        add_shortcode('JewishDate', array($this, 'shortcode'));
    }

    /**
     * Main shortcode function
     *
     * @param $atts
     * @return false|string
     */
    public function shortcode($atts)
    {

        $atts = shortcode_atts(array(
            'date' => date("d/m/Y"),
            'showheb' => 'true',
            'lang' => 'eng'

        ), $atts);

        if ($atts['lang'] === 'heb') {
            $atts['showheb'] = false;
        }

        $this->lang = $atts['lang'];
        $this->showHeb = $atts['showheb'] === 'true' ? true : false;

        list($this->jewishDay, $this->jewishMonth, $this->jewishYear) = explode('/', $atts['date']);

        ob_start();
        echo '<span class="GregorianToJewish">';
        echo $this->gttojewish();
        echo '</span>';

        if ($this->showHeb) {
            echo '<span class="GregorianToJewish-rtl" style="margin-left: 15px;margin-right: 15px;direction: rtl;text-align: right;">';
            $this->lang = 'heb';
            echo $this->gttojewish();
            echo '</span>';
        }

        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * Translate gregorian to jewish date
     * @return false|string
     */
    private function gttojewish()
    {

        $jdNumber = gregoriantojd($this->jewishMonth, $this->jewishDay, $this->jewishYear);
        $jewishDate = jdtojewish($jdNumber);
        if ($this->lang === 'heb') {
            $date = jdtojewish($jdNumber, true, CAL_JEWISH_ADD_GERESHAYIM);
            $date = iconv('WINDOWS-1255', 'UTF-8', $date); // convert to utf-8
            return $date;
        }

        list($jewishMonth, $jewishDay, $jewishYear) = explode('/', $jewishDate);
        $jewishMonthName = $this->getJewishMonthName($jewishMonth, $jewishYear);

        return $jewishDay . '/' . $jewishMonthName . '/' . $jewishYear;


    }


    /**
     * Check if it have 2 Adar
     * @param $year
     * @return bool
     */
    function isJewishLeapYear($year)
    {
        if ($year % 19 == 0 || $year % 19 == 3 || $year % 19 == 6 ||
            $year % 19 == 8 || $year % 19 == 11 || $year % 19 == 14 ||
            $year % 19 == 17)
            return true;
        else
            return false;
    }

    /**
     * Custom names for english and russion language
     *
     * @param $jewishMonth
     * @param $jewishYear
     * @return string
     */
    function getJewishMonthName($jewishMonth, $jewishYear)
    {
        //@TODO lang choose
        switch ($this->lang) {
            case 'ru':
                $jewishMonthNamesLeap = array("Тишрей", "Мар Хешван", "Кислев", "Тевет",
                    "Шват", "Адар", "Адар бет", "Нисан",
                    "Ияр", "Сиван", "Тамуз", "Ав", "Элул");
                $jewishMonthNamesNonLeap = array("Тишрей", "Мар Хешван", "Кислев", "Тевет",
                    "Шват", "", "Адар", "Нисан",
                    "Ияр", "Сиван", "Тамуз", "Ав", "Элул");
                break;


            default:
                $jewishMonthNamesLeap = array("Tishri", "Heshvan", "Kislev", "Tevet",
                    "Shevat", "Adar I", "Adar II", "Nisan",
                    "Iyar", "Sivan", "Tammuz", "Av", "Elul");
                $jewishMonthNamesNonLeap = array("Tishri", "Heshvan", "Kislev", "Tevet",
                    "Shevat", "", "Adar", "Nisan",
                    "Iyar", "Sivan", "Tammuz", "Av", "Elul");
                break;
        }

        if ($this->isJewishLeapYear($jewishYear))
            return $jewishMonthNamesLeap[$jewishMonth - 1];
        else
            return $jewishMonthNamesNonLeap[$jewishMonth - 1];
    }

}


new GregorianToJewish();
