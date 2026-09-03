<div class="ibox">
    <div class="ibox-title">
        <h5>Эхлэл хуудасны слайд</h5>
    </div>
    <div class="ibox-content">
        
        
        <div class="project-list">

            <table class="table table-striped table-bordered table-hover dataTables-example">
                <thead>
                <tr>
                    <th>Зураг</th>
                    <th>Овог нэр</th>
                    <th >Албан тушаал</th>
                    <th > </th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(count($ceoArr)>0)
                foreach($ceoArr as $key=>$obj){
                ?>
                <tr>
                	<td >
                        <img src="<?php echo admPicUrl("ceo",$obj["ceoID"].".jpg");?>" width="100" />
                    </td>
                    <td >
                        <strong><?php echo $obj["ceoName"];?></strong>
                        <div><?php echo $obj["ceoDegree"];?></div>
                    </td>
                    <td>
                        <?php echo $obj["ceoPosition"];?>
                    </td>
                    <td class="project-actions">                                        
                        <a href="<?php echo "/insert/aboutCeo/".$obj["ceoID"]."?menuID=".$selMenuID;?>" class="btn btn-warning btn-sm  accessModBtn"><i class="fa fa-pencil"></i></a>
                        <a href="#" class="btn btn-danger btn-sm accessModBtn"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <a href="/insert/aboutCeo/<?php echo "?menuID=".$selMenuID;?>" class="btn btn-primary accessModBtn">Удирдах зөвлөлийн гишүүн нэмэх</a>
    </div>
</div>