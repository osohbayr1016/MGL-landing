<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-8">
        <h2><?php echo RegistrationCore::esc($subTypeObj["subLabel"]);?></h2>
        <ol class="breadcrumb">
            <li><a href="/">Эхлэл</a></li>
            <li><a href="/registration/list">Арга хэмжээний бүртгэл</a></li>
            <li><a href="/registration/design">Хуудасны дизайн</a></li>
            <li class="active"><strong><?php echo RegistrationCore::esc($subTypeObj["label"]);?></strong></li>
        </ol>
    </div>
    <div class="col-sm-4 text-right" style="padding-top:22px;">
        <a href="/registration/design" class="btn btn-white"><i class="fa fa-angle-left"></i> Буцах</a>
        <a href="/registration/subEdit/0?parent=<?php echo (int)$subParentID;?>" class="btn btn-primary regModBtn">
            <i class="fa fa-plus"></i> Нэмэх
        </a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp">
    <div class="ibox">
        <div class="ibox-title">
            <h5><?php echo RegistrationCore::esc($subTypeObj["label"]);?> — дэд мөрүүд</h5>
        </div>
        <div class="ibox-content">

            <?php if(count($subRows)<1){ ?>
            <div class="alert alert-warning">Дэд мөр алга байна. "Нэмэх" товчоор эхэлнэ үү.</div>
            <?php } else { ?>

            <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th style="width:80px">Эрэмбэ</th>
                    <th style="width:90px">Зураг</th>
                    <th>Агуулга</th>
                    <th style="width:150px"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($subRows as $obj){
                    $data = RegistrationCore::decode($obj["blockData"]);

                    $line = "";
                    foreach(array("label","caption","value","icon") as $pk){
                        if(isset($data[$pk]) && trim(strip_tags($data[$pk]))!=""){
                            $line = trim(strip_tags($data[$pk]));
                            break;
                        }
                    }
                    $second = isset($data["value"]) && $data["value"]!=$line ? trim(strip_tags($data["value"])) : "";
                    $pic = isset($data["pic"]) ? $data["pic"] : "";
                ?>
                <tr id="regSubRow<?php echo (int)$obj["blockID"];?>">
                    <td>
                        <a href="#" class="btn btn-xs btn-white regSubMoveBtn" data-id="<?php echo (int)$obj["blockID"];?>" data-dir="up"><i class="fa fa-angle-up"></i></a>
                        <a href="#" class="btn btn-xs btn-white regSubMoveBtn" data-id="<?php echo (int)$obj["blockID"];?>" data-dir="down"><i class="fa fa-angle-down"></i></a>
                    </td>
                    <td>
                        <?php if($pic!=""){ ?>
                        <img src="<?php echo RegistrationCore::esc(newsPicFnc(0,$pic));?>" alt="" style="max-height:46px;border:1px solid #e5e5e5">
                        <?php } elseif(isset($data["icon"]) && $data["icon"]!=""){ ?>
                        <i class="<?php echo RegistrationCore::esc($data["icon"]);?>" style="font-size:22px;color:#676a6c"></i>
                        <?php } ?>
                    </td>
                    <td>
                        <strong><?php echo RegistrationCore::esc($line);?></strong>
                        <?php if($second!=""){ ?><br><small class="text-muted"><?php echo RegistrationCore::esc($second);?></small><?php } ?>
                    </td>
                    <td class="text-right">
                        <a href="/registration/subEdit/<?php echo (int)$obj["blockID"];?>" class="btn btn-xs btn-warning regModBtn">
                            <i class="fa fa-pencil"></i> Засах
                        </a>
                        <a href="#" class="btn btn-xs btn-danger regSubDelBtn" data-id="<?php echo (int)$obj["blockID"];?>">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>

            <?php } ?>

        </div>
    </div>
</div>

<div class="modal inmodal" id="orderModalFrm" tabindex="-1" role="dialog" aria-hidden="true"></div>
