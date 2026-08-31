<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Цэс</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/">Эхлэл</a>
            </li>
            <li>
                <a href="/info">Лавлах</a>
            </li>
            <li class="active">
                <strong>Цэс</strong>
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
                        <h5>Байрлал</h5>
                        <ul class="folder-list" style="padding: 0">
                        <?php
                        if(count($gloMenuType)>0)
                        foreach($gloMenuType as $key=>$obj){
                        ?>
                            <li <?php if($key==$mainType) echo "class=\"active\"";?>><a href="<?php echo "/info/menus/".$key."/0/";?>"><i class="fa fa-folder"></i> <?php echo $obj;?></a></li>
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
                    <h5>"<?php echo $gloMenuType[$mainType];?>" цэс жагсаалт</h5>
                    <div class="ibox-tools">
                        <a href="/info/menuEdit/<?php echo $mainType;?>/0" class="btn btn-primary btn-xs accessModBtn">Шинэ цэс нэмэх</a>
                    </div>
                </div>
                <div class="ibox-content">
                    

                    <div class="project-list">

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Эрэмбэ</th>
                                <th>Нэр</th>
                                <th>Түлхүүр үг</th>
                                <th>Дэд ангилал</th>
                                <th> </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(count($allTypesArr)>0)
                            foreach($allTypesArr as $key=>$obj){
                            ?>
                            <tr id="cat<?php echo $obj["id"]?>">
                                <td >
                                    <?php echo $obj["order"];?>
                                </td>
                                <td class="project-title">
                                    <?php echo $obj["name"];?>
                                </td>
                                
                                <td >
                                    <?php echo $obj["typeKeys"];?>
                                </td>
                                <td >                                        
                                    <a href="<?php echo "/info/submenuadd/".$mainType."/".$obj["id"];?>" class="btn btn-white btn-sm accessModBtn"><i class="fa fa-folder"></i> Дэд цэс </a>
                                </td>
                                <td class="project-actions">                                        
                                    <a href="<?php echo "/info/menuEdit/".$mainType."/".$obj["id"];?>" class="btn btn-warning btn-sm accessModBtn"><i class="fa fa-pencil"></i> засах </a>
                                    <a href="<?php echo "/info/menuDelete/".$mainType."/".$obj["id"];?>" class="btn btn-danger btn-sm accessModBtn"><i class="fa fa-trash"></i> устгах </a>
                                </td>
                            </tr>
                            <?php 
                            if(count($obj["sub"])>0)
                            foreach($obj["sub"] as $keys=>$objs){
                            ?>
                            <tr id="cat<?php echo $objs["id"]?>">
                                <td style="padding-left:20px;">
                                    <?php echo $objs["order"];?>
                                </td>
                                <td style="padding-left:20px;">
                                    <?php echo $objs["name"];?>
                                </td>
                                
                                <td >
                                    <?php echo $objs["typeKeys"];?>
                                </td>
                                <td >                                        
                                    <a href="<?php echo "/info/submenuadd/".$mainType."/".$objs["id"];?>" class="btn btn-white btn-sm accessModBtn"><i class="fa fa-folder"></i> Дэд цэс</a>
                                </td>
                                <td class="project-actions">                                        
                                    <a href="<?php echo "/info/menuEdit/".$mainType."/".$objs["id"];?>" class="btn btn-warning btn-sm accessModBtn"><i class="fa fa-pencil"></i> засах </a>
                                    <a href="<?php echo "/info/menuDelete/".$mainType."/".$objs["id"];?>" class="btn btn-danger btn-sm accessModBtn"><i class="fa fa-trash"></i> устгах </a>
                                </td>
                            </tr>
                            <?php 
                            if(count($objs["sub"])>0)
                            foreach($objs["sub"] as $keyss=>$objss){
                            ?>
                            <tr>
                                <td style="padding-left:30px;">
                                    <?php echo $objss["order"];?>
                                </td>
                                <td style="padding-left:30px;">
                                    <?php echo $objss["name"];?>
                                </td>
                                
                                <td >
                                    <?php echo $objss["typeKeys"];?>
                                </td>
                                <td >                                        
                                    
                                </td>
                                <td class="project-actions">                                        
                                    <a href="<?php echo "/info/menuEdit/".$mainType."/".$objss["id"];?>" class="btn btn-warning btn-sm accessModBtn"><i class="fa fa-pencil"></i> засах </a>
                                    <a href="<?php echo "/info/menuDelete/".$mainType."/".$objss["id"];?>" class="btn btn-danger btn-sm accessModBtn"><i class="fa fa-trash"></i> устгах </a>
                                </td>
                            </tr>
                            <?php } } } ?>
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