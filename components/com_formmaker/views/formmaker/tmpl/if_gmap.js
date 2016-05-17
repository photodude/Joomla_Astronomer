/* Code based on Google Map APIv3 Tutorials */


var gmapdata= new Array();
var gmapmarker = new Array();

function if_gmap_init(id, form_id)
{
	map=document.getElementById(id+"_element"+form_id);
	var def_zoomval = parseInt(map.getAttribute("zoom"));
	var def_longval = map.getAttribute("center_x");
	var def_latval  = map.getAttribute("center_y");

    var curpoint = new google.maps.LatLng(def_latval,def_longval);

    gmapdata[id] = new google.maps.Map(document.getElementById(id+"_element"+form_id), {
                center: curpoint,
                zoom: def_zoomval,
                mapTypeId: 'roadmap'
                });
        
    gmapmarker[id] = new Array();
   
        return false;
} // end of if_gmap_init


function add_marker_on_map(id, i, w_long, w_lat, w_info, form_id, dragb)
{
	var marker_point = new google.maps.LatLng(w_lat, w_long);
	
	
       
    gmapmarker[id][i] = new google.maps.Marker({
                                        map: gmapdata[id],
                                        position: marker_point,
                                        draggable: dragb
                                });
								
	gmapmarker[id][i].setDraggable(dragb);
	
	if(dragb)
	{
		google.maps.event.addListener(gmapmarker[id][i], 'drag', function() 
		{
				document.getElementById(id+"_lat"+form_id).value = gmapmarker[id][i].getPosition().lat().toFixed(6);
				document.getElementById(id+"_long"+form_id).value = gmapmarker[id][i].getPosition().lng().toFixed(6);
		});
	}
	
    infoW = new google.maps.InfoWindow;
        
    google.maps.event.addListener(gmapmarker[id][i], 'click', function() 
	{
        infoW.setContent('<div style="overflow: hidden;">'+document.getElementById(id+"_element"+form_id).getAttribute('info'+i)+"</div>");
		var infoWOpt = {
				maxWidth: "300"
				};
		infoW.setOptions(infoWOpt);
        infoW.open(this.getMap(), this);
    });
        
   return false;

	
} // end of if_gmap_init

