<div class="wheretobuy">
    <div class="wherecontent">
        <h6 class="left-black"><?php echo $selSchBody["title"];?></h6>
        <div class="horz-line dark"></div>
        <div class="row companies">
            <?php
            $i = 0;
            if(count($objs["sub"])>0)
            foreach($objs["sub"] as $key=>$obj){

                $subSchBody = json_decode($obj["schNote"],true);

                
            ?>
            <div class="col-33">
                <p><strong><?php echo $subSchBody["title"];?></strong> <br><?php echo $subSchBody["address"];?></p>
                <p>Tel:<?php echo $subSchBody["phone"];?> <br>E-mail: <a href="mailto:<?php echo $subSchBody["email"];?>" target="_blank" rel="noopener"><?php echo $subSchBody["email"];?></a> <br>Web: <a href="<?php echo $subSchBody["web"];?>" target="_blank" rel="noopener"><?php echo $subSchBody["web"];?></a></p>
                <?php if($subSchBody["loc"]!=""){?>
                <p><strong>Showrooms</strong></p>
                <div class="map-link">
                    <img src="/assets/images/location-dark.svg" alt="map marker"> 
                    <a href="<?php echo $subSchBody["loc"];?>" target="blank">
                     <?php echo $subSchBody["title"];?>
                    </a>
                </div>
                <?php   } ?>
            </div>
            <?php $i++;  } ?>
        </div>
    </div>
</div>