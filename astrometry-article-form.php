{source}
<style>
button#calendar_img {
background: #373B40;
padding: 7px 7px 3px 8px;
color: ivory;
}
</style>
<?php
/* lets find out how many records we have */
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query
->select('id')
->from($db->quoteName('#__aso_astrometry'))
->order($db->quoteName('id') . ' DESC')
->setLimit('1');
$db->setQuery($query);
$result = $db->loadResult();
?>
<div class="astrometry well">
<div class="panel panel-default">
<div class="panel-heading">
<h1 class="panel-title">Astrometric Database of Comets and Asteroids</h1>
</div>
<div class="panel-body panel-info">
ASO Astrometry data is available online by using the following form.<br /> Use the fields below to filter your selection from our database and then press the Submit button.<br /> This data is formatted in the MPC format using the official packed designation as submitted to Harvard from ASO each morning.
</div>
<div class="panel-footer">
<form action="/index.php?option=com_content&amp;view=article&amp;id=74" role="form" id="astrometry-article-search-form" method="post" name="astrometry-article-search-form">
<fieldset>
<!-- Display Results -->
<div class="well well-sm col-xs-12 col-sm-3" style="float: none; margin-left: auto; margin-right: auto;">
<div class="form-cluster">
<!-- Date selector -->
<div class="form-group form-group-sm">
<label class="control-label" for="calendar">Observation Date</label>
<?php echo JHTML::calendar(date("Y-m-d"), 'calendar', 'calendar', '%Y-%m-%d', array('size' => '10', 'maxlength' => '10', 'class' => ' validate[\'required\']',)); ?>
</div>
<!-- Designator Selector -->
<div class="form-group form-group-sm"><label class="control-label" for="designationSelector">Object</label>
<div><input class="form-control input-sm" id="designationSelector" name="designationSelector" type="text" /></div>
</div>
<!-- Observatory Selector -->
<div class="form-group form-group-sm"><label class="control-label" for="ObservatorySelector">Observatory</label>
<div><input class="form-control input-sm" id="ObservatorySelector" name="ObservatorySelector" type="text" placeholder="Example input"/></div>
</div>
</div>
<!-- Button (Double) -->
<div class="form-group form-group-sm" style="text-align: center;"><label class="control-label" for="button-submit"></label>
<div style="text-align: center;"><button class="btn btn-success" id="button-submit" name="button-submit" value="Submit">Submit</button> <button class="btn btn-danger" id="button-reset" name="button-reset" type="reset" value="Reset">Reset</button></div>
</div>
<h6 style="text-align: center;">The displayed format is exactly to the <a href="http://cfa-www.harvard.edu/iau/info/ObsExamples.html" target="_blank">Harvard Minor Planet specifications</a> and will allow easy import into most orbital calculation programs.</h6>
<h6 style="margin: 0; text-align: center;"><i><?php echo $result; ?> Observations recorded</i></h6>
</div>
</fieldset>
</form>
</div>
</div>
</div>
{/source}