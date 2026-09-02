<div class="panel-group" id="accordion">
		<?php 

		$bodyCl = "in";
        foreach($allSchArr as $key=>$obj){
            
            $selSchBody = json_decode($obj["schNote"],true);
        ?>
        <div id="courseSch<?php echo $obj["schID"];?>" class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">
                    <span class="inline-order-wrap" style="display:inline-block;margin-right:8px;vertical-align:middle;">
                        <input type="number"
                            min="1"
                            class="form-control input-sm js-order-input"
                            data-post="schOrderSet"
                            data-sch-id="<?php echo $obj["schID"];?>"
                            data-sch-key="<?php echo $obj["schKey"];?>"
                            data-parent-id="0"
                            value="<?php echo (int)$obj["schOrder"];?>"
                            title="Дэс дугаар"
                            style="width:72px;display:inline-block;">
                    </span>
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $obj["schID"];?>"><?php echo "АГУУЛГА ".$obj["schOrder"].". ".$selSchBody["title"]." (".$obj["widgetTitle"].")";?></a>
                    <a href="/insert/editPageSch/<?php echo $obj["schID"];?>" class="btn btn-primary btn-xs accessModBtn">Засах</a>
                    <a href="/insert/delPageSch/<?php echo $obj["schID"];?>" class="btn btn-danger btn-xs accessModBtn">Устгах</a>
                    <?php
                    if($obj["widgetSub"]){
                    ?>
                    <a href="/insert/pageSubSch/<?php echo $obj["schID"];?>" class="btn btn-warning btn-xs accessModBtn">Агуулга нэмэх</a>
                    <?php } ?>
                </h5>
            </div>
            <div id="collapse<?php echo $obj["schID"];?>" class="panel-collapse collapse <?php echo $bodyCl;?>">
                <div class="panel-body">
                	<strong><?php echo $obj["schSub"];?></strong>
                   <?php
                    if($obj["widgetSub"]){
                    ?>
                   <hr />
                   <h2>Агуулга</h2>
                   
                   <div id="subSch<?php echo $obj["schID"];?>" style="padding-left:30px;">
					<?php 
                    $subSchList = $obj["sub"];
                    include "page.sch.sub.list.php";
                    ?>
                    </div>
                    <?php } ?>
                   
                
                </div>
                
            </div>
        </div>
        <?php
                $bodyCl = "";
        }
        ?>
    </div>