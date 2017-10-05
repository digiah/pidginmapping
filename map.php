<?php

// connect to the database
$dberror = 0;

$mysqli = mysqli_connect("localhost", "root", "20CkbkZUN02", "dahi");
if (mysqli_connect_errno($mysqli)) {
  $dberror = 1;
  $status["badnews"][] = "Couldn't connect to database!";
}
mysqli_set_charset($mysqli,"utf8");

$images = array();

$q = "select `id`, `lat`, `lng`, `src`, `description` from `_mapping_language_images` where `lat` != ''";
$r = $mysqli->query($q) or die (mysqli_error($mysqli));
while ($i = mysqli_fetch_array($r)) {
	$images[] = array("id" => $i["id"], "lat" => $i["lat"], "lng" => $i["lng"], "src" => $i["src"], "description" => $i["description"]);
	//json_encode($result)
}
		
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Mapping Language</title>
<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.35.0/mapbox-gl.js'></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v0.35.0/mapbox-gl.css' rel='stylesheet' />
<style>

.marker {
	width: 30px;
	height: 30px;
	top: -15px;
	left: -15px;
	display: block;
    padding: 0;	
    cursor: pointer;
}

.mapboxgl-popup {
    max-width: 400px;
    font: 12px/20px 'Helvetica Neue', Arial, Helvetica, sans-serif;
}

#themap {
	width: 100%;margin-bottom: 25px;border: 1px solid #06F;
}

</style>

<script>

var stories = new Array();

<?php for ($i=0;$i<count($images);$i++): ?>
stories.push(<?php echo json_encode($images[$i]); ?>);
<?php endfor; ?>

$(document).ready(function(e) {

});


$( window ).resize(function() {
	var w = $("#themap").width();
	var h = (9 * parseInt(w)) / 16;
	$("#themap").height(h);
});
	
	
	
</script>

</head>

<body>


<div id="themap"></div>

<script>


var w = $("#themap").width();
var h = (9 * parseInt(w)) / 16;
$("#themap").height(h);

var markers = {};

mapboxgl.accessToken = 'pk.eyJ1IjoiZGF2aWRnb2wiLCJhIjoibW9vUnZHZyJ9.rILNHauIBtvc-FntxKuGvw';

var map = new mapboxgl.Map({
    container: 'themap',
    style: 'mapbox://styles/mapbox/light-v9', //mapbox://styles/mapbox/streets-v9
    center: [-157.74097964120034,21.39618067215781],
    zoom: 16
});

//map.addControl(new mapboxgl.Navigation());
//var popup = new mapboxgl.Popup({offset: 25}).setText(stories[i].description);

map.on("load", function() {
	
	for (i=0;i< stories.length;i++) {
		var el = document.createElement('div');
     	el.className = 'marker';
		el.style.backgroundImage = 'url(http://dahi.manoa.hawaii.edu/getdown/marker-end.png)';
		el.style.pointer = "cursor"; 
		el.id = "marker_"+i;
		$(el).attr("data-index", i);
		new mapboxgl.Marker(el)
                    .setLngLat([stories[i].lng,stories[i].lat])
                    .addTo(map);
	}
	
	$(".marker").click(function(e) {
		e.stopPropagation();
		//var html = "<div class='.mapboxgl-popup'>"+stories[$(this).attr("data-index")].description+"<br /><img src='http://dahi.manoa.hawaii.edu/mapping/language/images/thumbs/"+stories[$(this).attr("data-index")].src.replace(/\./,"_THUMB.")+"' style='width:400px;height:auto;' /></div>";
		
		var html = "<div class='.mapboxgl-popup'>"+stories[$(this).attr("data-index")].description+"<br /><img src='http://dahi.manoa.hawaii.edu/mapping/language/images/"+stories[$(this).attr("data-index")].src+"' style='width:400px;height:auto;' /></div>";
		
		
		$('.mapboxgl-popup') ? $('.mapboxgl-popup').remove() : null;
		var popup = new mapboxgl.Popup()
                    .setLngLat([stories[$(this).attr("data-index")].lng,stories[$(this).attr("data-index")].lat])
                    .setHTML(html)
                    .addTo(map);
					
		console.log($(this).attr("id"));
	});
	
/*
    map.addControl ( new mapboxgl.Navigation({ position: 'top-left' }) );
	
	
	for (i=0;i< stories.length;i++) {
		
		var el = document.createElement('div');
		el.className = 'marker';
		el.id = "marker_"+i;
		el.style.backgroundImage = 'url(http://dahi.manoa.hawaii.edu/getdown/marker-end.png)';
		el.style.pointer = "cursor"; 
		
		new mapboxgl.Marker(el)
		//.setPopup(popup) // sets a popup on this marker
		.setLngLat([stories[i].lng, stories[i].lat])
		.addTo(map);
	
		//map.flyTo({center:[lng,lat], zoom: 18});
	}
	*/
});
			

</script>

</body>
</html>