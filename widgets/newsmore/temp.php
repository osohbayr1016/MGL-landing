<div class="wrapperfull">
	<div class="process-slider-outer" style="margin-top:0;">
        <div class="process-slider-wrap owl-carousel">
            <div class="process-slide-item">
                <div class="process-slide-image">
                    <picture>
                    <source type="image/jpeg" srcset="<?php echo newsPicFnc($newsObj["newsID"],$newsObj["newsPic"]);?>">
                    <img src="<?php echo newsPicFnc($newsObj["newsID"],$newsObj["newsPic"]);?>">
                    </picture>
                </div>
            </div>
        </div>


        <div class="progress-circle-wrap">
            <div class="track"></div>
            <div class="halfclip">
                <div class="halfcircle clipped"></div>
            </div>
            <div class="halfcircle fixed"></div>
        </div>

        <div id="process-slider-dots" class="process-slider-trigger-wrap wrapper slider-trigger">
                <button class="trigger-link">1</button>
                <button class="trigger-link">2</button>
                <button class="trigger-link">3</button>
        </div>
    </div>
</div>
<div class="wrapper projectMore">
    <div class=""> <?php echo timeStampFnc($newsObj["createDate"]);?> / <a href="/news/cat/<?php echo $newsObj["newsCatID"];?>"><?php echo $newsObj["newsCat"]; ?></a>

            <h2><?php echo $newsObj["newsTitle"];?></h2>

           <?php echo newsrollTextImg($newsObj["newsBody"]);?>

  </div>
    
</div>

