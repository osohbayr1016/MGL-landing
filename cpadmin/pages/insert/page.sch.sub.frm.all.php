<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB2vFxpc5M0ziaL27PAOOKPodcpJd-HnTk&callback=initMap&v=weekly"></script>
<?php 
foreach($subColArr as $key=>$obj){
	switch($obj["colType"]){
		case "loc":
			?>
			
			<div class="form-group">
				<label class="font-noraml"><?php echo $obj["colName"]?></label><br />
                <div id="mapDiv" style="height:350px; background:#eee;"></div>
			</div>  
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group" >
                        <label class="font-noraml">Уртраг / Long</label>
                        <div >
                            <?php
                            if($selSchBody[$obj["colKey"]]!=""){
                                $arrLongLan = explode("|",$selSchBody[$obj["colKey"]]);
                            }
                            ?>
                            <input type="hidden" id="frmLocstr" name="frmVal[<?php echo $obj["colKey"]?>]" value="<?php echo $selSchBody[$obj["colKey"]];?>">
                            <input type="text" id="mapLon" class="form-control mapPosInp"  value="<?php echo $arrLongLan[0];?>">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group" >
                        <label class="font-noraml">Өргөрөг / Lat</label>
                        <div >
                            <input type="text" id="mapLat" class="form-control mapPosInp" value="<?php echo $arrLongLan[1];?>">
                        </div>
                    </div>
                </div>
            </div> 
			<script>
function initMap() {
        
        directionsDisplay = new google.maps.DirectionsRenderer();

        
        
        
        
        if($("#mapLat").val()!='' && $("#mapLon").val()!='')
            var latilongi = new google.maps.LatLng($("#mapLat").val(), $("#mapLon").val());
        else
            var latilongi = new google.maps.LatLng(47.920812059891624,106.9031320953369);
        
        var myOptions = {
            zoom: 6,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: latilongi
        }
        
        map = new google.maps.Map(document.getElementById("mapDiv"), myOptions);
        directionsDisplay.setMap(map);
        
        
        var marker = new google.maps.Marker({
            position: latilongi,
            title: 'new marker',
            draggable: true,
            map: map
        });

        var infowindow = new google.maps.InfoWindow({
            content: '<div id="infodiv2">infowindow!</div>'
        });
        //map.setZoom(15);
        map.setCenter(marker.getPosition())
        //infowindow.open(map, marker)
        
        google.maps.event.addListener(marker,'dragend',function(event){
            $("#mapLat").val(event.latLng.lat());
            $("#mapLon").val(event.latLng.lng());
        });
        
        google.maps.event.addListener(map, 'click', function(event) {
            marker.setPosition(event.latLng);
            
            $("#mapLat").val(event.latLng.lat());
            $("#mapLon").val(event.latLng.lng());
        });
        
        $(".mapPosInp").on('change', function() {
            
            newPos = new google.maps.LatLng($("#mapLat").val(), $("#mapLon").val());
            
            marker.setPosition(newPos);
            
            map.setCenter(newPos);
            
        })
        
        $(window).on('resize', function() {
            var currCenter = map.getCenter();
            google.maps.event.trigger(map, 'resize');
            map.setCenter(currCenter);
        })


    }
	</script>
			<?php 
			
			break;
		case "textarea":
			?>
			
			<div class="form-group">
				<label class="font-noraml"><?php echo $obj["colName"]?></label><br />
				<div >
					<textarea class="form-control" name="frmVal[<?php echo $obj["colKey"]?>]"><?php echo $selSchBody[$obj["colKey"]];?></textarea>					
				</div>
			</div>  
			<?php 
			break;
	case "editor":
?>

<div class="form-group">
    <label class="font-noraml"><?php echo $obj["colName"]?></label><br />
    <div >
        <textarea class="form-control input-sm wp-editor-area"><?php echo $selSchBody[$obj["colKey"]];?></textarea>
        <textarea style="display:none" id="frmNote" name="frmVal[<?php echo $obj["colKey"]?>]"></textarea>
        
    </div>
</div>  
<?php 
break;
case "file":
?>
<div class="form-group" >
    <label class="font-noraml"><?php echo $obj["colName"]?></label>
    <div class="input-group">
        <input type="text" name="frmVal[<?php echo $obj["colKey"]?>]" id="postVidLink<?php echo $obj["colKey"]?>" value="<?php echo $selSchBody[$obj["colKey"]];?>" class="form-control" placeholder="<?php echo $obj["colName"]?>...">
        <span class="input-group-btn">
        <button class="btn btn-default" onclick="open_popup('/assets/plugins/filemanager/dialog.php?popup=1&field_id=postVidLink<?php echo $obj["colKey"]?>')" type="button">Сонгох</button>
        </span>
    </div>
</div>
<?php 
break;
default:
?>
<div class="form-group">
    <label class="font-noraml"><?php echo $obj["colName"]?></label>
    <div >
        <input type="text" class="form-control input-sm" autocomplete="off" name="frmVal[<?php echo $obj["colKey"]?>]" value="<?php echo $selSchBody[$obj["colKey"]];?>">
    </div>
</div>

<?php break; } } ?>
<script>

$(document).ready(function() {
	
	$('#frmSch').submit(function(event) {
		

		if ($("#frmLocstr").length > 0)
			$("#frmLocstr").val($("#mapLon").val() + "|" +  $("#mapLat").val());

		if ($("#frmNote").length > 0)
			$("#frmNote").val(tinyMCE.activeEditor.getContent());
		
		$.ajax({
			type: "POST",
			url: "/userPost/insert",
			data: $('#frmSch').serialize(),
			dataType: "html",
			success: function(data){
				
				$('#subSch<?php echo $selCourseID;?>').html(data);	
				$('#orderModalFrm').modal('hide');
				$('#orderModalFrm').html('');	
			}
			
		});	
		
		return false;
		
	});
	
	tinymce.init({
		selector: '.wp-editor-area',
		theme: 'modern',
		height: 200,
		plugins: [
		  'advlist autolink link image lists charmap print preview hr anchor pagebreak',
		  'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
		  'save table contextmenu directionality emoticons template paste responsivefilemanager textcolor'
		],
		toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons fullscreen | responsivefilemanager ',
		image_advtab: true ,
		external_filemanager_path:"/assets/plugins/filemanager/",
	   filemanager_title:"Responsive Filemanager" ,
	   external_plugins: { "filemanager" : "/assets/plugins/filemanager/plugin.min.js"}
	  });
	  
});
$(document).on('focusin', function(e) {
  if ($(e.target).closest(".mce-window").length) {
    e.stopImmediatePropagation();
  }
});
</script>