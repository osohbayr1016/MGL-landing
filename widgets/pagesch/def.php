
<div class="wrapper">
	<?php
			if($selSchBody["pic"]!=""){
		?>
	<div class="pageHeader" id="widhas<?php echo $objs["schID"]?>">
		<div class="header-bt wrapper">
			<h1><?php echo $selSchBody["title"];?></h1>
			 
		</div>
		<picture>
		<source type="image/jpeg" srcset="<?php echo newsPicFnc(0,$selSchBody["pic"]);?>">
		<img src="<?php echo newsPicFnc(0,$selSchBody["pic"]);?>">
		</picture>
	</div>
    <?php } echo $selSchBody["body"];?>
    
    <div >
        <?php
		if(count($objs["sub"])>0)
		foreach($objs["sub"] as $key=>$obj){
            $subSchBody = json_decode($obj["schNote"],true);
		?>
			
            <div>
                <?php echo $subSchBody["title"];?>
            </div>
        <?php } ?>
	</div>
    
</div>