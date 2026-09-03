<div class="ibox">
    <div class="ibox-title">
        <h5>Visualisation</h5>
    </div>
    <div class="ibox-content">
        
        
        <div class="project-list">

            <table class="table table-striped table-bordered table-hover dataTables-example">
                <thead>
                <tr>
                    <th>Зураг</th>
                    <th>Бүтээгдэхүүн</th>
                    <th >Tags</th>
                    <th > </th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(count($ceoArr)>0)
                foreach($ceoArr as $key=>$obj){
                ?>
                <tr id="proTr<?php echo $obj["visualID"]?>">
                	<td >
                        <img src="<?php echo admPicUrl("visual",$obj["visualID"].".jpg");?>" width="100" />
                    </td>
                    <td >
                        <strong><?php echo $obj["visualTitle"];?></strong>
                        <div><?php echo $obj["ceoDegree"];?></div>
                    </td>
                    <td>
                        <?php echo $obj["visualTags"];?>
                    </td>
                    <td class="project-actions">                                        
                        <a href="<?php echo "/insert/visualAdd/".$obj["visualID"]."?menuID=".$selMenuID;?>" class="btn btn-warning btn-sm  accessModBtn"><i class="fa fa-pencil"></i></a>
                        <a href="/insert/visualDel/<?php echo $obj["visualID"];?>" class="btn btn-danger btn-sm accessModBtn"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <a href="/insert/visualAdd/0<?php echo "?menuID=".$selMenuID;?>" class="btn btn-primary accessModBtn">Нэмэх</a>
    </div>
</div>