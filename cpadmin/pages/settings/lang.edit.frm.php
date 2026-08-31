<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">Хэлний сонголт</div>
        </div>
        <form action="/userPost/settings" enctype="multipart/form-data" method="post">
        <div class="modal-body">

            <div class="form-group">
                <label class="font-noraml">Нэр</label>
                <div >
                    <input type="text" class="form-control input-sm auTypeName" autocomplete="off" name="frmName" value="<?php echo $selTypeObj["langName"];?>">
                </div>
            </div>
           <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Зураг</label>
                        <div >
                            <input type="file" name="frmIcon" >
                        </div>
                    </div>
                </div>
				<div class="col-lg-3">
                    <div class="form-group">
                        <label class="font-noraml">Түлхүүр</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmKey" value="<?php echo $selTypeObj["langKey"];?>">
                        </div>
                    </div>
                </div> 
                <div class="col-lg-3">
                    <div class="form-group">
                        <label class="font-noraml">Эрэмбэ</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmOrder" value="<?php echo $lastTypeOrder;?>">
                        </div>
                    </div>
                </div>                   
           </div>
           <?php
            if(isset($oldLangArr)){
            ?>
           <div class="form-group">
                <label class="font-noraml">Өгөгдөл татах</label>
                <div >
                    <select class="form-control input-sm" name="frmCopyLang">
                    <option  value="0">Хоосон өгөгдөлтэй үүсгэх</option>
                    <?php
                    if(count($oldLangArr)>0)
                    foreach($oldLangArr as $key=>$obj){
                    ?>
                    <option  value="<?php echo $obj["langID"];?>"><?php echo $obj["langName"];?>-хэлний өгөгдөлийг хуулбарлаж татах</option>
                    <?php } ?>
                    </select>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="langPost" />
            <?php 
			if($selTypeID>0){
			?>
			<input name="frmEditID" type="hidden" value="<?php echo $selTypeID;?>" />
			<?php } ?>
                            
            <button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
            <button type="submit" class="btn btn-primary">Хадгалах</button>
        </div>
        </form>
    </div>
</div>
    
