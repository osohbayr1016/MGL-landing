<div class="panel-group" id="accordion">
		<?php 
		$bodyCl = "in";
        foreach($allSchArr as $key=>$obj){
            
            $schImageArr = [];
            if($obj["schImage"]!="")
		        $schImageArr = explode("|",$obj["schImage"]);
        ?>
        <div id="courseSch<?php echo $obj["schID"];?>" class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $obj["schID"];?>"><?php echo "Өдөр ".$obj["schOrder"].". ".$obj["schTitle"];?></a>
                    <a href="/insert/editSch/<?php echo $obj["schID"];?>" class="btn btn-primary btn-xs accessModBtn">Засах</a>
                    <a href="/insert/delSch/<?php echo $obj["schID"];?>" class="btn btn-danger btn-xs accessModBtn">Устгах</a>
                </h5>
            </div>
            <div id="collapse<?php echo $obj["schID"];?>" class="panel-collapse collapse <?php echo $bodyCl;?>">
                <div class="panel-body">
                   <?php echo $obj["schNote"];?>
                   <hr />
                   <h2>Зургууд</h2>
                   <?php
                   if(count($schImageArr)>0)
                   foreach($schImageArr as $key=>$obj){
                   ?>     
                   <img src="<?php echo $obj?>" width="80">              
                    <?php } ?>
                </div>
                
            </div>
        </div>
        <?php
                $bodyCl = "";
        }
        ?>
    </div>