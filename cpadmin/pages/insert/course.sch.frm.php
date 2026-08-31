
<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">Аялалын хөтөлбөр</div>
        </div>
        <form action="/userPost/insert" id="frmSch" enctype="multipart/form-data" method="post">
        <div class="modal-body">
            
            <div class="row">
            	<div class="col-lg-6">
                    <label class="font-noraml">Өдөр</label><br />
                    <div >
						<input type="text" class="form-control input-sm" name="frmOrder" value="<?php echo $lastTypeOrder;?>">
					</div>
                </div> 
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Гарчиг</label>
                        <div >
                            <input type="text" class="form-control input-sm" autocomplete="off" name="frmName" value="<?php echo $selTypeObj["schTitle"];?>">
                        </div>
                    </div>
                </div>
                                   
           </div>
		   <div class="form-group">
				<label class="font-noraml">Тайлбар</label><br />
				<div >
					<textarea class="form-control input-sm" rows="5" name="frmNote"><?php echo $selTypeObj["schNote"];?></textarea>
				</div>
			</div>  
            <div class="form-group">
				<label class="font-noraml">Газрын зураг</label><br />
                <div id="mapDiv" style="height:250px; background:#eee;"></div>
			</div>  
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group" >
                        <label class="font-noraml">Уртраг / Long</label>
                        <div >
                            <input type="text" id="mapLon" class="form-control mapPosInp" name="frmLon" value="<?php echo $selTypeObj["schLon"];?>">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group" >
                        <label class="font-noraml">Өргөрөг / Lat</label>
                        <div >
                            <input type="text" id="mapLat" class="form-control mapPosInp" name="frmLat" value="<?php echo $selTypeObj["schLat"];?>">
                        </div>
                    </div>
                </div>
            </div>
            <?php
            for($i=0;$i<5;$i++){
            ?>
            <div class="form-group" >
                <label class="font-noraml">Зураг-<?php echo ($i+1)?></label>
                <div class="input-group">
                    <input type="text" name="frmPicUrl[]" id="schModalPic<?php echo $i;?>" value="<?php echo $schImageArr[$i];?>" class="form-control" placeholder="Зураг...">
                    <span class="input-group-btn">
                        <button class="btn btn-default" onclick="open_popup('/assets/plugins/filemanager/dialog.php?popup=1&field_id=schModalPic<?php echo $i;?>')" type="button">Зураг сонгох</button>
                    </span>
                </div>
            </div>
            <?php }?>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="courseSch" />
            <input name="frmCourseID" type="hidden" value="<?php echo $selCourseID;?>" />
            <?php 
			if($editSchID>0){
			?>
			<input name="frmEditID" type="hidden" value="<?php echo $editSchID;?>" />
			<?php } ?>
                            
            <button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
            <button type="submit" class="btn btn-primary">Хадгалах</button>
        </div>
        </form>
    </div>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB2vFxpc5M0ziaL27PAOOKPodcpJd-HnTk&callback=initMap&v=weekly"></script>
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
$(document).ready(function() {
    
	$('#frmSch').submit(function(event) {
		
		
		$.ajax({
			type: "POST",
			url: "/userPost/insert",
			data: $('#frmSch').serialize(),
			dataType: "html",
			success: function(data){
				
				$('#courseListID').html(data);			
				$('#orderModalFrm').modal('hide');
			}
			
		});	
		
		return false;
		
	});
	
});
</script>
