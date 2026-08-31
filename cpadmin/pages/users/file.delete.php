<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title"><?php echo $modTitle;?></div>
        </div>
        <form action="/userPost/users" enctype="multipart/form-data" method="post">
        <div class="modal-body">
            Та <strong><?php echo $delFileObj["fileName"];?></strong> файлыг устгахдаа итгэлтэй байна уу
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="fileDelete" />
			<input name="frmUserID" type="hidden" value="<?php echo $userID;?>" />
            <input name="frmFileID" type="hidden" value="<?php echo $deleteID;?>" />         
            <button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
            <button type="submit" class="btn btn-<?php echo $modBtn;?>">Тийм</button>
        </div>
        </form>
    </div>
</div>