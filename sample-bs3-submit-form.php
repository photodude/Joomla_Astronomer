<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
		<!--
		<script>
			jQuery(function($) { // onDomReady

    // reset handler that clears the form
    $('form[name="myform"] input:reset').click(function () {
        $('form[name="myform"]')
            .find(':radio, :checkbox').removeAttr('checked').end()
            .find('textarea, :text, select').val('')

        return false;
    });

});
</script>
-->
		<form id="astrometry-article-submit-form" class="form-horizontal" name="astrometry-article-submit-form" method="post" action="">

			<fieldset>
				<!-- Form Name -->
				<legend>Astrometry Observation Reporting Form</legend>

				<!-- Textarea -->
				<div class="form-group well well-sm">
					<label class="col-md-2 control-label" for="entrybox">Observations</label>
					<div class="col-md-8">
						<textarea class="form-control" id="entrybox" name="entrybox"></textarea>
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
    </body>
</html>
