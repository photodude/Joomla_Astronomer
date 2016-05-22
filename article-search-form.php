<form action="/index.php?option=com_content&amp;view=article&amp;id=74" class="form-horizontal" role="form" id="astrometry-article-search-form" method="post" name="astrometry-article-search-form">
	<fieldset><!-- Form Name --> <legend>Astrometric Database of Comets and Asteroids</legend> 
		<!-- Display Results -->
		<div class="well well-sm">
			<h4 class="alert alert-info" style="text-align: center; color: #000;">ASO Astrometry data is available online by using the following form.<br /> Use the fields below to filter your selection from our database and then press the Submit button.<br /> This data is formatted in the MPC format using the official packed designation as submitted to Harvard from ASO each morning.</h4>
			<div class="row">
				<!-- Date selector -->
			<div class="form-group form-group-sm col-sm-6"><label class="control-label" for="searchDate">Observation Date</label>
				<div class="form-control input-sm">
					{source}
 <?php
 echo JHTML::calendar(date("Y-m-d"), 'searchDate', 'searchDate', '%Y-%m-%d', array('size' => '10', 'maxlength' => '10', 'class' => 'calendar' ));
					?>
					{/source}
				</div>
			</div>
			<!-- Designator Selector -->
			<div class="form-group form-group-sm col-sm-3"><label class="control-label" for="designationSelector">Object</label>
				<div><input class="form-control input-sm" id="designationSelector" name="designationSelector" type="text" /></div>
			</div>
			<!-- Observatory Selector -->
			<div class="form-group form-group-sm col-sm-3"><label class="control-label" for="ObservatorySelector">Observatory</label>
				<div><input class="form-control input-sm" id="ObservatorySelector" name="ObservatorySelector" type="text" /></div>
			</div>
			</div>
			<!-- Button (Double) -->
			<div class="form-group form-group-sm col-sm-12"><label class="control-label" for="button-submit"></label>
				<div style="text-align: center;"><button class="btn btn-success" id="button-submit" name="button-submit" value="Submit">Submit</button></div>
			</div>
		</div>
	</fieldset>
	<h6 style="text-align: center;">The displayed format is exactly to the <a href="http://cfa-www.harvard.edu/iau/info/ObsExamples.html" target="_blank">Harvard Minor Planet specifications</a> and will allow easy import into most orbital calculation programs.</h6>
</form>