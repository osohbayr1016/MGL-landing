<div class=" headersubmenu">
    <ul class="aboutsubmenu">
    	<?php
		$aCls = "active";
		if(count($allSchArr)>0)
		foreach($allSchArr as $keys=>$objs){
			$selSchBody = json_decode($objs["schNote"],true);
			if($selSchBody["pic"]!=""){
		?>
        <li><a class="<?php echo $aCls;?>" href="#widhas<?php echo $objs["schID"]?>" id="widid-<?php echo $objs["schID"]?>"><span class="hover-text"><?php echo $selSchBody["title"];?></span></a></li>
        <?php $aCls = ""; } } ?>
    </ul>
</div>