<div class="wrapper">
	<div class="section-header">
		<h3><?php echo $selSchBody["title"];?></h3>
	</div>
	<div class="featured-news-wrap owl-carousel">
		<?php 
		if(count($newsWidArr[$objs["schID"]]))
		foreach($newsWidArr[$objs["schID"]] as $key=>$obj){
		?>
		<div class="featured-news-item">
			<div class="featured-news-item-inner">
				<picture>
					<source type="image/jpeg" srcset="<?php echo newsPicFnc($obj["newsID"],$obj["newsPic"]);?>">
					<img src="<?php echo newsPicFnc($obj["newsID"],$obj["newsPic"]);?>" alt="<?php echo $obj["newsTitle"];?>">
				</picture>
				<div class="featured-news-item-content">
					<div class="featured-news-item-category">
						<a href="#"><?php echo $obj["newsCat"]?></a>

					</div>
					<div class="featured-news-item-title">
						<a href="/n/<?php echo $obj["newsID"];?>"><?php echo $obj["newsTitle"];?></a>
					</div>
				</div>
			</div>
		</div>
		
		<?php }?>
		
	</div>
</div>