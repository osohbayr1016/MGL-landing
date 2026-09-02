<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-8">
        <h2>Формын талбар</h2>
        <ol class="breadcrumb">
            <li><a href="/">Эхлэл</a></li>
            <li><a href="/registration/list">Арга хэмжээний бүртгэл</a></li>
            <li class="active"><strong>Формын талбар</strong></li>
        </ol>
    </div>
    <div class="col-sm-4 text-right" style="padding-top:22px;">
        <a href="/registration/fieldEdit/0" class="btn btn-primary regModBtn"><i class="fa fa-plus"></i> Талбар нэмэх</a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Бүртгэлийн формын талбарууд</h5>
        </div>
        <div class="ibox-content">
            <p class="text-muted">
                Энд нэмсэн талбар бүр бүртгэлийн хуудсанд гарч, Excel файлд бас
                тусдаа багана болж орно. Нэр / Утас / И-мэйл гурав нь үндсэн талбар тул
                устгагдахгүй (гэхдээ нэрийг нь өөрчилж, эрэмбийг нь солиж болно).
            </p>

            <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th style="width:80px">Эрэмбэ</th>
                    <th>Талбарын нэр</th>
                    <th style="width:130px">Түлхүүр</th>
                    <th style="width:150px">Төрөл</th>
                    <th style="width:90px">Заавал</th>
                    <th style="width:90px">Өргөн</th>
                    <th style="width:90px">Төлөв</th>
                    <th style="width:150px"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($regFieldRows as $obj){
                    $isCore = $obj["fieldCore"]!="";
                ?>
                <tr id="regFieldRow<?php echo (int)$obj["fieldID"];?>"<?php if((int)$obj["fieldStatus"]!=1) echo ' style="opacity:.55"';?>>
                    <td>
                        <a href="#" class="btn btn-xs btn-white regFieldMoveBtn" data-id="<?php echo (int)$obj["fieldID"];?>" data-dir="up"><i class="fa fa-angle-up"></i></a>
                        <a href="#" class="btn btn-xs btn-white regFieldMoveBtn" data-id="<?php echo (int)$obj["fieldID"];?>" data-dir="down"><i class="fa fa-angle-down"></i></a>
                    </td>
                    <td>
                        <strong><?php echo RegistrationCore::esc($obj["fieldLabel"]);?></strong>
                        <?php if($isCore){ ?><span class="label label-info">үндсэн</span><?php } ?>
                    </td>
                    <td><code><?php echo RegistrationCore::esc($obj["fieldKey"]);?></code></td>
                    <td><?php echo isset($regFieldTypes[$obj["fieldType"]]) ? RegistrationCore::esc($regFieldTypes[$obj["fieldType"]]) : RegistrationCore::esc($obj["fieldType"]);?></td>
                    <td><?php echo (int)$obj["fieldRequired"]==1 ? '<span class="label label-primary">Тийм</span>' : '<span class="label label-default">Үгүй</span>';?></td>
                    <td>
                        <?php
                        $wMap = array("full"=>"Бүтэн","half"=>"1/2","third"=>"1/3");
                        echo isset($wMap[$obj["fieldWidth"]]) ? $wMap[$obj["fieldWidth"]] : "Бүтэн";
                        ?>
                    </td>
                    <td><?php echo (int)$obj["fieldStatus"]==1 ? '<span class="label label-primary">Идэвхтэй</span>' : '<span class="label label-default">Унтраасан</span>';?></td>
                    <td class="text-right">
                        <a href="/registration/fieldEdit/<?php echo (int)$obj["fieldID"];?>" class="btn btn-xs btn-warning regModBtn"><i class="fa fa-pencil"></i> Засах</a>
                        <?php if(!$isCore){ ?>
                        <a href="#" class="btn btn-xs btn-danger regFieldDelBtn" data-id="<?php echo (int)$obj["fieldID"];?>"><i class="fa fa-trash"></i></a>
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
