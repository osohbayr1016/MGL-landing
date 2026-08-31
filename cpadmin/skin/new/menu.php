<nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav" id="side-menu">
                <li class="nav-header text-center">
                    <div class="dropdown profile-element">
                    	<span>
                            <img alt="image" class="img-circle" src="<?php echo "/postpic/user.jpg";?>" />
						</span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> </span><span class="block m-t-xs"> <strong class="font-bold"><?php echo $onlainUserName;?></strong></span>
                              <span class="text-muted text-xs block">Admin</span></a>
                        
                    </div>
                    <div class="logo-element">
                        ME
                    </div>
                </li>
                <li <?php if($incPage=="home") echo "class='active'";?>>
                    <a href="/"><i class="fa fa-th-large"></i> <span class="nav-label">Самбар</span> </a>
                </li>
                <?php
                foreach($gloMenuArr as $key=>$obj){
					if(count($adminAccessPer[$key])>0){
				?>
                <li <?php if($incPage==$key) echo "class='active'";?>>
                    <a href="javascript:void(0);"><i class="<?php echo $obj["icon"];?>"></i> <span class="nav-label"><?php echo $obj["label"];?></span><span class="fa arrow"></span></a>
                    <?php
					if(count($obj["sub"])>0){
					?>
                    <ul class="nav nav-second-level">
                    	<?php
						foreach($obj["sub"] as $skey=>$sobj){
							if($adminAccessPer[$key][$skey]==$skey){
						?>
                        <li><a href="<?php echo "/".$key."/".$skey;?>"><?php echo $sobj;?></a></li>
                        <?php } } ?>
                    </ul>
                    <?php } ?>
                </li>
                <?php } } ?>                
                <li class="special_link">
                    <a href="<?php echo $gloDomainLink;?>" target="_blank"><i class="fa fa-database"></i> <span class="nav-label">Вэб сайт</span></a>
                </li>
            </ul>

        </div>
    </nav>
