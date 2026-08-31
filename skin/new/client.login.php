
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Хэрэглэгчийн цахим систем</title>

    <link href="/assets/client/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/client/plugins/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="/assets/client/css/animate.css" rel="stylesheet">
    <link href="/assets/client/css/login.css" rel="stylesheet">

</head>

<body >
<div class="row">
	
    <div class="col-sm-8 col-lg-9 hidden-xs" >
    	<div class="aboutDiv">
            <div class="headDiv">
                <H3>Хэрэглэгчийн систем v.1</H3>
                <h1>Тавтай морил</h1>
                <p>Нэг дороос илүү хялбар, илүү хурдан ...</p>
            </div>
            
        </div>
    </div>
    <div class="col-sm-4 col-lg-3">
        <div class="loginDiv">
            <div>
                <div class="loginLogo">
				<img id="logo" width="100" src="/assets/images/logoNew.svg">
                </div>
                <div style="height:20px;"></div>
                <h3>Хэрэглэгчийн систем </h3>

            
                <p>Системд нэвтрэх.</p>
                <form class="m-t" action="/modu/pagePost" method="post">
                    <div class="form-group">
                        <input type="text" name="userName" class="form-control" placeholder="Нэвтрэх нэр" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="userPass" class="form-control" placeholder="Нууц үг" required>
                    </div>
                    <input type="hidden" name="selPage" value="clientarea">
                    <input type="hidden" name="frmPost" value="login">
                    <button type="submit" class="btn btn-danger btn-block">Нэвтрэх</button>
                </form>
                <div style="height:50px;"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal inmodal" id="mainModalFrm" tabindex="-1" role="dialog" aria-hidden="true">
    
</div>
     <script src="/assets/client/js/jquery-3.1.1.min.js"></script>
    <script src="/assets/client/plugins/bootstrap/js/bootstrap.min.js"></script>
	
    <script>
$(document).ready(function() {
	
	$(".ajaxModBtn").click(function(){
		
		var linkURL = $(this).attr("href");
		
		$('#mainModalFrm').html('<div class="modal-body text-center">Түр хүлээнэ үү ...</div>');
		$('#mainModalFrm').modal()        
		$('#mainModalFrm').modal({ keyboard: false })
		$('#mainModalFrm').modal('show')

		$.ajax({
			type: "POST",
			url: linkURL,
			data: '&modAjax=ok',
			dataType: "html",
			success: function(msg){
				
				if(parseInt(msg)!=0)
				{				
					
					$('#mainModalFrm').html(msg);			
		
				}
			}
			
		});	
		
		return false;
			
	});
	
});
</script>

</body>

</html>
