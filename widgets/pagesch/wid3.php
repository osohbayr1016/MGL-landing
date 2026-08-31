
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
    
    <div class="awards" >
        <div class="flexRow">
        	<?php
			$i = 0;
			if(count($objs["sub"])>0)
			foreach($objs["sub"] as $key=>$obj){
                $subSchBody = json_decode($obj["schNote"],true);
			?>
				
            <div class="flexCol col-4-1">
                <div>
                    <h3><?php echo $subSchBody["title"];?></h3>
                    <div>
                        <?php echo $subSchBody["note"];?>
                    </div>
                </div>
            </div>
            <?php } ?>
            
        </div>
    </div>
    
</div>