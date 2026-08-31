<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label class="font-noraml">Гарчиг</label>
            <div >
                <input type="text" class="form-control input-sm" autocomplete="off" name="frmName" value="<?php echo $selTypeObj["schTitle"];?>">
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="font-noraml">Дэд гарчиг</label>
            <div >
                <input type="text" class="form-control input-sm" autocomplete="off" name="frmSub" value="<?php echo $selTypeObj["schSub"];?>">
            </div>
        </div>
    </div>                        
</div>
<div class="form-group">
    <label class="font-noraml">Тайлбар</label><br />
    <?php
    $schStatss = explode("{NOTES}",$selTypeObj["schNote"]);
    ?>
    <div >
        <textarea row="6" class="form-control" id="frmNotes"><?php echo $schStatss[0];?></textarea>                    
    </div>
</div> 
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Дэлгэрэнгүй үзүүлэлт</h5>
        <div class="row">
            
                <div class="col-lg-6">
                    <label class="font-noraml">Үзүүлэлт</label>
                </div> 
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Тайлбар</label>
                    </div>
                </div>     
                                        
        </div>
        <input type="hidden" name="frmNote" id="frmNote" value="<?php echo $selTypeObj["schNote"];?>">
        <?php
        $schStats = explode("{STATS}",$schStatss[1]);
        $statsTitles = explode("|||",$schStats[0]);
        $statsStats = explode("|||",$schStats[1]);
        for($i=0;$i<3;$i++){
        ?>
        <div class="row">
            
                <div class="col-lg-6">
                    <div >
                        <input type="text" class="form-control input-sm statTitle" value="<?php echo $statsTitles[$i];?>">
                    </div>
                </div> 
                <div class="col-lg-6">
                    <div class="form-group">
                        <div >
                            <input type="text" class="form-control input-sm statValue" autocomplete="off" value="<?php echo $statsStats[$i];?>">
                        </div>
                    </div>
                </div>     
                                        
        </div>
        <?php }?>   
    </div>
</div>
<script>

$(document).ready(function() {
	
	$('#frmSch').submit(function(event) {
		
        var statTitles = "";
        var statValues = "";
		$(".statTitle").each(function() {
            statTitles = statTitles + $(this).val() + "|||";
        });
        $(".statValue").each(function() {
            statValues = statValues + $(this).val() + "|||";
        });
        
        $("#frmNote").val($("#frmNotes").val() + "{NOTES}"+ statTitles + "{STATS}"+ statValues);

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