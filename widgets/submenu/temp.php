<div class=" headersubmenu">
    <ul class="aboutsubmenu">
    	<?php
		
		if(count($subMenuArr)>0)
		foreach($subMenuArr as $keys=>$obj){
			$menuLink = menuLinkFunc($obj);
			$aCls = ""; 
			if($obj['id']==$pageID)
				$aCls=" active";
			if($obj["staticlink"]==$_SERVER['REQUEST_URI'])
				$aCls=" active";
			if($obj["pageType"]=="home" and $incPage==$obj["pageType"])
				$aCls=" active";
			if($obj["pageType"]=="news" and $incPage==$obj["pageType"])
				$aCls=" active";
		?>
        <li><a class="<?php echo $aCls;?>" href="<?php echo $menuLink?>" ><span class="hover-text"><?php echo $obj["name"];?></span></a></li>
        <?php  } ?>
    </ul>
</div>