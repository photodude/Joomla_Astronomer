{source}
<?php
/* declare variables */
$yep = '';
$nope = '';
date_default_timezone_set('UTC');
$i = 0;
$foundCount = 0;
$badCount = 0;
$errors = '';
$uid = '424'; // Doc's user id, we'll need to pull this for each observer post shown

/* we need the params from the user profile plugin */
$plugin = JPluginHelper::getPlugin('user', 'astronomerprofile');
if ($plugin) {
//get plugin params
    $pluginParams = new JRegistry($plugin->params);
    $header = $pluginParams->get('profile-require_header');
}

/* we need some functions */

function decimal_to_time($time, $precision = '5') {
    $DoW = sprintf("%02d", $time);
    $time = $time - $DoW;
    $time = bcmul($time, '24', $precision);
    $hours = sprintf("%02d", $time);
    $time = bcmul($time - $hours, '60', $precision);
    $minutes = sprintf("%02d", $time);
    $seconds = bcmul($time - $minutes, '60', $precision);

    return $DoW . ' ' . $hours . ':' . $minutes . ':' . $seconds;
}

/* NOW LETS CODE */

/* lets make sure last character is a EOL */
$data = htmlspecialchars($_POST["entrybox"]) . PHP_EOL;
/* convert line endings to *nix format and strip empty lines */
$data = preg_replace('/\n$/', '', preg_replace('/^\n/', '', preg_replace('/[\r\n]+/', "\n", $data)));

/* convert to an array now. */
$data = explode("\n", $data);

/* process array into entries */
$valid = array();
$invalid = array();
foreach ($data as $line) {

    /* lets start checking line integrity */
    if (strlen($line) <> '80') {
    	$invalid[] = $line;
    	$badCount++;
    	$nope .= 'Line ' . $i . ' <span class="glyphicon glyphicon-hand-right"></span>' . $line . '<span class="glyphicon glyphicon-hand-left"></span> has <span class="badge">' . strlen($line) . '</span> characters' . PHP_EOL;
    }
    /* is it a space? */
    elseif ($line[12] <> "*" && $line[12] <> " ") {
    	$invalid[] = $line;
    	$badCount++;
    	$nope .= 'Line ' . $i . ' <span class="glyphicon glyphicon-hand-right"></span>' . $line . '<span class="glyphicon glyphicon-hand-left"></span> invalid column 13 must be space or * format' . PHP_EOL;
    }
    /* does it have a space in cols 57-65? */
    elseif ( strcmp( substr( $line, 56, 9 ),"         ") ){
    	$invalid[] = $line;
    	$badCount++;
    	$nope .= 'Line ' . $i . ' <span class="glyphicon glyphicon-hand-right"></span>' . $line . '<span class="glyphicon glyphicon-hand-left"></span> invalid, column 57-65 must be blank' . PHP_EOL;
    }
    	/* does it have a space in cols 72-77? */
    elseif ( strcmp( substr( $line, 71, 6 ),"      ") ){
    	$invalid[] = $line;
    	$badCount++;
    	$nope .= 'Line ' . $i . ' <span class="glyphicon glyphicon-hand-right"></span>' . $line . '<span class="glyphicon glyphicon-hand-left"></span> invalid, column 72-77 must be blank' . PHP_EOL;
    }
    /* ok, it must be ok then */
    else {
    	$valid[] = $line;
    	$yep .= $line . PHP_EOL;
    	$foundCount++;

    	/* make sure we have a 4 digit year */
    	$year = substr($line, 15, 4);

    	/* make sure we have a 2 digit month */
    	$month = sprintf("%02d", substr($line, 20, 2));

    	/* get designation & strip the spaces, we dont' need / want them for searches */
    	$desg = trim(substr($line, 0, 14), " ");

    	/* we need 8 chars for DD.nnnnn ( n = decimal hours ) */
    	$day = substr($line, 23, 8);

    	/* only get the magnitude itself */
    	$mag = substr($line, 65, 4);

    	/* get the observatory identifer */
    	$observatory = substr($line, 77, 3);

    	/* lets get the day date & time in "standard" format */
    	$dayTime = decimal_to_time($day);

    	/* date in system useable format, YYYY-MM-DD HH:MM:SS.sssss, like we are used to */
    	$humanDate = $year . '-' . $month . '-' . $dayTime;

    	/* 2 digit singular day */
    	$day = sprintf("%02d", $day);

    	/* ITS DATABASE TIME */

// Create and populate an object
    	$saveData = new stdClass();
    	$saveData->Human_Date = $humanDate;
    	$saveData->Designation = $desg;
    	$saveData->Year = $year;
    	$saveData->Month = $month;
    	$saveData->Day = $day;
    	$saveData->Mag = $mag;
    	$saveData->Observatory = $observatory;
    	$saveData->Entry = $line;
// Insert the object into the user profile table.
    	$result = JFactory::getDbo()->insertObject('#__aso_astrometry', $saveData);
    }
    $i++;
}
?>

<!-- Display Results -->
<div class="well well-sm">
    <div>
    	<h4 class="alert alert-info" style="text-align: center; color: #000;">
    		Data pasted into the space below MUST be in the format specified by the <a href="http://cfa-www.harvard.edu/iau/info/ObsExamples.html" target="_blank">Harvard Minor Planet Center</a>.
    	</h4>
    </div>
    <?php if ($nope) { ?>
    	<div class="alert alert-danger">
    		You have entered <span class="badge btn-default"><?php echo $badCount; ?></span> invalid lines.<br>
    		<pre style="max-height: 250px;"><?php echo $nope; ?></pre>
    		This data will NOT be processed. Please correct your format and try these lines again.
    	</div>
    <?php } ?>

    <?php if ($yep) { ?>
    	<div class="alert alert-success">
    		You have entered <pre style="max-height: 250px;"><?php echo $yep; ?></pre><br>
    		Your <span class="badge label-success"><?php echo $foundCount; ?></span> validly formated observations were processed.
    	</div>
    <?php } ?>

</div>
{/source}