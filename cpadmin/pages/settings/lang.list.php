<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Хэлний сонголт</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/">Эхлэл</a>
            </li>
            <li>
                <a href="/settings">Тохиргоо</a>
            </li>
            <li class="active">
                <strong>Хэлний сонголт</strong>
            </li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInUp">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Сайтны хэлний жагсаалт</h5>
            <div class="ibox-tools">
                <a href="/settings/langEdit/0" class="btn btn-primary btn-xs accessModBtn">Шинэ хэлний сонголт нэмэх</a>
            </div>
        </div>
        <div class="ibox-content">
            

            <div class="project-list">

                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Нэр</th>
                        <th> </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(count($langArr)>0)
                    foreach($langArr as $key=>$obj){
                    ?>
                    <tr id="cat<?php echo $obj["langID"]?>">
                        <td >
                            <?php echo $obj["langKey"];?>
                        </td>
                        <td class="project-title">
                            <?php echo $obj["langName"];?>
                        </td>
                        <td class="project-actions">                                        
                            <a href="<?php echo "/settings/langEdit/".$obj["langID"];?>" class="btn btn-warning btn-sm accessModBtn"><i class="fa fa-pencil"></i> засах </a>
                            <a href="<?php echo "/settings/langDelete/".$obj["langID"];?>" class="btn btn-danger btn-sm accessModBtn"><i class="fa fa-trash"></i> устгах </a>
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