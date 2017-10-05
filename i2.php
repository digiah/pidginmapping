<?php

/*

GPS-related php functions

*/

//Pass in GPS.GPSLatitude or GPS.GPSLongitude or something in that format
function getGps($exifCoord)
{
  $degrees = count($exifCoord) > 0 ? gps2Num($exifCoord[0]) : 0;
  $minutes = count($exifCoord) > 1 ? gps2Num($exifCoord[1]) : 0;
  $seconds = count($exifCoord) > 2 ? gps2Num($exifCoord[2]) : 0;

  //normalize
  $minutes += 60 * ($degrees - floor($degrees));
  $degrees = floor($degrees);

  $seconds += 60 * ($minutes - floor($minutes));
  $minutes = floor($minutes);

  //extra normalization, probably not necessary unless you get weird data
  if($seconds >= 60)
  {
    $minutes += floor($seconds/60.0);
    $seconds -= 60*floor($seconds/60.0);
  }

  if($minutes >= 60)
  {
    $degrees += floor($minutes/60.0);
    $minutes -= 60*floor($minutes/60.0);
  }

  return array('degrees' => $degrees, 'minutes' => $minutes, 'seconds' => $seconds);
}

function gps2Num($coordPart)
{
  $parts = explode('/', $coordPart);

  if(count($parts) <= 0)// jic
    return 0;
  if(count($parts) == 1)
    return $parts[0];

  return floatval($parts[0]) / floatval($parts[1]);
}

function dms2Decimal ($gpsarray, $hemi) {
	$d = $gpsarray["degrees"] + $gpsarray["minutes"]/60 + $gpsarray["seconds"]/3600;
	return ($hemi=='S' || $hemi=='W') ? $d*=-1 : $d;
	
}


# point to correct directory

if ($handle = opendir("images")) {
	# for each file in the directory...
	
    while (false !== ($file = readdir($handle))) {
        if ('.' === $file) continue;
        if ('..' === $file) continue;
		# see if this filename exists in the database...
		// connect to the database
		$dberror = 0;
		
		$mysqli = mysqli_connect("localhost", "root", "20CkbkZUN02", "dahi");
		if (mysqli_connect_errno($mysqli)) {
		  $dberror = 1;
		  echo "Couldn't connect to database!";
		  exit(0);
		}
		mysqli_set_charset($mysqli,"utf8");
		
		$q = "select `id`, `src` from `_mapping_language_images` where `src` = '".$file."'";
		$r = $mysqli->query($q) or die (mysqli_error($mysqli));
		if ($r->num_rows > 0) {
			$img = mysqli_fetch_array($r);
			echo "checkfor \"".$img["src"]."\" (".$img["id"].")";
			$exif = exif_read_data("images/".$file, 'IFD0');
			if ($exif===false) { 
				echo "<span style='color: #F00;'>".$file." no header found</span><br />"; 
			}
			else {
				$exif = exif_read_data("images/".$file, 0, true);
				$latarr = getGps($exif["GPS"]["GPSLatitude"]);
				$lngarr = getGps($exif["GPS"]["GPSLongitude"]);
				
				$lat = dms2Decimal($latarr, $exif["GPS"]["GPSLatitudeRef"]);
				$lng = dms2Decimal($lngarr, $exif["GPS"]["GPSLongitudeRef"]);
			
				echo $file." -> LAT: ".$lat." LNG: ".$lng;
				
				$sq = "update `_mapping_language_images` set `lat` = ?, `lng` = ? where `id` = ?";
				$stmt = $mysqli->prepare($sq) or die(mysqli_error($mysqli));
				
				if (!$stmt->bind_param('ddi', $lat,$lng,$img["id"])) {
					echo "Binding failed";
				}
				else {
					$stmt->execute();
				}
			}
			echo"<br />";
		}
		else {
			echo "<span style='color: #F00;'>".$img["src"]." not found in DB!</span><br />";
		}
		
        // do something with the file
    }
    closedir($handle);
	/**/
}
else {
	echo "couldn't open directory!";	
}



# yes

# read exif data from file

# write lat and lng to database

# no

# log error to stdout

?>