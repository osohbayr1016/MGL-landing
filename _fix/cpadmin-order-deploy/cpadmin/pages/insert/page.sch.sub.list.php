<?php 
if(count($subSchList)>0){
?>
<table class="table table-condensed table-bordered sub-sch-order-table">
    <thead>
        <tr>
            <th style="width:90px;">Дэс дугаар</th>
            <th style="width:110px;">Зураг</th>
            <th>Гарчиг</th>
            <th style="width:130px;"></th>
        </tr>
    </thead>
    <tbody>
<?php
foreach($subSchList as $skey=>$sobj){	
    $selSchBody = json_decode($sobj["schNote"],true);
    $thumb = "";
    if(!empty($selSchBody["pic"])){
        $thumb = newsPicFnc(0, $selSchBody["pic"]);
    } elseif(!empty($selSchBody["vid"])){
        $thumb = newsPicFnc(0, $selSchBody["vid"]);
    }
?>
        <tr id="courseSch<?php echo $sobj["schID"];?>">
            <td>
                <input type="number"
                    min="1"
                    class="form-control input-sm js-order-input"
                    data-post="schOrderSet"
                    data-sch-id="<?php echo $sobj["schID"];?>"
                    data-parent-id="<?php echo $sobj["parentID"];?>"
                    value="<?php echo (int)$sobj["schOrder"];?>"
                    style="width:72px;">
            </td>
            <td>
                <?php if($thumb!=""){ ?>
                <img src="<?php echo $thumb;?>" alt="" style="width:96px;height:auto;display:block;">
                <?php } ?>
            </td>
            <td><strong><?php echo $selSchBody["title"];?></strong></td>
            <td>
                <a href="/insert/pageSubSchEdit/<?php echo $sobj["schID"];?>" class="btn btn-primary btn-xs accessModBtn">Засах</a>
                <a href="/insert/pageSubSchDel/<?php echo $sobj["schID"];?>" class="btn btn-danger btn-xs accessModBtn">Устгах</a>
            </td>
        </tr>
<?php
}
?>
    </tbody>
</table>
<?php
}
?>
