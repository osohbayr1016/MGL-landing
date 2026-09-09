<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-8">
        <h2>Формын асуулт</h2>
        <ol class="breadcrumb">
            <li><a href="/">Эхлэл</a></li>
            <li><a href="/internship/list">Дадлага</a></li>
            <li class="active"><strong>Формын асуулт</strong></li>
        </ol>
    </div>
    <div class="col-sm-4 text-right" style="padding-top:22px;">
        <a href="/internship/fieldEdit/0" class="btn btn-primary applyModBtn"><i class="fa fa-plus"></i> Асуулт нэмэх</a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Өргөдлийн формын талбарууд</h5>
        </div>
        <div class="ibox-content">
            <p class="text-muted">
                Энд нэмсэн асуулт бүр вэб сайтын өргөдлийн формд гарч, Excel файлд бас
                тусдаа багана болж орно. "Хаана гарах" баганаас тухайн асуултыг зөвхөн
                дадлагын, зөвхөн ажлын байрны, эсвэл хоёуланд нь харуулахыг сонгоно.
                Нэр / Утас / И-мэйл / Ажлын байр дөрөв нь үндсэн талбар тул устгагдахгүй
                (гэхдээ нэрийг нь өөрчилж, эрэмбийг нь солиж болно).
            </p>

            <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th style="width:80px">Эрэмбэ</th>
                    <th>Асуулт</th>
                    <th style="width:120px">Түлхүүр</th>
                    <th style="width:150px">Төрөл</th>
                    <th style="width:140px">Хаана гарах</th>
                    <th style="width:80px">Заавал</th>
                    <th style="width:80px">Өргөн</th>
                    <th style="width:90px">Төлөв</th>
                    <th style="width:130px"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($applyFieldRows as $obj){
                    $isCore = $obj["fieldCore"]!="";
                ?>
                <tr id="applyFieldRow<?php echo (int)$obj["fieldID"];?>"<?php if((int)$obj["fieldStatus"]!=1) echo ' style="opacity:.55"';?>>
                    <td>
                        <a href="#" class="btn btn-xs btn-white applyFieldMoveBtn" data-id="<?php echo (int)$obj["fieldID"];?>" data-dir="up"><i class="fa fa-angle-up"></i></a>
                        <a href="#" class="btn btn-xs btn-white applyFieldMoveBtn" data-id="<?php echo (int)$obj["fieldID"];?>" data-dir="down"><i class="fa fa-angle-down"></i></a>
                    </td>
                    <td>
                        <strong><?php echo ApplyCore::esc($obj["fieldLabel"]);?></strong>
                        <?php if($isCore){ ?><span class="label label-info">үндсэн</span><?php } ?>
                    </td>
                    <td><code><?php echo ApplyCore::esc($obj["fieldKey"]);?></code></td>
                    <td><?php echo isset($applyFieldTypes[$obj["fieldType"]]) ? ApplyCore::esc($applyFieldTypes[$obj["fieldType"]]) : ApplyCore::esc($obj["fieldType"]);?></td>
                    <td><?php echo isset($applyFieldFor[$obj["fieldFor"]]) ? ApplyCore::esc($applyFieldFor[$obj["fieldFor"]]) : "Хоёуланд нь";?></td>
                    <td><?php echo (int)$obj["fieldRequired"]==1 ? '<span class="label label-primary">Тийм</span>' : '<span class="label label-default">Үгүй</span>';?></td>
                    <td>
                        <?php
                        $wMap = array("full"=>"Бүтэн","half"=>"1/2");
                        echo isset($wMap[$obj["fieldWidth"]]) ? $wMap[$obj["fieldWidth"]] : "Бүтэн";
                        ?>
                    </td>
                    <td><?php echo (int)$obj["fieldStatus"]==1 ? '<span class="label label-primary">Идэвхтэй</span>' : '<span class="label label-default">Унтраасан</span>';?></td>
                    <td class="text-right">
                        <a href="/internship/fieldEdit/<?php echo (int)$obj["fieldID"];?>" class="btn btn-xs btn-warning applyModBtn"><i class="fa fa-pencil"></i> Засах</a>
                        <?php if(!$isCore){ ?>
                        <a href="#" class="btn btn-xs btn-danger applyFieldDelBtn" data-id="<?php echo (int)$obj["fieldID"];?>"><i class="fa fa-trash"></i></a>
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

<div class="modal inmodal" id="orderModalFrm" tabindex="-1" role="dialog" aria-hidden="true"></div>
