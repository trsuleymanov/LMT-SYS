<?php
use app\models\Transport;
use app\components\Helper;

//echo "aDates:<pre>"; print_r($aDates); echo "</pre>";
//echo "aTransportsEfficiencyData:<pre>"; print_r($aTransportsEfficiencyData); echo "</pre>";
?>

<table id="efficiency-table" width="100%">
    <tr>
        <th>Т/с</th>
        <th>К.</th>
        <th><?= Helper::getWeekDay($aDates[0]).', '. date('d.m.Y', $aDates[0]) ?></th>
        <th><?= Helper::getWeekDay($aDates[1]).', '. date('d.m.Y', $aDates[1]) ?></th>
        <th><?= Helper::getWeekDay($aDates[2]).', '. date('d.m.Y', $aDates[2]) ?></th>
        <th><?= Helper::getWeekDay($aDates[3]).', '. date('d.m.Y', $aDates[3]) ?></th>
        <th><?= Helper::getWeekDay($aDates[4]).', '. date('d.m.Y', $aDates[4]) ?></th>
        <th><?= Helper::getWeekDay($aDates[5]).', '. date('d.m.Y', $aDates[5]) ?></th>
        <th><?= Helper::getWeekDay($aDates[6]).', '. date('d.m.Y', $aDates[6]) ?></th>
        <th><?= Helper::getWeekDay($aDates[7]).', '. date('d.m.Y', $aDates[7]) ?></th>
        <th><?= Helper::getWeekDay($aDates[8]).', '. date('d.m.Y', $aDates[8]) ?></th>
        <th><?= Helper::getWeekDay($aDates[9]).', '. date('d.m.Y', $aDates[9]) ?></th>
        <th><?= Helper::getWeekDay($aDates[10]).', '. date('d.m.Y', $aDates[10]) ?></th>
    </tr>
    <?php foreach($aTransportsEfficiencyData as $aTransportData) { ?>
        <tr>
            <td><?= $aTransportData['transport'] ?></td>
            <td><?= $aTransportData['efficiency'] ?>, <br /><?= $aTransportData['average_total_proceeds'] ?>, <br /><?= round($aTransportData['average_30_formula_result'], 2) ?></td>
            <td><?= Transport::getEfficiencyDayCell($aTransportData, $aDates[0]) ?></td>
            <td><?= Transport::getEfficiencyDayCell($aTransportData, $aDates[1]) ?></td>
            <td><?= Transport::getEfficiencyDayCell($aTransportData, $aDates[2]) ?></td>
            <td><?= Transport::getEfficiencyDayCell($aTransportData, $aDates[3]) ?></td>
            <td><?= Transport::getEfficiencyDayCell($aTransportData, $aDates[4]) ?></td>
            <td><?= Transport::getEfficiencyDayCell($aTransportData, $aDates[5]) ?></td>
            <td><?= Transport::getEfficiencyDayCell($aTransportData, $aDates[6]) ?></td>
            <td><?= Transport::getEfficiencyDayCell($aTransportData, $aDates[7]) ?></td>
            <td><?= Transport::getEfficiencyDayCell($aTransportData, $aDates[8]) ?></td>
            <td><?= Transport::getEfficiencyDayCell($aTransportData, $aDates[9]) ?></td>
            <td><?= Transport::getEfficiencyDayCell($aTransportData, $aDates[10]) ?></td>
        </tr>
    <?php } ?>
</table>