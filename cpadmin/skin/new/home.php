<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Administrator | JAMTOUR </title>

    <link href="/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet">
	<link href="/assets/plugins/datapicker/datepicker3.css" rel="stylesheet">
    <link href="/assets/plugins/ptsansnarrow/ptnarrow.css" rel="stylesheet">
    <!-- Toastr style -->
    <link href="/assets/plugins/morris/morris-0.4.3.min.css" rel="stylesheet">
	<link href="/assets/plugins/chosen/css/chosen.css" rel="stylesheet">
    <link href="/assets/css/animate.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">

</head>

<body class="mini-navbar2">
    <div id="wrapper">
    	<?php include "menu.php";?>

        <div id="page-wrapper" class="gray-bg">
        
        <?php include "header.php";?>        
        
        <?php include $incPageUrl;?> 


        <div class="footer">
            <div class="pull-right">
                Development by BaA4kA
            </div>
        </div>

        </div>
        
    </div>

    <!-- Mainly scripts -->
    <script src="/assets/js/jquery-2.1.1.js"></script>
    <script src="/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
	<script src="/assets/plugins/datapicker/bootstrap-datepicker.js"></script>
    
    

    <!-- Custom and plugin javascript -->
    <script src="/assets/js/inspinia.js"></script>
    <script src="/assets/plugins/pace/pace.min.js"></script>
    
    <?php 
		if(count($widJsArr)>0)
		foreach($widJsArr as $key=>$incPageJS){
			$selWidID = txtSec($key);
			if($incPageJS!="" and is_file($incPageJS))
				include $incPageJS; 
		}
		?>
</body>
</html>
