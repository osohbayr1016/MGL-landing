<script src="/assets/plugins/chosen/js/chosen.jquery.js"></script>
<script type='text/javascript' src='/assets/plugins/tinymce/tinymce.min.js'></script>
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
	
	$(".chosen-select").chosen({width: "100%"});
	
	$(document).on('click',".accessModBtn", function(){
		
		var linkURL = $(this).attr("href");
		
		
		$('#orderModalFrm').html('<div class="modal-body text-center">Түр хүлээнэ үү ...</div>');
		$('#orderModalFrm').modal()        
		$('#orderModalFrm').modal({ keyboard: false })
		$('#orderModalFrm').modal('show')

		$.ajax({
			type: "POST",
			url: linkURL,
			data: '&modAjax=ok',
			dataType: "html",
			success: function(msg){
				
				if(parseInt(msg)!=0)
				{				
					
					$('#orderModalFrm').html(msg);			
		
				}
			}
			
		});	
		
		return false;
			
	});
	
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
	
	$('.dateFrmInp').datepicker({
		todayBtn: "linked",
		format: 'yyyy-mm-dd',
		keyboardNavigation: false,
		forceParse: false,
		calendarWeeks: true,
		autoclose: true
	});
	
	
});
tinymce.init({
			selector: '.wp-editor-area',
			theme: 'modern',
			height: 200,
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
</script>