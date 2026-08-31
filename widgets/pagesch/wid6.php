<div class="wrapper">
	<div class="section-header">
		<h3><?php echo $selSchBody["title"];?></h3>
	</div>
    <div class="image-teaser-wrap owl-carousel image-teaser-carousel">
    		<div class="image-teaser-content">
 	<?php 
			$i = 0;
			if(count($workWidArr[$objs["schID"]]))
			foreach($workWidArr[$objs["schID"]] as $key=>$obj){
				if($i%3==0 && $i>0) echo "</div><div class=\"image-teaser-content\">";
			?>
            <a class="image-teaser-inner" href="/project/<?php echo $obj["ceoID"];?>">
                <picture>
                    <source type="image/jpeg" srcset="<?php echo newsPicFnc($obj["ceoID"],$obj["ceoPic"]);?>">
                    <img src="<?php echo newsPicFnc($obj["ceoID"],$obj["ceoPic"]);?>" alt="<?php echo $obj["ceoName"];?>">
                </picture>

                <div class="image-teaser-copy">
                    <h4><?php echo $obj["ceoName"];?></h4>
                    <span class="image-teaser-subtext"><?php echo $obj["brandName"];?></span>
                    <span class="button button-white"></span>
                </div>
            </a>
            <?php $i++; }?>
			</div>


        
    </div>
    
	
	
</div>