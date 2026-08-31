<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">Эрхийн бүлэг</div>
        </div>
        <form action="/userPost/access" method="post">
        <div class="modal-body">
            
            <div class="form-group">
                <label class="font-noraml">Админы нэр</label>
                <div >
                    <input class="form-control input-sm" type="text" name="frmName" value="<?php echo $selAdminObj["name"];?>">
                </div>
           </div> 
           <div class="form-group">
                <label class="font-noraml">Нэвтрэх нэр</label>
                <div >
                    <input class="form-control input-sm" type="text" name="frmAName" value="<?php echo $selAdminObj["aname"];?>">
                </div>
           </div>
           <div class="form-group">
                <label class="font-noraml">Нэвтрэх нууц үг</label>
                <div >
                    <input class="form-control input-sm" type="password" name="frmPass">
                </div>
           </div>
           <div class="form-group">
                <label class="font-noraml">Эрхийн бүлэг</label>
                
                <select data-placeholder="Хандах эрхээ сонгоно уу..." name="frmGroup" class="chosen-select"  >
                    <option value="">Select</option>
                    <?php
                    foreach($groupArr as $key=>$obj){
                    ?>
                        <option <?php if($selAdminObj["adminGroupID"]==$obj["adminGroupID"]) echo 'selected="selected"';?>  value="<?php echo $obj["adminGroupID"]?>"><?php echo $obj["adminGroupName"]?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="admins" />
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