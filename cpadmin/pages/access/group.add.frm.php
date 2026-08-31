<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">Эрхийн бүлэг</div>
        </div>
        <form action="/userPost/access" method="post">
        <div class="modal-body">
            
            <div class="form-group">
                <label class="font-noraml">Бүлгийн нэр</label>
                <div >
                    <input class="form-control input-sm" type="text" name="frmName" value="<?php echo $selGroupObj["adminGroupName"];?>">
                </div>
            </div> 
           <div class="form-group">
                <label class="font-noraml">Хандах эрх</label>
                
                <select data-placeholder="Хандах эрхээ сонгоно уу..." name="frmAccess[]" class="chosen-select" multiple >
                    <option value="">Select</option>
                    <?php
                    foreach($gloMenuArr as $key=>$obj){
                    ?>
                    <optgroup label="<?php echo $obj["label"]?>">
                        <?php
                        foreach($obj["sub"] as $skey=>$sobj){
                        ?>
                        <option <?php if($accessCArr[$skey]==$skey) echo 'selected="selected"';?>  value="<?php echo $key."_".$skey?>"><?php echo $sobj?></option>
                        <?php } ?>
                    </optgroup>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="groups" />
            <?php
            if($editID>0){
			?>
            <input type="hidden" name="frmEditID" value="<?php echo $editID;?>" />
            <?php } ?>
            <button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
            <button type="submit" class="btn btn-primary">Хадгалах</button>
        </div>
        </form>
    </div>
</div>
    
<script>
$(document).ready(function() {
	
	$(".chosen-select").chosen({width:"100%"});
	
});
</script>