           
            <div class="form-group">
                <label class="font-noraml">Гарчиг</label>
                <div >
                    <input type="text" class="form-control input-sm" autocomplete="off" name="frmName" value="<?php echo $selTypeObj["schTitle"];?>">
                </div>
            </div>
          
            <div class="form-group" >
                <label class="font-noraml">Зураг</label>
                <div class="input-group">
                    <input type="text" name="frmPic" id="postVidLink" value="<?php echo $selTypeObj["schPic"];?>" class="form-control" placeholder="Зураг...">
                    <span class="input-group-btn">
                    <button class="btn btn-default" onclick="open_popup('/assets/plugins/filemanager/dialog.php?popup=1&field_id=postVidLink')" type="button">Зураг сонгох</button>
                    </span>
                </div>
            </div>
            
            <div >
                <?php 
                if($selTypeObj["schPic"]!=""){?>
                <img height="100" src="<?php echo $selTypeObj["schPic"];?>" />
                <?php } ?>
            </div>
              
		   <div class="form-group">
				<label class="font-noraml">Тайлбар</label><br />
				<div >
                	<textarea class="form-control input-sm wp-editor-area"><?php echo $selTypeObj["schNote"];?></textarea>
                    <textarea style="display:none" id="frmNote" name="frmNote"></textarea>
                    
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
				
				$('#courseListID').html(data);			
				$('#orderModalFrm').modal('hide');
			}
			
		});	
		
		return false;
		
	});
	
	tinymce.init({
		selector: '.wp-editor-area',
		theme: 'modern',
		height: 300,
		plugins: [
		  'advlist autolink link image lists charmap print preview hr anchor pagebreak',
		  'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
		  'save table contextmenu directionality emoticons template paste responsivefilemanager textcolor'
		],
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