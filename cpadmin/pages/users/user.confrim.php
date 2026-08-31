<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title"><?php echo $modTitle;?></div>
        </div>
        <form action="/userPost/users" enctype="multipart/form-data" method="post">
        <div class="modal-body">
            <?php 
			if($isDelete){
				?>
            Та <strong><?php echo $userObj["companyName"];?></strong> харилцагчийг устгахдаа итгэлтэй байна уу
            <?php
			}
			else{
			?>
           <div class="alert alert-danger" role="alert"><strong><?php echo $userObj["companyName"];?></strong> харилцагчийг устгах боломжгүй. Энэ ажлын (<?php echo $isUserFileCount;?>) файл оруулсан байна!</div>
           <?php } ?>

            
           
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="delete" />
			<input name="frmUserID" type="hidden" value="<?php echo $userID;?>" />
                            
            <button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
            <?php if($isDelete){?>
            <button type="submit" class="btn btn-<?php echo $modBtn;?>">Тийм</button>
            <?php }?>
        </div>
        </form>
    </div>
</div>