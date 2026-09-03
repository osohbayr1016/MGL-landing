<div class="visualisationPage">
<div class="wrapper">
	<div class="visualHead">
    	<div class="visualAuthorPic">
        	<div class="authorPic"></div>
        </div>
        <div class="visualAuthor">
            <div><?php echo $productObj["visualTitle"];?></div>
            <span class="eyebrow"><?php echo $productObj["designerName"];?></span>
        </div>
    </div>
	<div class="visualisationMore">
        <div >
                <?php 
                if(count($selProPics)>1)
                foreach($selProPics as $imgi=>$imgurl){
                    if($imgurl!=""){
                ?>
                <div class="visualPic">
                        <picture>
                            <source type="image/jpeg" srcset="<?php echo picUrl("visual",$imgurl);?>">
                            <img src="<?php echo picUrl("visual",$imgurl);?>">
                        </picture>
                </div>
                <?php 
                    }
                }
                else{
                ?>
                <div class="visualPic">
                    <picture>
                    <source type="image/jpeg" srcset="<?php echo picUrl("visual",$productObj["visualID"].".jpg");?>">
                    <img src="<?php echo picUrl("visual",$productObj["visualID"].".jpg");?>" >
                    </picture>
                </div>
                <?php 
                }
                ?>
                <?php 
                if($productObj["visualVideo"]!=""){
                ?>
            	<div class="visualPic">
                	<?php echo newsrollTextImg($productObj["visualVideo"]);?>
                </div>
                <?php 
                }
                ?>
        </div>
		<div class="visualLikeDiv">
        	<div class="fb-like" data-href="<?php echo $gloConstSiteURL;?>visualisation/<?php echo $productObj["visualID"];?>" data-width="" data-layout="box_count" data-action="like" data-size="large" data-share="true"></div>
            <h2><?php echo $productObj["visualTitle"];?></h2>
            <div class="projectLike">
                <div class="Stats-stats-Q1s"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0.5 0.5 16 16" class="Appreciations-icon-Z4i ProjectCover-icon-QsA ProjectCover-appreciations-hIS"><path fill="none" d="M.5.5h16v16H.5z"></path><path d="M.5 7.5h3v8h-3zM7.207 15.207c.193.19.425.29.677.293H12c.256 0 .512-.098.707-.293l2.5-2.5c.19-.19.288-.457.293-.707V8.5c0-.553-.445-1-1-1h-5L11 5s.5-.792.5-1.5v-1c0-.553-.447-1-1-1l-1 2-4 4v6l1.707 1.707z"></path></svg><span title="1,181">1.2k</span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" class="ProjectCover-icon-QsA ProjectCover-views-Euf"><path d="M8.5 3.5c-5 0-8 5-8 5s3 5 8 5 8-5 8-5-3-5-8-5zm0 7c-1.105 0-2-.896-2-2 0-1.106.895-2 2-2 1.104 0 2 .894 2 2 0 1.104-.896 2-2 2z"></path></svg><span title="8,698">8.7k</span></div>
            </div>
            <span class="eyebrow">Published: <?php echo $productObj["visualDate"];?></span>
        </div>
        <div class="visualReleate">
            <div class="section-header">
                <div>Recommend</div>
            </div>
            <div class="image-teaser-wrap owl-carousel image-teaser-carousel">
                <?php 
                $i = 0;
                if(count($randomVisualArr))
                foreach($randomVisualArr as $key=>$obj){
                ?>
                <div class="image-teaser-content">
                    <a class="image-teaser-inner" href="/visualisation/<?php echo $obj["visualID"];?>">
                        <picture>
                            <source type="image/jpeg" srcset="<?php echo picUrl("visual",$obj["visualID"].".jpg");?>">
                            <img src="<?php echo picUrl("visual",$obj["visualID"].".jpg");?>" alt="<?php echo $obj["visualTitle"];?>">
                        </picture>
        
                        <div class="image-teaser-copy">
                            <h4><?php echo $obj["visualTitle"];?></h4>
                            <span class="image-teaser-subtext"><?php echo $obj["designerName"];?></span>
                            <span class="button button-white"></span>
                        </div>
                    </a>
                </div>
            	<?php $i++; }?>                    
            </div>
        </div>
        <div class="visualBody">
            <div class="visualComment">
                <div class="visualCom">
                    <div class="fb-comments" data-href="<?php echo $gloConstSiteURL;?>visualisation/<?php echo $productObj["visualID"];?>" data-width="100%" data-numposts="5"></div>
                </div>
            </div>
            <div class="visualInfo">
            	<div class="visualInfoMore">
                    <b><?php echo $productObj["visualTitle"];?></b>
                    <div class="projectLike">
                        <div class="Stats-stats-Q1s"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0.5 0.5 16 16" class="Appreciations-icon-Z4i ProjectCover-icon-QsA ProjectCover-appreciations-hIS"><path fill="none" d="M.5.5h16v16H.5z"></path><path d="M.5 7.5h3v8h-3zM7.207 15.207c.193.19.425.29.677.293H12c.256 0 .512-.098.707-.293l2.5-2.5c.19-.19.288-.457.293-.707V8.5c0-.553-.445-1-1-1h-5L11 5s.5-.792.5-1.5v-1c0-.553-.447-1-1-1l-1 2-4 4v6l1.707 1.707z"></path></svg><span title="1,181">1.2k</span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" class="ProjectCover-icon-QsA ProjectCover-views-Euf"><path d="M8.5 3.5c-5 0-8 5-8 5s3 5 8 5 8-5 8-5-3-5-8-5zm0 7c-1.105 0-2-.896-2-2 0-1.106.895-2 2-2 1.104 0 2 .894 2 2 0 1.104-.896 2-2 2z"></path></svg><span title="8,698">8.7k</span></div>
                    </div>
                    <span class="eyebrow">Published: <?php echo $productObj["visualDate"];?></span>
                    <div style="padding-top:40px;">
                    	<div class="fb-like" data-href="<?php echo $gloConstSiteURL;?>visualisation/<?php echo $productObj["visualID"];?>" data-width="" data-layout="box_count" data-action="like" data-size="large" data-share="true"></div>
                    </div>
                </div>
                
            </div>
            
        </div>
	</div>
</div>
</div>