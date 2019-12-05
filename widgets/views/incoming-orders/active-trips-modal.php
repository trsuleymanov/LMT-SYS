<div class="modal-title">Активные рейсы<button type="button" class="modal-close">×</button></div>
<div class="modal-body">
    <?php
    /*
    $aTripsDirection1 = [];
    if(isset($aActiveTrips[1])) {
        foreach ($aActiveTrips[1] as $aActiveTrip) {
            $aTripsDirection1[] = '<a href="/trip/trip-orders?trip_id=' . $aActiveTrip['trip']->id . '&trips-modal-is-open" class="active-trip' . ($aActiveTrip['bad'] == true ? ' bad' : '') . '">' . $aActiveTrip['trip']->name . '</a>';
        }
    }

    $aTripsDirection2 = [];
    if(isset($aActiveTrips[2])) {
        foreach ($aActiveTrips[2] as $aActiveTrip) {
            $aTripsDirection2[] = '<a href="/trip/trip-orders?trip_id=' . $aActiveTrip['trip']->id . '&trips-modal-is-open" class="active-trip' . ($aActiveTrip['bad'] == true ? ' bad' : '') . '">' . $aActiveTrip['trip']->name . '</a>';
        }
    }
    <div class="direction1-trips">АК <?= implode(' | ', $aTripsDirection1); ?></div>
    <div class="direction2-trips">КА <?= implode(' | ', $aTripsDirection2); ?></div>
    */
    ?>
    <div class="direction1-trips">
        <?php
        if(isset($aActiveTrips[1])) {
            foreach ($aActiveTrips[1] as $aActiveTrip) {
                ?><span class="active-trip"><a href="/trip/trip-orders?trip_id=<?= $aActiveTrip['trip']->id ?>&trips-modal-is-open" class="<?= ($aActiveTrip['bad'] == true ? 'bad' : '') ?>">АК <?= $aActiveTrip['trip']->name ?> / <?= date("d.m", $aActiveTrip['trip']->date)?> : </a><?= (count($aActiveTrip['bad_descriptions']) > 0 ? implode(', ', $aActiveTrip['bad_descriptions']) : 'продолжайте отправку') ?></span><?php
            }
        }
        ?>
    </div>
    <div class="direction2-trips">
        <?php
        if(isset($aActiveTrips[2])) {
            foreach ($aActiveTrips[2] as $aActiveTrip) {
                ?><span class="active-trip"><a href="/trip/trip-orders?trip_id=<?= $aActiveTrip['trip']->id ?>&trips-modal-is-open" class="<?= ($aActiveTrip['bad'] == true ? 'bad' : '') ?>">КА <?= $aActiveTrip['trip']->name ?> / <?= date("d.m", $aActiveTrip['trip']->date)?> : </a><?= (count($aActiveTrip['bad_descriptions']) > 0 ? implode(', ', $aActiveTrip['bad_descriptions']) : 'продолжайте отправку') ?></span><?php
            }
        }
        ?>
    </div>
</div>