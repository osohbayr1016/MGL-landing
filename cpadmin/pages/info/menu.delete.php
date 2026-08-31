<link href="/assets/plugins/tagsinput/bootstrap-tagsinput.css" rel="stylesheet">
<script src="/assets/plugins/tagsinput/bootstrap-tagsinput.js"></script>
<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">Цэс устгах</div>
        </div>
        <form action="/userPost/info" method="post">
        <div class="modal-body">
            
            <?php 
			if($isDelete){
				?>
            Та <strong><?php echo $selTypeObj["name"]?></strong> цэсийг устгахдаа итгэлтэй байна уу
            <?php
			}
			else{
			?>
           <div class="alert alert-danger" role="alert"><strong><?php echo $selTypeObj["name"]?></strong> цэсийг устгах боломжгүй. Энэ цэсэнд мэдээлэл оруулсан байна!</div>
           <?php } ?>
           
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="menuDel" />
			<input name="frmDelID" type="hidden" value="<?php echo $selTypeID;?>" />
            <input type="hidden" name="frmMainType" value="<?php echo $thisTypeType;?>" />
                            
            <button type="button" class="btn btn-white" data-dismiss="modal">Болих</button>
            <?php if($isDelete){?>
            <button type="submit" class="btn btn-danger">Устгах</button>
            <?php }?>
        </div>
        </form>
    </div>
</div>