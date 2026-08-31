<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Widget жагсаалт</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/">Эхлэл</a>
            </li>
            <li>
                <a href="/info">Лавлах</a>
            </li>
            <li class="active">
                <strong>Лавлахын ангилал</strong>
            </li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInUp">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Widget жагсаалт</h5>
            <div class="ibox-tools">
                <a href="/info/widgetEdit/0" class="btn btn-primary btn-xs accessModBtn">Нэмэх</a>
            </div>
        </div>
        <div class="ibox-content">
            

            <div class="project-list">

                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Нэр</th>
                        <th>Түлхүүр үг</th>
                        <th>Дэд агуулгатай эсэх</th>
                        <th> </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(count($widgetArr)>0)
                    foreach($widgetArr as $key=>$obj){
                    ?>
                    <tr id="cat<?php echo $obj["id"]?>">
                        <td class="project-title">
                            <?php echo $obj["widgetTitle"];?>
                        </td>                        
                        <td >
                            wid<?php echo $obj["id"];?>
                        </td>
                        <td >                                        
                            <label class="success"><?php echo $obj["widgetSub"];?></label>
                        </td>
                        <td class="project-actions">                                        
                            <a href="<?php echo "/info/widgetEdit/".$obj["id"];?>" class="btn btn-warning btn-sm accessModBtn"><i class="fa fa-pencil"></i> засах </a>
                        </td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
        
<div class="modal inmodal" id="orderModalFrm" tabindex="-1" role="dialog" aria-hidden="true">
    
</div>