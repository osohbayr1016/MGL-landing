<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">Харилцагч</div>
        </div>
        <form action="/userPost/users" id="frmMainReg" enctype="multipart/form-data" method="post">
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group frmReq">
                        <label class="font-noraml">Овог нэр</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmFname" value="<?php echo $editUserObj["userName"];?>">
                        </div>
                        <div class="frmErr">Овог нэр оруулна уу</div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group frmReq">
                        <label class="font-noraml">Компани нэр</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmName" value="<?php echo $editUserObj["companyName"];?>">
                        </div>
                        <div class="frmErr">Компани нэр оруулна уу</div>
                    </div>
                </div>                    
           </div>

            
            <div class="form-group" >
                <div><label >Төлөв </label></div>
                <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <div class="btn btn-primary btn-sm <?php if($editUserObj["userActive"]=="a") echo 'active';?>">
                        <input type="radio" name="frmStatus" <?php if($editUserObj["userActive"]=="a") echo 'checked="checked"';?> value="a"  autocomplete="off"> Зөвшөөрөгдсөн
                    </div>
                    <div class="btn btn-primary btn-sm <?php if($editUserObj["userActive"]=="n") echo 'active';?>">
                        <input type="radio" name="frmStatus" <?php if($editUserObj["userActive"]=="n") echo 'checked="checked"';?> value="n"  autocomplete="off"> Хандах эрхгүй
                    </div>
                </div>
                <small class="frmErr">Төрөл сонгоно уу</small>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Утас</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmPhone" value="<?php echo $editUserObj["companyPhone"];?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                   <div class="form-group">
                        <label class="font-noraml">И-Мэйл</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmMail" value="<?php echo $editUserObj["companyMail"];?>">
                        </div>
                    </div>
                </div>                    
           </div>
           <div class="row">
                <div class="col-lg-6">
                    <div class="form-group frmReq">
                        <label class="font-noraml">Нэвтрэх нэр</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmLogin" id="frmLogin" value="<?php echo $editUserObj["loginMail"];?>">
                        </div>
                        <div class="frmErr">Нэвтрэх нэр оруулна уу</div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Нэвтрэх нууц үг</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmPass" value="">
                        </div>
                    </div>
                </div>                   
           </div>
           
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="userPost" />
            <?php 
			if($editID>0){
			?>
			<input name="frmEditID" id="frmEditID" type="hidden" value="<?php echo $editID;?>" />
			<?php } ?>
                            
            <button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
            <button type="submit" class="btn btn-primary">Хадгалах</button>
        </div>
        </form>
    </div>
</div>
<script>
$().ready(function(){
	
	
	
	$('#frmMainReg').submit(function(event) {
		
		blurIS = 'ok';
		var postFrm = true;
		

		if($(".frmReq").length>0)
		$(".frmReq").each(function() {
			if($(this).find(".form-control").val()=='' || $(this).find(".form-control").val()== null){
				
				if(blurIS=='ok'){
					$(this).find(".form-control").focus();
					blurIS = '';
				}
				
				$(this).addClass("errDiv");
				$(this).removeClass("doneDiv");
				
				postFrm = false;
					
			}
			
		});
		
		if(postFrm){

            addUrl= "";
            if($("#frmEditID").length>0)
                addUrl= "&editID=" + $("#frmEditID").val();
			
            $.ajax({
                type: "POST",
                url: "/userPost/users",
                data: '&frmPost=checkname&loginName=' + $("#frmLogin").val() + addUrl,
                dataType: "html",
                success: function(msg){
                    
                    if(parseInt(msg)>0)
                    {				
                        
                       alert("Нэвтрэх нэр давхардаж байна! солино уу");			
                    
                       return false;
                        
                    }
                    else
                        return false;
                }
                    
            });	
			
		}
       
		
        return postFrm;
		
	});
	
});

</script>
