<?php
 
?>
<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">Агуулга</div>
        </div>
        <form action="/userPost/insert" id="frmSch" enctype="multipart/form-data" method="post">
        <div class="modal-body">
            <div class="row">
            	<div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Дэс дугаар</label><br />
                        <div >
                            <input type="number" min="1" class="form-control input-sm" name="frmOrder" value="<?php echo $lastTypeOrder;?>">
                        </div>
                    </div>
                </div> 
                <div class="col-lg-6">
                    
                    <div  class="form-group">
                        <label class="font-noraml">Харагдах хэлбэр</label>
                        <select class="form-control input-sm" id="frmTemplate" name="frmTemplate" >
							<?php 
                            foreach($pageSchTemp as $key=>$obj){
                            ?>
                            <option <?php if($selSchTemp==$obj["id"]) echo 'selected="selected"';?> value="<?php echo $obj["id"]?>"><?php echo $obj["widgetTitle"]?></option>
                        	<?php } ?>
                        </select>
					</div>
                </div> 
                
                                   
           </div>
        <?php
                      
                
            include $clkMenuModDir."page.sch.frm.all.php";
            ?>
           
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="pageSch" />
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
<script>
function open_popup(url)
{
        var w = 880;
        var h = 570;
        var l = Math.floor((screen.width-w)/2);
        var t = Math.floor((screen.height-h)/2);
        var win = window.open(url, 'ResponsiveFilemanager', "scrollbars=1,width=" + w + ",height=" + h + ",top=" + t + ",left=" + l);
}
$(document).ready(function() {
	
    
	$("#frmTemplate").on('change', function() {

        var linkURL = "<?php echo $_SERVER["REQUEST_URI"];?>";
		
		
		$('#orderModalFrm').html('<div class="modal-body text-center">Түр хүлээнэ үү ...</div>');


		$.ajax({
			type: "POST",
			url: linkURL,
			data: '&modAjax=ok&frmModulType='+$(this).val(),
			dataType: "html",
			success: function(msg){
				
				if(parseInt(msg)!=0)
				{				
					
					$('#orderModalFrm').html(msg);			
		
				}
			}
			
		});	
		
		return false;

    });
	  
});

</script>
