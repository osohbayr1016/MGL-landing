<div class="wrapper">
	<div class="pageHeader" id="widhas<?php echo $objs["schID"]?>">
		<div class="header-bt wrapper">
			<h1><?php echo $selSchBody["title"];?></h1>
			 
		</div>
		<picture>
		<source type="image/jpeg" srcset="<?php echo newsPicFnc(0,$selSchBody["pic"]);?>">
		<img src="<?php echo newsPicFnc(0,$selSchBody["pic"]);?>">
		</picture>
	</div>
   	<div>
        <ul class="clients-list">
        	<?php
			if(count($objs["sub"])>0)
			foreach($objs["sub"] as $key=>$objss){
                $subSchBody = json_decode($objss["schNote"],true);
			?>
            <li class="client">
                <a href="<?php echo $subSchBody["link"];?>"><img alt="<?php echo $subSchBody["title"];?>" src="<?php echo newsPicFnc(0,$subSchBody["pic"]);?>"></a>
            </li>
			<?php } ?>
        </ul>
    </div>
</div>