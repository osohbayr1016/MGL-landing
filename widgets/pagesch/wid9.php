
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
    <div class="people-grid-wrap people-grid-small">
        <div class="people-grid-inner">
        <?php
		$i = 0;
		if(count($objs["sub"])>0)
		foreach($objs["sub"] as $key=>$obj){
            $subSchBody = json_decode($obj["schNote"],true);
		?>
            <div class="people-teaser-item people-teaser-small">
                <a href="#people<?php echo $obj["schID"]?>" class="people-teaser-image popup-trigger">
                    <img src="<?php echo newsPicFnc(0,$subSchBody["pic"]);?>"/>
                </a>
                <div class="people-teaser-content">
                        <div class="people-teaser-name"><?php echo $subSchBody["title"];?></div>
                        <div class="people-teaser-job"><?php echo $subSchBody["sub"];?></div>
                        <div class="people-teaser-language">
                                <h6>MGL E&C</h6>
                        </div>
                                                            
                        <div id="people<?php echo $obj["schID"]?>" class="popup-content">
                            <div class="popup-content-inner">
                                <div class="person-details-top">
                                    <div class="person-image">
										<picture>
                                            <source type="image/jpeg" srcset="<?php echo newsPicFnc(0,$subSchBody["pic"]);?>">
                                            <img src="<?php echo newsPicFnc(0,$subSchBody["pic"]);?>">
                                        </picture>
                                    </div>
                                    <div class="person-primary-details">
                                        <div class="person-name"><?php echo $subSchBody["title"];?></div>
                                        <div class="person-job"><?php echo $subSchBody["sub"];?></div>
                                        <div class="person-language">
                                                <h6>MGL E&C</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="person-details-bottom">
                                    <div class="person-copy">
                                        <?php echo $subSchBody["note"];?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="#people<?php echo $obj["schID"]?>" class="popup-trigger"></a>
					</div>
                </div>
                                                                
           <?php } ?>                                                     
                                                                
                                                                
                                                                
	
		</div>
    </div>
    
    
    
</div>