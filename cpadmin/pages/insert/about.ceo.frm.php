<script src="/assets/plugins/chosen/js/chosen.jquery.js"></script>
<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">Төсөл</div>
        </div>
        <form action="/userPost/insert" id="frmSch" enctype="multipart/form-data" method="post">
        <div class="modal-body">
            <div class="form-group">
                <label class="font-noraml">Төслийн нэр</label>
                <div >
                    <input type="text" class="form-control input-sm" name="frmTitle" value="<?php echo $selTypeObj["ceoName"];?>">
                </div>
            </div>
            <div class="form-group">
                <label class="font-noraml">Төрөл</label>
                <div >
                    <select class="chosen-select" multiple="multiple" name="proType[]" id="proType" >
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
                        <label class="font-noraml">Салбар</label>
                        <div >
                            <select class="chosen-select" multiple="multiple" name="ceoType[]" id="ceoType" >
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
                        <label class="font-noraml">Байршил</label>
                        <div >
                            <select class="chosen-select" name="frmCat" id="frmCat" >
                                <option value="">Сонгох</option>
                                <?php 
                                foreach($proTypeArr as $key=>$obj){
                                ?>
                                <option <?php if($selTypeObj["ceoCat"]==$obj["id"]) echo 'selected="selected"';?> value="<?php echo $obj["id"]?>"><?php echo $obj["name"]?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>                   
           </div>
           <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Эхлэсэн огноо</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmStart" value="<?php echo $selTypeObj["ceoStart"];?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Дууссан огноо</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmEnd" value="<?php echo $selTypeObj["ceoEnd"];?>">
                        </div>
                    </div>
                </div>                   
           </div>
           <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Статус</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmStatus" value="<?php echo $selTypeObj["ceoStatus"];?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Хэмжээ</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmSize" value="<?php echo $selTypeObj["ceoSize"];?>">
                        </div>
                    </div>
                </div>                   
           </div>
           <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Харилцагч</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmClient" value="<?php echo $selTypeObj["ceoClient"];?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Дизайн баг</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmTeam" value="<?php echo $selTypeObj["ceoTeam"];?>">
                        </div>
                    </div>
                </div>                   
           </div>
            <div class="form-group">
				<label class="font-noraml">Үзүүлэлт</label><br />
				<div >
                	<textarea name="frmDesc" class="form-control input-sm "><?php echo $selTypeObj["ceoDesc"];?></textarea>
                    
				</div>
			</div>
		   <div class="form-group">
				<label class="font-noraml">Дэлгэрэнгүй тайлбар</label><br />
				<div >
                	<textarea class="form-control input-sm wp-editor-area"><?php echo $selTypeObj["ceoBody"];?></textarea>
                    <textarea style="display:none" id="frmNote" name="frmNote"></textarea>
                    
				</div>
			</div>
            
           <div class="row">
                <div class="col-lg-9">
                <div class="form-group" >
                        <label class="font-noraml">Үндсэн зураг</label>
                        <div class="input-group">
                            <input type="text" name="frmPic" id="postPicLink" value="<?php echo $selTypeObj["ceoPic"];?>" class="form-control" placeholder="Үндсэн зураг...">
                            <span class="input-group-btn">
                            <button class="btn btn-default" onclick="open_popup('/assets/plugins/filemanager/dialog.php?popup=1&field_id=postPicLink')" type="button">Сонгох</button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label class="font-noraml">Эрэмбэ</label>
                        <div >
                            <input type="number" min="1" class="form-control input-sm" name="frmOrder" value="<?php echo $lastTypeOrder;?>">
                        </div>
                    </div>
                </div>                   
           </div>
		<label>Слайд оруулах</label>
        <div class="row">
            <?php
            for($i=1;$i<9;$i++){
            ?>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                <div class="filePic" id="adsPicPath<?php echo $i;?>" style=" <?php if($selCeoPics[$i-1]!="") echo "background-image:url(".admPicUrl("ceo",$selCeoPics[$i-1]).")";?> ">
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
            <input type="hidden" name="frmPost" value="ceoPost" />
            <?php 
			if($selSlideID>0){
			?>
			<input name="frmEditID" type="hidden" value="<?php echo $selSlideID;?>" />
			<?php } ?>
            <input type="hidden" name="frmSlideType" value="<?php echo $slideType;?>" />
            <input type="hidden" name="frmMenuID" value="<?php echo txtSec($_REQUEST["menuID"]);?>" />
            <button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
            <button type="submit" class="btn btn-primary">Хадгалах</button>
        </div>
        </form>
    </div>
</div>
<script>
function open_popup(url)
{
        var w = 880;
        var h = 570;
        var l = Math.floor((screen.width-w)/2);
        var t = Math.floor((screen.height-h)/2);
        var win = window.open(url, 'ResponsiveFilemanager', "scrollbars=1,width=" + w + ",height=" + h + ",top=" + t + ",left=" + l);
}
$(document).ready(function() {
	$('.adsPicClk').click(function () {
		
		inputID = $(this).attr("data-file-id");		
		
		$('#frmPic' + inputID).click();
	
	});
	
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
	$('#frmSch').submit(function(event) {
		
		$("#frmNote").val(tinyMCE.activeEditor.getContent());
		
		
		return true;
		
	});
	$(".chosen-select").chosen({width: "100%"});
	tinymce.init({
		selector: '.wp-editor-area',
		theme: 'modern',
		height: 300,
		plugins: [
		  'advlist autolink link image lists charmap print preview hr anchor pagebreak',
		  'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
		  'save table contextmenu directionality emoticons template paste responsivefilemanager textcolor'
		],
		content_css: 'https://mglenc.com/assets/css/style.css?v=1699980233',
		toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons fullscreen | responsivefilemanager ',
		image_advtab: true ,
		external_filemanager_path:"/assets/plugins/filemanager/",
	   filemanager_title:"Responsive Filemanager" ,
	   external_plugins: { "filemanager" : "/assets/plugins/filemanager/plugin.min.js"}
	  });
});
$(document).on('focusin', function(e) {
  if ($(e.target).closest(".mce-window").length) {
    e.stopImmediatePropagation();
  }
});
</script>