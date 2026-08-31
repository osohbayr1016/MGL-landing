
<div class="ibox float-e-margins">
    <div class="ibox-title">
        <h5>"<?php echo $selMenuObj["name"];?>" хуудасны агуулгын жагсаалт</h5>
        <div class="ibox-tools">
            <a href="/insert/pageSch/<?php echo $selMenuID;?>" class="btn btn-primary btn-xs accessModBtn">Шинэ агуулга нэмэх</a>
        </div>
    </div>
    <div class="ibox-content">                        
        
        <div class="panel-body">
            <div id="courseListID">
            <?php include "page.sch.list.php"; ?>
            </div>
        </div>
        
        
        
    </div>
</div>