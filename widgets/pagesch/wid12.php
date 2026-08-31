<div class="wrapperfull">

        <div class="workfilter filter-container">
			<div class="filterbtns">
			<div class="filterBtn">
					<h6>view all (<?php echo count($workWidArr[$objs["schID"]]);?>)</h6>
				</div>
				<div class="filterBtn">
					<h6 class="filter-collapse-expand  ">filter <span class="filter-collapse-expand-trigger"></span></h6>
				</div>
			</div>
            
			<div class="filter" style="display:none;">
				<form action="">
					<div class="filter-columns">
						<div class="filter-column sector-filter filter-close">
							<div class="filter-title">Sector</div>
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
							<div class="filter-title">Type</div>
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
						<div class="filter-column location-filter filter-close">
							<div class="filter-title">Location</div>
                            <div class="filter-body">
                            <?php
                                foreach($workLocArr as $key=>$obj){
								?>
								<div class="filter-item">
									<input class="locinp filterinp" data-type="loc" id="sector<?php echo $obj["id"];?>" <?php if($locationSel[$obj["id"]]==$obj["id"]) echo "checked=\"checked\"";?> type="checkbox" name="locations[]"  value="<?php echo $obj["id"];?>" >
									<label for="sector<?php echo $obj["id"];?>" ><?php echo $obj["name"];?></label>
								</div>
                                <?php } ?>
                                </div>
						</div>

						<div class="filter-column scale-filter filter-close">
							<div class="filter-title">Scale</div>
                            <div class="filter-body">
												<div class="filter-item">
									<input id="under-100m" type="checkbox" name="scales[]"  value="under-100m" >
									<label for="under-100m" >&lt; 1,000 m²</label>
								</div>
												<div class="filter-item">
									<input id="100m-200m" type="checkbox" name="scales[]"  value="100m-200m" >
									<label for="100m-200m" >1,000 m²–10,000 m²</label>
								</div>
												<div class="filter-item">
									<input id="200m-300m" type="checkbox" name="scales[]"  value="200m-300m" >
									<label for="200m-300m" >10,000 m²–50,000 m²</label>
								</div>
												<div class="filter-item">
									<input id="50-000-100-000" type="checkbox" name="scales[]"  value="50-000-100-000" >
									<label for="50-000-100-000" >50,000 m²–1,000,000 m²</label>
								</div>
												<div class="filter-item">
									<input id="300m-400m" type="checkbox" name="scales[]"  value="300m-400m" >
									<label for="300m-400m" >&gt; 1,000,000 m²</label>
								</div>
                                </div>
										</div>

						<div class="filter-column budget-filter filter-close">
							<div class="filter-title">Status</div>
                            <div class="filter-body">
                            <?php
                                foreach($workStatusArr as $key=>$obj){
									if($obj["ceoStatus"]!=""){
								?>
								<div class="filter-item">
									<input class="statusinp filterinp" data-type="status" id="statusWork<?php echo $key;?>" <?php if($sectorsSel[$obj["ceoStatus"]]==$obj["ceoStatus"]) echo "checked=\"checked\"";?> type="checkbox" name="budgets[]"  value="<?php echo $obj["ceoStatus"];?>" >
									<label for="statusWork<?php echo $key;?>" ><?php echo $obj["ceoStatus"];?></label>
								</div>
                                <?php } } ?>
                                </div>
						</div>

					</div>

					<div class="filter-actions" style="display:none;">
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
					if($obj["ceoType"]!="")
						$dataSectorArr = explode("|",$obj["ceoType"]);
					
					$dataTypeArr = "";
					if($obj["proType"]!="")
						$dataTypeArr = explode("|",$obj["proType"]);
					
                ?>
				<div class="project-listing-item project-sector" data-id="<?php echo $obj["ceoID"]?>" data-loc="<?php echo $obj["ceoCat"];?>" data-status="<?php echo $obj["ceoStatus"];?>" <?php if(count($dataSectorArr)>0)foreach($dataSectorArr as $k=>$sector){if($sector!=""){?> data-sector="<?php echo $sector;?>"<?php } } ?> <?php if(count($dataTypeArr)>0)foreach($dataTypeArr as $k=>$sector){if($sector!=""){?> data-type="<?php echo $sector;?>"<?php } } ?> >
					<a href="/project/<?php echo $obj["ceoID"];?>">
						<div class="project-listing-item-inner">
							<div class="project-listing-teaser-image">
								<picture>
									<source type="image/jpeg" srcset="<?php echo newsPicFnc($obj["ceoID"],$obj["ceoPic"]);?>">
                    				<img src="<?php echo newsPicFnc($obj["ceoID"],$obj["ceoPic"]);?>" alt="<?php echo $obj["ceoName"];?>">
								</picture>
							</div>
							<div class="project-listing-teaser-content">
								<h4 class="project-teaser-name"><?php echo $obj["ceoName"];?></h4>
								<div class="project-teaser-location">
                                       <?php echo $obj["typeName"];?>
                                </div>
							</div>
						</div>
					</a>
				</div>
                <?php $i++; }?>
                
			</div>


        </div>

	</div>