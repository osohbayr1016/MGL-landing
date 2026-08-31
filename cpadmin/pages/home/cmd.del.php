<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">Сэтгэгдэл устгах</div>
        </div>
        <form action="/userPost/home" method="post">
        <div class="modal-body">
            Та <strong>"<?php echo $selTypeObj["comment"]?>"</strong> гэсэн сэтгэгдлийг устгахдаа итгэлтэй байна уу
           
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="cmdDel" />
			<input name="frmDelID" type="hidden" value="<?php echo $selTypeID;?>" />
                            
            <button type="button" class="btn btn-white" data-dismiss="modal">Болих</button>
            <button type="submit" class="btn btn-danger">Устгах</button>
        </div>
        </form>
    </div>
</div>