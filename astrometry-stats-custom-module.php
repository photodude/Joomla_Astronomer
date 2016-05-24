{source}
<?php function colorize($period,$count){
    if($period == 'Day'){
    	if($count <= '10'){
    		$count = 'danger';
    	}elseif ($count >= '10' && $count <='50') {
    		$count = 'warning';
    	}elseif ($count > '51' && $count <= '100') {
    		$count = 'info';
    	}elseif ($count > '100') {
    		$count = 'success';
    	}
    }elseif($period == 'Month'){
    			if($count <= '100'){
    		$count = 'danger';
    	}elseif ($count >= '100' && $count <='500') {
    		$count = 'warning';
    	}elseif ($count > '500' && $count < '1000') {
    		$count = 'info';
    	}elseif ($count >= '1000') {
    		$count = 'success';
    	}
    }elseif($period == 'Year'){
    			if($count <= '1000'){
    		$count = 'danger';
    	}elseif ($count >= '1000' && $count <='3000') {
    		$count = 'warning';
    	}elseif ($count > '3000' && $count <= '5000') {
    		$count = 'info';
    	}elseif ($count > '5000') {
    		$count = 'success';
    	}
}
return $count;
    	}

?>
<div class="panel panel-info">
    <div class="panel-heading"><h4>Astrometry Stats<h4></div>
    			<div class="panel panel-body" style="text-align: left">
    				<?php
    				/* we need to connect to the db */
    				$db = JFactory::getDbo();

    				/* initialize the query variable */
    				$query = $db->getQuery(true);

    				/* lets find out how many records we have
    				 * so first lets choose what we need to find */
    				$query
    				->select('id')
    				->from($db->quoteName('#__aso_astrometry'))
    				->order($db->quoteName('id') . ' DESC')
    				->setLimit('1');

    				/* ok, thats everything, lets get it loaded */
    				$db->setQuery($query);

    				/* ok, go fetch the data */
    				$totalObservations = $db->loadResult();


    				/* ok, now lets do the same for observatories */
    				/* initialize the query variable */
    				$query = $db->getQuery(true);

    				/* lets find out how many observatories we have
    				 * so first lets choose what we need to find */
    				$query
    				->select('DISTINCT ' . $db->quoteName('Observatory'))
    				->from($db->quoteName('#__aso_astrometry'))
    				->order($db->quoteName('Observatory') . ' DESC');


    				/* ok, thats everything, lets get it loaded */
    				$db->setQuery($query);

    				/* ok, go fetch the data */
    				$knownObservatories = $db->loadColumn();
    				?>
    				<h6 style="margin: 0;"><span><?php echo '<i class="badge label-info">' . $totalObservations . '</i>'; ?> <i><b>Observations recorded</b></i></span></h6>
    				<br>
    				<h6 style="margin: 0;"><b>Known Observatories</b><br>
    					<?php
    					foreach ($knownObservatories as $ko) {
    						echo ' <i class="badge label-default">' . $ko . "</i> ";
    					}
    					?>
    				</h6>

    				<!-- ok, now get YTD -->
    				<?php
    				/* initialize the query variable */
    				$query = $db->getQuery(true);

    				/* lets find out how many records we have
    				 * so first lets choose what we need to find */
    				$query
    				->select('COUNT('. $db->quote('Year').')')
    				->from($db->quoteName('#__aso_astrometry'))
    				->where( $db->quoteName('Year') . ' = ' . $db->quote(date('Y')));

    				/* ok, thats everything, lets get it loaded */
    				$db->setQuery($query);

    				/* ok, go fetch the data */
    				$YTD = $db->loadResult();
    				/* get year + month */

    				$query
    				->clear('where')
->clear('select')
    				->select('COUNT('. $db->quote('Human_Date').')')

    				->where('SUBSTRING(' . $db->quoteName('Human_Date') . ',1,7)' . ' = ' . $db->quote(date('Y-m')));
    				/* ok, thats everything, lets get it loaded */
    				$db->setQuery($query);
    				/* ok, go fetch the data */
    				$MTD = $db->loadResult();

    				$query
    				->clear('where')
    				->where('SUBSTRING(' . $db->quoteName('Human_Date') . ',1,10)' . ' = ' . $db->quote(date('Y-m-d')));
    				/* ok, thats everything, lets get it loaded */
    				$db->setQuery($query);
    				/* ok, go fetch the data */
    				$DTD = $db->loadResult();
    				?>
    				<br>
    				<h6 style="margin: 0;"><b>Observations so far this year</b></h6>
    				<ul class="list-inline">
    					<?php echo '<li><i class="badge badge-' . colorize('Year',$YTD) . '">' . $YTD; ?><sup> Year</sup></i></li>
    					<?php echo '<li><i class="badge badge-' . colorize('Month',$MTD) . '">' . $MTD; ?><sup> Month</sup></i></li>
    					<?php echo '<li><i class="badge badge-' . colorize('Day',$DTD) . '">' . $DTD; ?><sup> Day</sup></i></li>
    				</ul>
    			</div>
    			</div>
{/source}