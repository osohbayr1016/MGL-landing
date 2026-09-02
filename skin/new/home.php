<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta charset="utf-8" />
    <title><?php echo $siteMainTitle;?></title>
	<meta name="description"
		content="<?php echo $siteInfoDes;?>" />
	<meta name="keywords" content="<?php echo $siteMainTitle;?> <?php echo $siteInfoKeywords;?>">
	<!-- <meta name="author" content=""> -->
	<meta property="og:title" content="<?php echo $siteMainTitle;?>" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="<?php echo $siteInfoThisUrl;?>" />
	<meta property="og:image"
		content="<?php echo $siteInfoImg;?>" />
	<meta property="og:site_name" content="<?php echo $siteMainTitle;?>" />
	<meta property="og:description"
		content="<?php echo $siteInfoDes;?>" />
	<meta name="twitter:card" content="summary" />
	<meta name="twitter:title" content="<?php echo $siteMainTitle;?>" />
	<meta name="twitter:description"
		content="<?php echo $siteInfoDes;?>" />
	<meta name="twitter:image"
		content="<?php echo $siteInfoImg;?>" />
	<meta itemprop="image"
		content="<?php echo $siteInfoImg;?>" />
        
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <meta name="referrer" content="origin-when-cross-origin" />

   
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;500;600;700;800&display=swap" rel="stylesheet">
    <link href="/assets/css/style.css?v=<?php echo time()?>" rel="stylesheet">
    <?php if($clkMenuObj["pageType"]=="home") { ?>
    <link href="/assets/css/home-projects.css?v=<?php echo time()?>" rel="stylesheet">
    <link href="/assets/css/home-projects-marquee.css?v=<?php echo time()?>" rel="stylesheet">
    <link href="/assets/css/home-news.css?v=<?php echo time()?>" rel="stylesheet">
    <?php } ?>
    <?php if(isset($_REQUEST["productID"]) && $incPage=="projects") { ?>
    <link href="/assets/css/project-detail-hero.css?v=<?php echo time()?>" rel="stylesheet">
    <link href="/assets/css/project-detail.css?v=<?php echo time()?>" rel="stylesheet">
    <?php } ?>
    <link href="/assets/css/mobile.css?v=<?php echo time()?>" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link href="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.css" type="text/css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script type='text/javascript' src='https://platform-api.sharethis.com/js/sharethis.js#property=5f7ae97f870509001288d656&product=sop' async='async'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.31/moment-timezone-with-data.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="https://rawgit.com/kimmobrunfeldt/progressbar.js/1.0.0/dist/progressbar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.5/ScrollMagic.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.20.3/TweenMax.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.5/plugins/animation.gsap.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.20.3/plugins/ScrollToPlugin.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-scrollify@1.0.21/jquery.scrollify.min.js"></script>
</head>
<body class="<?php echo $clkMenuObj["pageType"]." ".$pageHeaderCls;?>">

    <div class="search-overlay"></div>




	<?php include "header.php"; ?>
	



   <?php include $incPageUrl; ?> 



    <div id="page-footer">
        <div id="page-footer-wrap" class="wrapperfull">
            <div id="page-footer-right">
                <div id="footer-menu-left" class="menu">
					<ul class="">
                    	<?php
						foreach($allAllMenuArr as $key=>$obj){
							$menuLink = menuLinkFunc($obj);
						?>
						<li id="menu-item-<?php echo $obj["id"]?>" class="menu-item  current-menu-item"><a class=" active" target="_self" href="<?php echo $menuLink;?>"><?php echo $obj["name"]?></a></li>
          <?php } ?>
          <li id="menu-item-clientarea" class="menu-item  current-menu-item"><a class=" active" target="_self" href="/clientarea">Client area</a></li>
					</ul>
				</div>
            </div>
            <div id="footer-menu-social" class="menu"><ul class=""><li id="menu-item-21" class="menu-item "><a class="facebook" target="_blank" href="<?php echo $selConfig["socialFB"];?>">Facebook</a></li><li id="menu-item-22" class="menu-item "><a class="linkedintw" target="_blank" href="<?php echo $selConfig["socialIN"];?>">LinkedIn</a></li></ul></div>
            <div class="footer-copyright">
                <p>Copyright © 2022  |  All rights reserved | MGL E&C LLC</p>
            </div>
        </div>
    </div>


    
    <script>
        AOS.init();
    </script>




    <script src="https://unpkg.com/magic-grid@3.1.2/dist/magic-grid.min.js"></script>

    <script src="/assets/js/all.js?v=4"></script>
    <script src="/assets/js/mobile.js?v=2"></script>
	<?php 
if(isset($widJsArr))
if(count($widJsArr)>0)
foreach($widJsArr as $key=>$incPageJS){
	$selWidID = txtSec($key);
	if($incPageJS!="" and is_file($incPageJS))
		include $incPageJS; 
}
?>

<!-- Messenger Chat Plugin Code -->
    <div id="fb-root"></div>

    <!-- Your Chat Plugin code -->
    <div id="fb-customer-chat" class="fb-customerchat">
    </div>

    <script>
      var chatbox = document.getElementById('fb-customer-chat');
      chatbox.setAttribute("page_id", "1409857409039950");
      chatbox.setAttribute("attribution", "biz_inbox");
    </script>

    <!-- Your SDK code -->
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          xfbml            : true,
          version          : 'v14.0'
        });
      };

      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>
    
</body>
</html>
