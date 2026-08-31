<div class="wrapperfull">

        <div class="workfilter filter-container">
			<div class="filterbtns">
				<div class="filterBtn">
					<h6 class="filter-collapse-expand  ">filter <span class="filter-collapse-expand-trigger"></span></h6>
				</div>
			</div>
			<div class="filter" style="display:none;">
				<form action="">
					<div class="filter-columns">
						<div class="filter-column sector-filter filter-close">
							<div class="filter-title">Tools</div>
                            <div class="filter-body">
							<?php
                            foreach($workSecArr as $key=>$obj){
                            ?>
                            <div class="filter-item">
                                <input class="sectorinp filterinp" data-type="sector" id="sector<?php echo $obj["id"];?>" <?php if($sectorsSel[$obj["id"]]==$obj["id"]) echo "checked=\"checked\"";?> type="checkbox" name="sectors[]"  value="<?php echo $obj["id"];?>" >
                                <label for="sector<?php echo $obj["id"];?>" ><?php echo $obj["name"];?></label>
                            </div>
                            <?php } ?>
							</div>
						</div>
                        <div class="filter-column location-filter filter-close">
							<div class="filter-title">Creative Fields</div>
                            	<div class="filter-body">
                            <?php
                                foreach($workTypeArr as $key=>$obj){
								?>
								<div class="filter-item">
									<input class="typeinp filterinp" data-type="type" id="sector<?php echo $obj["id"];?>" <?php if($typesSel[$obj["id"]]==$obj["id"]) echo "checked=\"checked\"";?> type="checkbox" name="types[]"  value="<?php echo $obj["id"];?>" >
									<label for="sector<?php echo $obj["id"];?>" ><?php echo $obj["name"];?></label>
								</div>
                                <?php } ?>
                                </div>
						</div>

					

					</div>

					<div class="filter-actions" style="display:none">
						<a href="/projects">Reset Filters</a>
						<input class="apply-filters" type="submit" value="Apply Filters">
					</div>

				</form>
			</div>
        </div>

        <div class="projects-listing">
			<div class="projects-listing-row ">
				<?php 
                $i = 0;
                if(count($workWidArr[$objs["schID"]]))
                foreach($workWidArr[$objs["schID"]] as $key=>$obj){
					
					$dataSectorArr = "";
					if($obj["visualTools"]!="")
						$dataSectorArr = explode("|",$obj["visualTools"]);
					
					$dataTypeArr = "";
					if($obj["visualFields"]!="")
						$dataTypeArr = explode("|",$obj["visualFields"]);
                ?>
				<div class="project-listing-item project-sector project-listing-anim" data-id="<?php echo $obj["visualID"]?>" <?php if(count($dataSectorArr)>0)foreach($dataSectorArr as $k=>$sector){if($sector!=""){?> data-sector="<?php echo $sector;?>"<?php } } ?> <?php if(count($dataTypeArr)>0)foreach($dataTypeArr as $k=>$sector){if($sector!=""){?> data-type="<?php echo $sector;?>"<?php } } ?>>
					<a href="/visualisation/<?php echo $obj["visualID"];?>">
						<div class="project-listing-item-inner">
							<div class="project-listing-teaser-image">
								<picture>
									<source type="image/jpeg" srcset="/pics/visual/<?php echo $obj["visualID"];?>.jpg">
                    				<img src="/pics/visual/<?php echo $obj["visualID"];?>.jpg" alt="<?php echo $obj["visualTitle"];?>">
								</picture>
							</div>
							<div class="project-listing-teaser-content">
								<div class="protype">
                                       <?php echo $obj["designerName"];?>
                                </div>
							</div>
						</div>
                        <div class="project-listing-show-content">
                       		<div class="projectName">
                                <div class="e"><?php echo $obj["visualTitle"];?></div>
                                <small class="">
                                       <?php echo $obj["designerName"];?>
                                </small>
                            </div>
                           	<div class="projectLike">
                            	<div class="Stats-stats-Q1s"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0.5 0.5 16 16" class="Appreciations-icon-Z4i ProjectCover-icon-QsA ProjectCover-appreciations-hIS"><path fill="none" d="M.5.5h16v16H.5z"></path><path d="M.5 7.5h3v8h-3zM7.207 15.207c.193.19.425.29.677.293H12c.256 0 .512-.098.707-.293l2.5-2.5c.19-.19.288-.457.293-.707V8.5c0-.553-.445-1-1-1h-5L11 5s.5-.792.5-1.5v-1c0-.553-.447-1-1-1l-1 2-4 4v6l1.707 1.707z"></path></svg><span title="1,181">1.2k</span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" class="ProjectCover-icon-QsA ProjectCover-views-Euf"><path d="M8.5 3.5c-5 0-8 5-8 5s3 5 8 5 8-5 8-5-3-5-8-5zm0 7c-1.105 0-2-.896-2-2 0-1.106.895-2 2-2 1.104 0 2 .894 2 2 0 1.104-.896 2-2 2z"></path></svg><span title="8,698">8.7k</span></div>
                            </div>
                        </div>
					</a>
				</div>
                <?php $i++; }?>
                
			</div>


        </div>

	</div>