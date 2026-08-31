<script src="/assets/js/bootstrap-autocomplete.min.js"></script>
<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">Ангилал</div>
        </div>
        <form action="/userPost/info" enctype="multipart/form-data" method="post">
        <div class="modal-body">
            
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Гарчиг</label>
                        <div >
                            <input type="text" class="form-control input-sm auTypeName" autocomplete="off" name="frmName" value="<?php echo $selTypeObj["name"];?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <label class="font-noraml">Өнгө</label><br />
                    <div >
						<input type="text" class="form-control input-sm" name="frmColor" value="<?php echo $selTypeObj["color"];?>">
					</div>
                </div>                    
           </div>
		   <div>
				<label class="font-noraml">Тайлбар</label><br />
				<div >
					<input type="text" class="form-control input-sm" name="frmTitle" value="<?php echo $selTypeObj["typeTitle"];?>">
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
                            <input type="text" class="form-control input-sm" name="frmKey" value="<?php echo $selTypeObj["typeKeys"];?>">
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
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="typePost" />
            <?php 
			if($selTypeID>0){
			?>
			<input name="frmEditID" type="hidden" value="<?php echo $selTypeID;?>" />
			<?php } ?>
            <input type="hidden" name="frmMainCat" value="<?php echo $mainCatID;?>" />
            <input type="hidden" name="frmMainType" value="<?php echo $mainType;?>" />
                            
            <button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
            <button type="submit" class="btn btn-primary">Хадгалах</button>
        </div>
        </form>
    </div>
</div>
    
