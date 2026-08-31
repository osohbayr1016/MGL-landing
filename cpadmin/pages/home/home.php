<div class="wrapper wrapper-content">
<div class="row">
    <div class="col-md-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-success pull-right"><?php echo date("m/d");?></span>
                <h5>Өнөөдөр</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins"><?php echo number_format($newsTodaySum);?></h1>
                <div class="stat-percent font-bold text-success">2% <i class="fa fa-bolt"></i></div>
                <small>мэдээ оруулсан</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-info pull-right"><?php echo date("m");?>-р сар</span>
                <h5>Энэ сард</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins"><?php echo number_format($newsMonthSum);?></h1>
                <div class="stat-percent font-bold text-info">20% <i class="fa fa-level-up"></i></div>
                <small>мэдээ оруулсан</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-primary pull-right"><?php echo date("Y");?> он</span>
                <h5>Энэ жил</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins"><?php echo number_format($newsYearSum);?></h1>
                <div class="stat-percent font-bold text-info">20% <i class="fa fa-level-up"></i></div>
                <small>мэдээ оруулсан</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Хандалт</h5>
                <div class="ibox-tools">
                    <span class="label label-primary"><?php echo date("Y");?> он</span>
                </div>
            </div>
            <div class="ibox-content no-padding">
                <div class="flot-chart m-t-lg" style="height: 55px;">
                    <div class="flot-chart-content" id="flot-chart1"></div>
                </div>
            </div>

        </div>
    </div>
</div>


<div class="row">

<div class="col-lg-12">
<div class="ibox float-e-margins">
<div class="ibox-title">
    <h5>Мэдээнд ирсэн сэтгэгдэл </h5>
    <div class="ibox-tools">
        <a class="collapse-link">
            <i class="fa fa-chevron-up"></i>
        </a>
    </div>
</div>
<div class="ibox-content">
    <div>
    <form action="" method="post">
    <div class="input-group">
      <input type="text" name="searchKey" value="<?php echo $searchKey;?>" class="form-control" placeholder="Сэтгэгдэл хайх... Мэдээний ID, IP, Сэтгэгдэл гм">
      <span class="input-group-btn">
        <button class="btn btn-default" type="submit">Хайх!</button>
      </span>
    </div>
    </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Огноо</th>
                <th>Мэдээ</th>
                <th>Сэтгэгдэл</th>
                <th>IP</th>
                <th>Байршил</th>
                <th> </th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($commentArr)>0)
            foreach($commentArr as $key=>$obj){
            ?>
            <tr>
                <td class="project-status">
                    <?php echo timeStampFnc($obj["edate"]);?>
                </td>
                <td class="project-status">
                   <a href="<?php echo "http://www.montimes.mn/v/".$obj["feild_id"];?>" target="_blank"><?php echo $obj["newsTitle"];?></a>
                </td>
                <td class="project-title">
                    <?php echo $obj["comment"];?>
                </td>
                <td >
                    <?php echo $obj["cmdIP"];?>
                </td>
                <td >
                    <?php echo $obj["loc"];?>
                </td>
                <td class="project-actions">
                	<?php
                    if($obj["isDel"]=="n"){
					?>
                    <a href="<?php echo "/home/delComm/".$obj["id"];?>" class="btn btn-danger btn-sm accessModBtn"><i class="fa fa-trash"></i> устгах </a>                                            
                    <? } 
					else{
					?>
                     <a href="<?php echo "/home/resComm/".$obj["id"];?>" class="btn btn-success btn-sm accessModBtn"><i class="fa fa-check"></i> сэргээх </a> 
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

</div>
</div>
</div>

</div>

</div>

<div class="modal inmodal" id="orderModalFrm" tabindex="-1" role="dialog" aria-hidden="true">
    
</div>