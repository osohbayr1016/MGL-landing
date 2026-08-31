<div class="wrapperfull" id="projectFullscreen" >
	<div class="process-slider-outer"style="margin-top:0;">
        <div class="process-slider-wrap owl-carousel">
        	<?php 
			if(count($selProPics)>1)
			foreach($selProPics as $imgi=>$imgurl){
				if($imgurl!=""){
			?>
            <div class="process-slide-item">
                <div class="process-slide-image">
                    <picture>
                        <source type="image/jpeg" srcset="/pics/ceo/<?php echo $imgurl;?>">
                        <img src="/pics/ceo/<?php echo $imgurl;?>">
                    </picture>
                </div>
            </div>
            <?php 
				}
			}
			else{
			?>
            <div class="process-slide-item">
                <div class="process-slide-image">
                    <picture>
                    <source type="image/jpeg" srcset="<?php echo newsPicFnc($productObj["ceoID"],$productObj["ceoPic"]);?><?php echo $productObj["ceoID"];?>">
                    <img src="<?php echo newsPicFnc($productObj["ceoID"],$productObj["ceoPic"]);?><?php echo $productObj["ceoID"];?>" >
                    </picture>
                </div>
            </div>
            <?php 
			}
			?>
            
        </div>

        <div id="slideFullScreenBtn">
            <div></div>
        </div>
        <div class="progress-circle-wrap">
            <div class="track"></div>
            <div class="halfclip">
                <div class="halfcircle clipped"></div>
            </div>
            <div class="halfcircle fixed"></div>
        </div>

        <div id="process-slider-dots" class="process-slider-trigger-wrap slider-trigger">
        	<?php 
			$key=1;
			if(count($selProPics)>1)
			foreach($selProPics as $imgi=>$imgurl){
				if($imgurl!=""){
			?>
                <button class="trigger-link"><?php echo $key;?></button>
            <?php $key++;}} ?>
        </div>
    </div>
</div>
<div class="wrapper projectMore">
    <div class="">
            <span class="eyebrow"><?php echo $productObj["brandName"];?></span>

            <h3><?php echo $productObj["ceoName"];?></h3>

            <?php echo $productObj["ceoBody"];?>

    </div>
    <div class="media-text-copy" style="padding-top:20px;">
		<div class="media-text-subtext">
            <table style="width:100%;border-top:thin solid;border-bottom:thin solid;">
                <tbody>
                    <tr style="border-bottom:thin solid;height:50px;">
                        <td style="vertical-align:middle;"><h5>Project name</h5></td>
                        <td style="vertical-align:middle;"><h6><?php echo $productObj["ceoName"];?></h6></td>
                    </tr>
                    <tr style="border-bottom:thin solid;height:50px;">
                        <td style="vertical-align:middle;"><h5>Typology</h5></td>
                        <td style="vertical-align:middle;"><h6><?php 
                        if(count($productTypeArr)>0)
                        foreach($productTypeArr as $k=>$name) echo $name["name"].", "; ?></h6></td>
                    </tr>
                    <tr style="border-bottom:thin solid;height:50px;">
                        <td style="vertical-align:middle;"><h5>Location</h5></td>
                        <td style="vertical-align:middle;"><h6><?php echo $productObj["typeName"];?></h6></td>
                    </tr>
                    <tr style="border-bottom:thin solid;height:50px;">
                        <td style="vertical-align:middle;"><h5>Year</h5></td>
                        <td style="vertical-align:middle;"><h6><?php echo $productObj["ceoStart"]."-".$productObj["ceoEnd"];?></h6></td>
                    </tr>
                    <tr style="border-bottom:thin solid;height:50px;">
                        <td style="vertical-align:middle;"><h5>Status</h5></td>
                        <td style="vertical-align:middle;"><h6><?php echo $productObj["ceoStatus"];?></h6></td>
                    </tr>
                    <tr style="border-bottom:thin solid;height:50px;">
                        <td style="vertical-align:middle;"><h5>Size</h5></td>
                        <td style="vertical-align:middle;"><h6><?php echo $productObj["ceoSize"];?></h6></td>
                    </tr>
                    <tr style="border-bottom:thin solid;height:50px;">
                        <td style="vertical-align:middle;"><h5>Client</h5></td>
                        <td style="vertical-align:middle;"><h6><?php echo $productObj["ceoClient"];?></h6></td>
                    </tr>
                    <tr style="border-bottom:thin solid;height:50px;">
                        <td style="vertical-align:middle;"><h5>Design team</h5></td>
                        <td style="vertical-align:middle;"><h6><?php echo $productObj["ceoTeam"];?></h6></td>
                    </tr>
                </tbody>
            </table>
		</div>
    </div>
</div>