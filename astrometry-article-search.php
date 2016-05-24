{source}
<style>
    button#calendar_img {
    	background: #373B40;
    	padding: 7px 7px 3px 8px;
    	color: ivory;
    }
</style>
<?php 
/* lets declare our initial variables to empty */
$date = '';
$desg = '';
$obs = '';
$searchResults = '';

/* get the post data */
/* we need to find out what exactly we need */ 
if($_POST['calendar']){
$date = 'SUBSTR(' . $db->quoteName('Human_Date') . ',1,10) = ' . $db->quote($_POST['calendar']);

}
if($_POST['designationSelector']){
$desg = ' AND ' . $db->quoteName('Designation'). ' = ' . $db->quote($_POST['designationSelector']);
}
if($_POST['ObservatorySelector']){
    $obs = ' AND ' . $db->quoteName('Observatory'). ' = ' . $db->quote($_POST['ObservatorySelector']);
}

/* ========= WE NEED GRAND TOTAL COUNT ============ */
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
$totalObservations = $db->loadColumn();

/* ========= WE NEED TO GATHER THE DATA ============ */
/* Start new Query */
$query = $db->getQuery(true);
/* build the query selecting only the records we need */
$query
->select($db->quoteName('Entry'))
->from($db->quoteName('#__aso_astrometry'))
->where($date.$desg.$obs);
/* ok, thats everything, lets get it loaded */
$db->setQuery($query);
$db->execute();
$num_rows = $db->getNumRows();
$result_rows = $db->loadRowList();
$bear = $db->loadObjectList();

if($num_rows > 0){
    foreach($result_rows as $key=>$row){
    	$searchResults .= $row[0].'<br>';
    }
}
echo '<pre>'. $searchResults .'</pre>';
exit;



/* ========= WE NEED YEARS TOTAL COUNT ============ */
/* initialize the query variable */
$query = $db->getQuery(true);

/* lets find out how many records we have
 * so first lets choose what we need to find */
$query
->select('COUNT(' . $db->quoteName('Human_Date') . ' ) ')
->from($db->quoteName('#__aso_astrometry'))
->where($date);

/* ok, thats everything, lets get it loaded */
$db->setQuery($query);

/* ok, go fetch the data */
$YTD = $db->loadResult();

/* Now that we have our data we need to break it down into a variable to display it */

?>
<div class="astrometry well">
    <div class="panel panel-default">
    	<div class="panel-heading">
    		<h1 class="panel-title">Astrometric Database Search Results</h1>
    	</div>
    	<div class="panel-body panel-info">
    		This data is formatted in the MPC format using the official packed designation as submitted to Harvard from ASO each morning.
    	</div>
    	<div class="panel-footer">
    		<form action="/index.php?option=com_content&amp;view=article&amp;id=73" role="form" id="astrometry-article-search-form" method="post" name="astrometry-article-search-form">
    			<fieldset>
    				<!-- Display Results -->
    				<div class="well well-sm col-xs-12 col-sm-3" style="float: none; margin-left: auto; margin-right: auto;">
    					<div class="form-cluster">
    						<!-- Date selector -->
    						<div class="form-group form-group-sm">
    							<label class="control-label" for="observations">Observations</label>
    							<?php echo $searchResults;?>
    						</div>
    						
    					</div>
    					<!-- Button (Double) -->
    					<div class="form-group form-group-sm" style="text-align: center;"><label class="control-label" for="button-submit"></label>
    						<div style="text-align: center;"><button class="btn btn-warning" id="button-submit" name="button-submit" value="Submit"><i class="fa fa-hand-o-left"></i>Return</button></div>
    					</div>
    					<h6 style="text-align: center;">The displayed format is exactly to the <a href="http://cfa-www.harvard.edu/iau/info/ObsExamples.html" target="_blank">Harvard Minor Planet specifications</a> and will allow easy import into most orbital calculation programs.</h6>
    					<h6 style="margin: 0; text-align: center;"><i><?php echo $totalObservations; ?> Observations recorded</i></h6>
    				</div>
    			</fieldset>
    		</form>
    	</div>
    </div>
</div>
{/source}