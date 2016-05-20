<form id="astrometry-article-submit-form" class="form-horizontal" name="astrometry-article-submit-form" method="post" action="/astrometry-article-display">
	<fieldset>
		<!-- Form Name -->
		<legend>Astrometry Observation Reporting Form</legend>
		<!-- Display Results -->
		<div class="well well-sm">
			<div>
				<h4 class="alert alert-info" style="text-align: center; color: #000;">
					Data pasted into the space below MUST be in the format specified by the <a href="http://cfa-www.harvard.edu/iau/info/ObsExamples.html" target="_blank">Harvard Minor Planet Center</a>.
				</h4>
			</div>
			<!-- Textarea -->
			<div class="form-group well well-sm">
				<label class="col-md-2 control-label" for="entrybox">Observations</label>
				<div class="col-md-8"><textarea class="form-control" style="height: 400px;" id="entrybox" name="entrybox">
					
					</textarea>
				</div>
			</div>
			<!-- Button (Double) -->
			<div class="form-group">
				<label class="control-label" for="button-submit"></label>
				<div style="text-align: center;">
					<button id="button-submit" name="button-submit" class="btn btn-success" value="Submit">Submit</button>
					<button id="button-reset" name="button-reset" class="btn btn-danger" value="Reset">Reset</button>
				</div>
			</div>
	</fieldset>
</form>