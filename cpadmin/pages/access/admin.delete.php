<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">Админ устгах</div>
        </div>
        <form action="/userPost/access" method="post">
        <div class="modal-body">
            
            <?php 
			if($isDelete){
				?>
            Та <strong><?php echo $selNewObj["name"]?></strong> админ устгахдаа итгэлтэй байна уу
            <?php
			}
			else{
			?>
           <div class="alert alert-danger" role="alert"><strong><?php echo $selNewObj["name"]?></strong> админг устгах боломжгүй!</div>
           <?php } ?>
           
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="adminDel" />
			<input name="frmDelID" type="hidden" value="<?php echo $selNewsID;?>" />
                            
            <button type="button" class="btn btn-white" data-dismiss="modal">Болих</button>
            <?php if($isDelete){?>
            <button type="submit" class="btn btn-danger">Устгах</button>
            <?php }?>
        </div>
        </form>
    </div>
</div>