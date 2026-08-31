<section class="text-img-section">
    <div class="text-img fadeInLeft">
        <div class="text">
            <div class="wrap">
                <h2><?php echo $selSchBody["title"];?></h2>
                <?php echo $selSchBody["body"];?>
            </div>
        </div>
        <div class="img fadeInRight">
            <div class="koda-slider-outer">
                <div class="process-slider-wrap owl-carousel">
                    <?php
                    $i = 0;
                    if(count($objs["sub"])>0)
                    foreach($objs["sub"] as $key=>$obj){

                        $subSchBody = json_decode($obj["schNote"],true);

                        if($subSchBody["vid"]!="" || $subSchBody["pic"]!=""){

                            
                        $imgSlide = false;
                        
                        if($subSchBody["vid"]=="")
                            $imgSlide = true;
                    ?>
                        
                
                                    
                    <div class="process-slide-item" data-hash="hslide-<?php echo $key?>">
                    <?php
                    if($imgSlide){
                    ?>
                        <div class="process-slide-image process-banner-image process-slider-image ">
                            <div class="img-overlay "></div>
                            <picture>
                                <source type="image/jpeg" srcset="<?php echo newsPicFnc(0,$subSchBody["pic"]);?>">
                                <img src="<?php echo newsPicFnc(0,$subSchBody["pic"]);?>" alt="<?php echo $subSchBody["title"];?>">
                            </picture>
                        </div>
                    <?php 
                    }
                    else{
                    ?>
                        <div class="process-slide-image process-banner-image process-slider-video  video-available ">
                            <div class="img-overlay hidden"></div>
                            <video class="lozad" autoplay muted loop playsinline>
                                <source src="<?php echo newsPicFnc(0,$subSchBody["vid"]);?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    <?php } ?>   
                        
                        <a href="<?php echo $subSchBody["link"];?>">
                            <div class="process-slide-title wrapper">
                                <?php echo $subSchBody["title"];?>
                            </div>
                        </a>
                    </div>
                    <?php $i++; } } ?>
                    
                </div>
            </div>
        </div>
    </div>
</section>