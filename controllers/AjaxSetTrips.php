<div class="col-tobus-center">

        <?php
        foreach ($aDirections as $key => $aDirection)
        {
            $direction = $aDirection['direction'];

            if(count($aDirection['trips']) > 0)
            {
                ?>
                <div
                    class="<?= (($key == count($aDirections) - 1) ? 'col-tobus-center-right' : 'col-tobus-center-left') ?>">

                    <p class="sh_route"><?= $direction->sh_name ?></p>
                    <table class="info-list <?= (($key == count($aDirections) - 1) ? 'info-list-right' : '') ?>">
                        <tbody>
                        <?php
                        foreach ($aDirection['trips'] as $trip) { ?>
				<?php 
					$tr_dr_info = $trip->getTransportDriverInfo();
					$names_to_show = [];
					$vacant_places_count = 0;
					foreach($tr_dr_info as $tr_dr){
						$vacant_places_count += $tr_dr['transport']['places_count'];
						$names_to_show[] = ['to_show'=>$tr_dr['transport']['to_show'], 'id'=>$tr_dr['id']];
					}
					
					
					
				?>
                            <tr class="752">
				
                                <td rowspan="3" class="span1"></td>
                                <td class="span2 points"><?= $trip->start_time ?></td>
				<td><input type="checkbox" class="merged" value="<?=$trip->id ?>" trip_name="<?=$trip->name ?>"></td>
                                <td rowspan="3" class="reis_name span5">
                                    <div class="reis_name_content">
                                        <a href="#" class="trip_detail_link" trip-id="<?= $trip->id ?>"><?= $trip->name ?></a>
                                        <span class="add_transport_plus" trip-id="<?= $trip->id ?>"><i
                                                class="glyphicon glyphicon-plus-sign"></i></span>
                                    </div>
                                </td>
                                <td rowspan="3" class="span2">
                                    <span class="many_orders"><?= count($trip->orders) ?>
                                        /<?=$vacant_places_count ?></span>
                                </td>
                                <td rowspan="3" class="span2">
					<?php 
						foreach($names_to_show as $tr_name){
							$name = $tr_name['to_show'];
							$id = $tr_name['id'];
							$trip_id = $trip->id;
							echo "<div style=\"color:gray;font-size:small;\" trip_transport_id=\"$id\" trip_id=\"$trip_id\" class=\"trip_transport\">$name</div>";
						}	
					?>
				</td>
                            </tr>
                            <tr class="752">
                                <td class="points"><?= $trip->mid_time ?></td>
                            </tr>
                            <tr class="752">
                                <td class="points"><?= $trip->end_time ?></td>
                            </tr>
                            <tr class="empty_tr 752"></tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
        <?php
            }
        }
        ?>
    </div>