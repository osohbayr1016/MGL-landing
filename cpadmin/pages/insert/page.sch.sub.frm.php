<div class="modal-dialog modal-lg">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">"<?php echo $selSchObj["schTitle"];?>" туслах агуулга</div>
        </div>
        <form action="/userPost/insert" id="frmSch" enctype="multipart/form-data" method="post">
        <div class="modal-body">
            <div class="form-group">
                <label class="font-noraml">Дэс дугаар</label>
                <div >
                    <input type="text" class="form-control input-sm" autocomplete="off" name="frmOrder" value="<?php echo $lastTypeOrder;?>">
                </div>
            </div>
            <?php
            $incSubUrl = $clkMenuModDir."page.sch.sub.frm.all.php";           
                
            include $incSubUrl;
            ?>
           
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="pageSubSch" />
            <input type="hidden" name="frmMMID" value="<?php echo $selSchObj["schKey"];?>" />
            <input type="hidden" name="frmSchID" value="<?php echo $selCourseID;?>" />
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
	
	
  
});

</script>
