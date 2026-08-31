<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Хэрэглэгчийн цахим систем v.1</title>

    <link href="/assets/client/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/client/plugins/font-awesome/css/font-awesome.css" rel="stylesheet">
	<link href="/assets/client/plugins/datapicker/datepicker3.css" rel="stylesheet">
    <link href="/assets/client/plugins/ptsansnarrow/ptnarrow.css" rel="stylesheet">
    <link href="/assets/client/plugins/jasny/jasny-bootstrap.min.css" rel="stylesheet">
    <link href="/assets/client/plugins/morris/morris-0.4.3.min.css" rel="stylesheet">
	<link href="/assets/client/plugins/chosen/css/chosen.css" rel="stylesheet">
    <link href="/assets/client/css/animate.css" rel="stylesheet">
    <link href="/assets/client/css/style.css" rel="stylesheet">

    <link rel="preconnect" href="https://www.myqnapcloud.com/" />

</head>

<body class="mini-navbar2 fixed-sidebar">
    <div id="wrapper">
    	<nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
        	<div class="logoHead">
                <div class="loginLogo">
				    <img id="logo" src="/assets/images/logoNew.svg">
                </div>
                <div class="logoTitle">Хэрэглэгчийн цахим систем</div>
            </div>
            <ul class="nav" id="side-menu">
            	
                <li class='active'>
                    <a href="/clientarea/"><i class="fa fa-th-large"></i> <span class="nav-label">Файл</span> </a>
                </li>              
                <li >
                    <a href="javascript:void(0);"><i class="fa fa-wrench"></i> <span class="nav-label">Тохиргоо</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li ><a href="/clientarea/changepass">Нууц үг солих</a></li>
                    </ul>
                </li>
                 
            </ul>

        </div>
    </nav>
    <div id="page-wrapper" class="gray-bg">
        
        <div class="row border-bottom headerBgTop">
        <nav class="navbar navbar-static-top " role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
			<a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
            <div class="welcome-user">Сайн байна уу<div><?php echo $onlainUserObj["userName"];?> <span >/<?php echo $onlainUserObj["companyName"];?>/</span></div></div>          
        </div>
            <ul class="nav navbar-top-links navbar-right">
                
				<li>
                    <a href="" class="count-info">
                        <i class="fa fa-envelope"></i> Миний бичиг <span class="label label-danger">1</span>
                    </a>
                </li>
                <li>
                    <a href="/clientarea/logout">
                        <i class="fa fa-sign-out"></i> Гарах
                    </a>
                </li>
                
            </ul>

        </nav>
    </div>        
        
        
    <?php include $incPageUrl; ?> 
 


        <div class="footer">
            <div class="pull-right">
                @2023 он. Хэрэглэгчийн цахим систем v.1
            </div>
        </div>

        </div>
        
    </div>

    <!-- Mainly scripts -->
    <script src="/assets/client/js/jquery-3.1.1.min.js"></script>
    <script src="/assets/client/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/client/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/assets/client/plugins/slimscroll/jquery.slimscroll.min.js"></script>
	<script src="/assets/client/plugins/datapicker/bootstrap-datepicker.js"></script>
    
    
	<script src="/assets/client/plugins/jasny/jasny-bootstrap.min.js"></script>
    <!-- Custom and plugin javascript -->
    <script src="/assets/client/js/inspinia.js"></script>
    <script src="/assets/client/plugins/pace/pace.min.js"></script>
    
    <script src="/assets/client/plugins/chartJS/Chart.min.js"></script>
<script>
        $(document).ready(function() {

            var lineData = {
                labels: ["1-р сар", "2-р сар", "3-р сар", "4-р сар", "5-р сар", "6-р сар", "7-р сар"],
                datasets: [
                    {
                        label: "Гэрээ сунгасан",
                        backgroundColor: "rgba(40,134,240,0.5)",
                        borderColor: "rgba(40,134,240,0.7)",
                        pointBackgroundColor: "rgba(40,134,240,1)",
                        pointBorderColor: "#fff",
                        data: [28, 48, 40, 19, 86, 27, 90]
                    },
                    {
                        label: "Хугацаа дууссан",
                        backgroundColor: "rgba(240,40,96,0.5)",
                        borderColor: "rgba(240,40,96,0.7)",
                        pointBackgroundColor: "rgba(240,40,96,1)",
                        pointBorderColor: "#fff",
                        data: [40, 59, 60, 51, 56, 55, 40]
                    }
					,
                    {
                        label: "Нийт хүсэлт",
                        backgroundColor: "rgba(220,220,220,0.5)",
                        borderColor: "rgba(220,220,220,1)",
                        pointBackgroundColor: "rgba(220,220,220,1)",
                        pointBorderColor: "#fff",
                        data: [60, 69, 80, 71, 86, 65, 50]
                    }
                ]
            };

            var lineOptions = {
                responsive: true
            };


            var ctx = document.getElementById("lineChart").getContext("2d");
            new Chart(ctx, {type: 'line', data: lineData, options:lineOptions});

        });
    </script></body>
</html>
