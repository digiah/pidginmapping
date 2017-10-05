<?php

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
		
		echo "checking: ".$file.": ";
		
		$q = "select `id`, `src`, from `_mapping_language_images` where `src` = '".$file."'";
		$r = $mysqli->query($q) or die (mysqli_error($mysqli));
		if ($r->num_rows > 0) {
			$img = mysqli_fetch_array($r);
			echo "DB REF \"".$img["src"]."\" (".$img["id"].") ";
			
			if (file_exists("images/".$img["src"])) {
				echo " found on server!";
			}
			else {
				echo " <span style='color: #F00;'>".$img["src"]." not found on server!</span>";
			}
		/*
			
			$sq = "update `_mapping_language_images` set `islive` = ? where `id` = ?";
			$stmt = $mysqli->prepare($sq) or die(mysqli_error($mysqli));
			
			if (!$stmt->bind_param('ii', $islive,$img["id"])) {
				echo "Binding failed";
			}
			else {
				$stmt->execute();
			}
			*/
		}
		else {
			echo "<span style='color: #F00;'> not found in DB!</span>";
		}
		
			echo"<br />";
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