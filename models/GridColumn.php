<?php

namespace app\models;

/*
 * Класс для использования в футере колонки в GridView::widget
 */
class GridColumn
{
    /*
     * Расчет итоговой цены в таблице
     */
    public static function pageTotal($provider, $fieldName)
    {
        $total = 0;
        foreach($provider as $item){

            if(in_array($fieldName, ['waybill_state', 'values_fixed_state', 'gsm'])) {
                if ($item[$fieldName] == 'accepted') {
                    $total += 1;
                }
            }elseif($fieldName == 'klpto') {
                if (!empty($item[$fieldName]) && $item[$fieldName] != 'none') {
                    $total += 1;
                }
            }else {
                $total += $item[$fieldName];
            }
        }
        return $total;
    }
}