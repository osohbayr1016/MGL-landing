<div class="modal-dialog modal-lg">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">"<?php echo $selSchObj["schTitle"];?>" хөтөлбөрийн хичээл</div>
        </div>
        <form action="/userPost/insert" id="frmSch" enctype="multipart/form-data" method="post">
        <div class="modal-body">
            
            <div class="row">
            	<div class="col-lg-6">
                    <label class="font-noraml">Дэс дугаар</label><br />
                    <div >
						<input type="text" class="form-control input-sm" name="frmOrder" value="<?php echo $lastTypeOrder;?>">
					</div>
                </div> 
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Гарчиг</label>
                        <div >
                            <input type="text" class="form-control input-sm" autocomplete="off" name="frmName" value="<?php echo $selTypeObj["schTitle"];?>">
                        </div>
                    </div>
                </div>
                                   
           </div>
		   <div class="form-group">
				<label class="font-noraml">Тайлбар</label><br />
				<div >
					<textarea class="form-control input-sm wp-editor-area"><div class="cssNewsMore"><div class="newsContent"><?php echo $selTypeObj["schNote"];?></div></div></textarea>
                    <textarea style="display:none" id="frmNote" name="frmNote"></textarea>
				</div>
			</div>  
           
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="courseSubSch" />
            <input type="hidden" name="frmMMID" value="<?php echo $selSchObj["schKey"];?>" />
            <input type="hidden" name="frmSchID" value="<?php echo $selCourseID;?>" />
            <?php 
			if($editSchID>0){
			?>
			<input name="frmEditID" type="hidden" value="<?php echo $editSchID;?>" />
			<?php } ?>
                            
            <button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
            <button type="submit" class="btn btn-primary">Хадгалах</button>
        </div>
        </form>
    </div>
</div>
<script>
$(document).ready(function() {
	
	$('#frmSch').submit(function(event) {
		
		$("#frmNote").val(tinyMCE.activeEditor.getContent());
		$.ajax({
			type: "POST",
			url: "/userPost/insert",
			data: $('#frmSch').serialize(),
			dataType: "html",
			success: function(data){
				
				$('#subSch<?php echo $selCourseID;?>').html(data);			
				$('#orderModalFrm').modal('hide');
				$('#orderModalFrm').html('');	
			}
			
		});	
		
		return false;
		
	});
	
	tinymce.init({
		selector: '.wp-editor-area',
		theme: 'modern',
		height: 400,
		plugins: [
		  'advlist autolink link image lists charmap print preview hr anchor pagebreak',
		  'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
		  'save table contextmenu directionality emoticons template paste responsivefilemanager textcolor'
		],
		content_css: 'http://www.medbook.mn/skin/new/css/style.css,http://www.medbook.mn/skin/new/css/custom.css?v=1585893108"',
		toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons fullscreen | responsivefilemanager ',
		image_advtab: true ,
		external_filemanager_path:"/assets/plugins/filemanager/",
	   filemanager_title:"Responsive Filemanager" ,
	   external_plugins: { "filemanager" : "/assets/plugins/filemanager/plugin.min.js"}
	  });
  
});

</script>
