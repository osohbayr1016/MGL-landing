<div class="mainNavHeader">
<div class="menu-lang">
					<div class="lang-selected"><span style="margin-left:0.3em;"><?php echo $gloLangObj["langKey"];?></span></div>
					<div class="menu-lang-sub">
					<?php
						foreach($sysLangArr as $key=>$obj){
							if($obj["langID"]!=$gloLang){
						?>
						 / <a href="/<?php echo $obj["langKey"]?>"><span style="margin-left:0.3em;"><?php echo $obj["langKey"]?></span></a>
						<?php } } ?>
					</div>
				</div>
		<div id="header-top" class=" light">
			<div class="header-logo-div">
				<a class="header-logo" href="/"><img id="logo" src='/assets/images/logoNew.svg?v=3' alt="MGL E&C"></a>
				
			</div>
			
			<ul class="menu-icons-rhs">
			
            	<?php
                foreach($allAllMenuArr as $key=>$obj){
					if($obj["pageType"]!="home"){
					$menuLink = menuLinkFunc($obj);
				?>
				<li class="mainMenu">
					<a class="" href="<?php echo $menuLink;?>">
						<?php echo $obj["name"]?>
					</a>
				</li>
				<?php } } ?>
				<li class="mobMenu">
					<div class="menu-toggle-btn menu-icon">
						<span></span>
						<span></span>
						<span></span>
					</div>
				</li>
			</ul>

		</div>
		
	</div>
    <div id="menu-wrap-outer">
        <div id="menu-wrap-inner-wrap" style="display:none">
            <div id="menu-wrap-inner" class="wrapper">
                <div class="standard-menu">
                    <div id="header-menu" class="menu">
						<ul class="">
                        	<?php
							foreach($allAllMenuArr as $key=>$obj){
								if($obj["pageType"]!="home"){
								$menuLink = menuLinkFunc($obj);
							?>
							<li id="menu-item-<?php echo $obj["id"]?>" class="menu-item">
							<a class="" target="_self" href="<?php echo $menuLink;?>"><?php echo $obj["name"]?></a>
							<?php
							if(count($obj["sub"])>0){
							?>
							<ul class="sub-menu">
								<?php
								foreach($obj["sub"] as $keys=>$objs){
									$menuLink = menuLinkFunc($objs);
								?>
								<li >									
									<a href="<?php echo $menuLink?>" class="js-link-force_reload"><?php echo $objs["name"]?></a>									
								</li>
								<?php 
								}
								?>
							</ul>
							<?php } ?>
							</li>
                            <?php }} ?>
						</ul>
					</div>


                    <section class="language-link-wrap">
						<?php
						foreach($sysLangArr as $key=>$obj){
						?>
						<a href="/<?php echo $obj["langKey"];?>"><span class="menu-language-link"><?php echo $obj["langName"];?></span></a>
						<?php }?>
                    </section>

                    <div id="header-social-menu" class="menu"><ul class=""><li id="menu-item-10" class="menu-item "><a class="facebook" target="_blank" href="https://www.facebook.com/mglengineer">Facebook</a></li><li id="menu-item-11" class="menu-item "><a class="linkedin" target="_blank" href="https://www.linkedin.com/company//">LinkedIn</a></li><li id="menu-item-12" class="menu-item "><a class="instagram" target="_blank" href="https://www.instagram.com//">Instagram</a></li></ul></div>
                </div>
                <div id="menu-speak-your-language" class="language-menu">
                    <span class="menu-back-link">Back</span>
                    <div class="language-menu-inner">
						<ul class="">
                        	<?php
							foreach($allAllMenuArr as $key=>$obj){
								if($obj["pageType"]!="home"){
								$menuLink = menuLinkFunc($obj);
							?>
							<li id="menu-item-<?php echo $obj["id"]?>" class="menu-item"><a class="" target="_self" href="<?php echo $menuLink;?>"><?php echo $obj["name"]?></a></li>
                            <?php }} ?>
						</ul>
                    </div>
                </div>
            </div>
        </div>

    </div>