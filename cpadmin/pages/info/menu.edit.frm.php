<link href="/assets/plugins/tagsinput/bootstrap-tagsinput.css" rel="stylesheet">
<script src="/assets/plugins/tagsinput/bootstrap-tagsinput.js"></script>
<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">Цэс</div>
        </div>
        <form action="/userPost/info" enctype="multipart/form-data" method="post">
        <div class="modal-body">
            
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Гарчиг</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmName" value="<?php echo $selTypeObj["name"];?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <label class="font-noraml">Төрөл</label>
                    <div >
                       <select class="form-control input-sm" name="frmType">
                       <?php
                        if(count($gloMenuMenu)>0)
                        foreach($gloMenuMenu as $key=>$obj){
                        ?>
                        <option <?php if($key==$selTypeObj["pageType"]) echo 'selected="selected"';?> value="<?php echo $key;?>"><?php echo $obj;?></option>
                        <?php } ?>
                       </select>
                    </div>
                </div>                    
           </div>
           <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Өнгө</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmColor" value="<?php echo $selTypeObj["color"];?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Icon</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmIcon" value="<?php echo $selTypeObj["icon"];?>">
                        </div>
                    </div>
                </div>                   
           </div>
           <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Линк</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmLinks" value="<?php echo $selTypeObj["staticlink"];?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Эрэмбэ</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmOrder" value="<?php echo $lastTypeOrder;?>">
                        </div>
                    </div>
                </div>                   
           </div>
           
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="menuPost" />
            <?php 
			if($selTypeID>0){
			?>
			<input name="frmEditID" type="hidden" value="<?php echo $selTypeID;?>" />
			<?php } ?>
            <input type="hidden" name="frmMainMenu" value="<?php echo $mainMenuID;?>" />
            <input type="hidden" name="frmMainType" value="<?php echo $mainType;?>" />
                            
            <button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
            <button type="submit" class="btn btn-primary">Хадгалах</button>
        </div>
        </form>
    </div>
</div>
    
<script>
$(document).ready(function() {
	
	$('.tagsInp').tagsinput();
	
});
</script>