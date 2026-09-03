<script src="/assets/plugins/chosen/js/chosen.jquery.js"></script>
<link href="/assets/plugins/tagsinput/bootstrap-tagsinput.css" rel="stylesheet">
<script src="/assets/plugins/tagsinput/bootstrap-tagsinput.js"></script>
<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">Visualisation</div>
        </div>
        <form action="/userPost/insert" id="frmSch" enctype="multipart/form-data" method="post">
        <div class="modal-body">
            <div class="form-group">
                <label class="font-noraml">Гарчиг</label>
                <div >
                    <input type="text" class="form-control input-sm" name="frmTitle" value="<?php echo $selTypeObj["visualTitle"];?>">
                </div>
            </div>
            <div class="form-group">
                <label class="font-noraml">Tools</label>
                <div >
                    <select class="chosen-select" multiple="multiple" name="frmTools[]" id="frmTools" >
                        <option value="">Сонгох</option>
                        <?php 
                        foreach($proTypesArr as $key=>$obj){
                        ?>
                        <option <?php if($selProTypeArr[$obj["id"]]==$obj["id"]) echo 'selected="selected"';?> value="<?php echo $obj["id"]?>"><?php echo $obj["name"]?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
           <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Creative Fields</label>
                        <div >
                            <select class="chosen-select" multiple="multiple" name="frmField[]" id="frmField" >
                                <option value="">Сонгох</option>
                                <?php 
                                foreach($typesArr as $key=>$obj){
                                ?>
                                <option <?php if($selCeoTypeArr[$obj["id"]]==$obj["id"]) echo 'selected="selected"';?> value="<?php echo $obj["id"]?>"><?php echo $obj["name"]?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Дизайнер</label>
                        <div >
                            <select class="chosen-select" name="frmDesigner" id="frmDesigner" >
                                <option value="">Сонгох</option>
                                <?php 
                                foreach($proTypeArr as $key=>$obj){
                                    $subSchBody = json_decode($obj["schNote"],true);
                                ?>
                                <option <?php if($selTypeObj["visualDesigner"]==$obj["schID"]) echo 'selected="selected"';?> value="<?php echo $obj["schID"]?>"><?php echo $subSchBody["title"]?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>                   
           </div>
            <div class="form-group">
                <label class="font-noraml">Түлхүүр үгс</label>
                <div >
                    <input type="text" class="form-control input-sm tagsInp" name="frmTags" value="<?php echo $selTypeObj["visualTags"];?>">
                </div>
            </div>
            <div class="form-group">
				<label class="font-noraml">Video embed code</label><br />
				<div >
                	<textarea name="frmEmbed" class="form-control input-sm "><?php echo $selTypeObj["visualVideo"];?></textarea>
                    
				</div>
			</div>
            <div class="form-group">
                <label class="font-noraml">Үндсэн зураг</label>
                <div >
                    <input type="file" name="frmPic" >
                </div>
            </div>
            <label>Слайд оруулах</label>
            <div class="row">
                <?php
                for($i=1;$i<9;$i++){
                ?>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                    <div class="filePic" id="adsPicPath<?php echo $i;?>" style=" <?php if($selCeoPics[$i-1]!="") echo "background-image:url(".admPicUrl("visual",$selCeoPics[$i-1]).")";?> ">
                        <span class="adsPicRemove" id="adsPicRemove<?php echo $i;?>" data-file-id="<?php echo $i;?>" style=" <?php if($selCeoPics[$i-1]!="") echo "display:block";?> "><i class="glyphicon glyphicon-remove"></i></span>
                        <div class="fileTxt adsPicClk" id="adsPicClk<?php echo $i;?>" data-file-id="<?php echo $i;?>" style=" <?php if($selCeoPics[$i-1]!="") echo "display:none";?> "><i class="glyphicon glyphicon-picture"></i></div>
                        <div class="fileInp"><input type="file" class="adsPicFile" data-file-id="<?php echo $i;?>" id="frmPic<?php echo $i;?>" name="frmSlide[]" /></div>
                        <input type="hidden" id="adsPicOld<?php echo $i;?>" name="frmOldPics[]" value="<?php echo $selCeoPics[$i-1];?>" />
                        <input type="hidden" id="adsPicDel<?php echo $i;?>" name="adsPicDel[]" value="" />
                    </div>
                </div>
            <?php } ?>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="visualPost" />
            <?php 
			if($selSlideID>0){
			?>
			<input name="frmEditID" type="hidden" value="<?php echo $selSlideID;?>" />
			<?php } ?>
            <input type="hidden" name="frmMenuID" value="<?php echo txtSec($_REQUEST["menuID"]);?>" />
            <button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
            <button type="submit" class="btn btn-primary">Хадгалах</button>
        </div>
        </form>
    </div>
</div>
<script>

$(document).ready(function() {
	$('.adsPicClk').click(function () {
		
		inputID = $(this).attr("data-file-id");		
		
		$('#frmPic' + inputID).click();
	
	});
	$('.tagsInp').tagsinput();
	$('.adsPicFile').change(function (event) {
		
		inputID = $(this).attr("data-file-id");
		
		$('#adsPicPath' + inputID).css("background-image", 'url(' + URL.createObjectURL(event.target.files[0]) + ')');
		
		$('#adsPicRemove' + inputID).css("display","block");
		$('#adsPicClk' + inputID).css("display","none");
		
	
	});
	
	$('.adsPicRemove').click(function () {
		
		inputID = $(this).attr("data-file-id");		
		
		$('#adsPicPath' + inputID).css("background-image", 'url()');
		$('#adsPicRemove' + inputID).css("display","none");
		$('#adsPicClk' + inputID).css("display","block");		
		$('#frmPic' + inputID).val('');
		$('#adsPicDel' + inputID).val('d');
		
	
	});
	$(".chosen-select").chosen({width: "100%"});
});

</script>