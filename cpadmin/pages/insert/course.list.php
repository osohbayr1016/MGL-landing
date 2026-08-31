<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Аялалын жагсаалт</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/">Эхлэл</a>
            </li>
            <li>
                <a href="/insert/course">Аялал оруулах</a>
            </li>
            <li class="active">
                <strong>Аялалын жагсаалт</strong>
            </li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInUp">
<div class="row">
	<div class="col-md-3">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <div class="file-manager">
                    <a href="/insert/courseAdd/<?php echo $catID;?>" class="btn btn-primary btn-block">Шинэ аялал нэмэх</a>
                    <div class="hr-line-dashed"></div>
                    <h5>Ангилал</h5>
                    <ul class="folder-list" style="padding: 0">
                    	<li <?php if($catID<1) echo "class=\"active\"";?>><a href="/insert/course/0"><i class="fa fa-folder"></i> Бүх аялал</a></li>
                    <?php
                    if(count($typesArr)>0)
					foreach($typesArr as $key=>$obj){
					?>
                        <li <?php if($obj["id"]==$catID) echo "class=\"active\"";?>><a href="<?php echo "/insert/course/".$obj["id"];?>"><i class="fa fa-folder"></i> <?php echo $obj["name"];?></a></li>
					<?php } ?>
                    </ul>
                    
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-9">
    
        <div class="ibox">
            <div class="ibox-title">
                <h5>"<?php echo $selCatTitle;?>" ангиллын жагсаалт</h5>
            </div>
            <div class="ibox-content">
                
				
                <div class="project-list">

                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>Статус</th>
                            <th>Зураг</th>
                            <th>Гарчиг</th>
                            <th>Эхлэх үнэ</th>
                            <th>Товч тайлбар</th>
                            <th > </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(count($newsArr)>0)
                        foreach($newsArr as $key=>$obj){
                        ?>
                        <tr>
                            <td >
                                <?php if($obj["isPublish"]!="y"){?>
                                    <span class="label label-danger">Түр хаасан</span>
                                    <?php }
                                    else{
                                    ?>
                                    <span class="label label-success">Нээлттэй</span>
                                    <?php }?>
                            </td>
                            <td class="project-title">
                            <img src="<?php echo newsPicFnc($obj["courseID"],$obj["coursePic"],"y");?>" height="50" />
                            </td>
                            <td >
                                <?php echo $obj["courseTitle"];?>
                            </td>
                            <td>
                                <?php echo number_format($obj["tourPrice"]);?>$
                            </td>
                            <td>
                                <?php echo $obj["courseNote"];?>
                            </td>
                            <td class="project-actions">                                        
                                <a href="<?php echo "/insert/courseEdit/".$obj["courseID"];?>" class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i></a>
                                <a href="<?php echo "/insert/courseDel/".$obj["courseID"];?>" class="btn btn-danger btn-sm accessModBtn"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <a href="/insert/courseAdd/<?php echo $catID;?>" class="btn btn-primary">Шинэ аялал нэмэх</a>
            </div>
        </div>
            
    </div>
</div>
</div>
<div class="modal inmodal" id="orderModalFrm" tabindex="-1" role="dialog" aria-hidden="true">
    
</div>