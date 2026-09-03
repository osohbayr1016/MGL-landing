<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-7">
        <h2>Бүртгэлийн жагсаалт</h2>
        <ol class="breadcrumb">
            <li><a href="/">Эхлэл</a></li>
            <li class="active"><strong>Арга хэмжээний бүртгэл</strong></li>
        </ol>
    </div>
    <div class="col-sm-5 text-right" style="padding-top:22px;">
        <form action="/userPost/registration" method="post" target="_blank" style="display:inline">
            <input type="hidden" name="frmPost" value="regEditLink">
            <button type="submit" class="btn btn-warning" title="Хуудсаа нээж, текст дээр нь шууд дарж засна"><i class="fa fa-magic"></i> Хуудсан дээр нь засах</button>
        </form>
        <a href="/registration/export?type=xlsx<?php if($regQ!="") echo "&q=".urlencode($regQ);?>" class="btn btn-primary">
            <i class="fa fa-file-excel-o"></i> Excel татах (.xlsx)
        </a>
        <a href="/registration/export?type=csv<?php if($regQ!="") echo "&q=".urlencode($regQ);?>" class="btn btn-white">
            <i class="fa fa-download"></i> CSV
        </a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp">

    <div class="row">
        <div class="col-lg-3 col-sm-6">
            <div class="ibox">
                <div class="ibox-content">
                    <h5>Нийт бүртгэл</h5>
                    <h1 class="no-margins"><?php echo $regAllCount;?></h1>
                    <small><?php if($regLimit>0){ echo "Багтаамж: ".$regLimit; } else { echo "Багтаамж хязгааргүй"; }?></small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="ibox">
                <div class="ibox-content">
                    <h5>Өнөөдөр</h5>
                    <h1 class="no-margins"><?php echo $regToday;?></h1>
                    <small>сүүлийн 24 цагт биш, өнөөдрийн огноогоор</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="ibox">
                <div class="ibox-content">
                    <h5>Төлөв</h5>
                    <h1 class="no-margins" style="color:<?php echo $regStatus["open"] ? "#1ab394" : "#ed5565";?>">
                        <?php echo $regStatus["open"] ? "Нээлттэй" : "Хаалттай";?>
                    </h1>
                    <small><a href="/registration/settings">Тохиргоо өөрчлөх</a></small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="ibox">
                <div class="ibox-content">
                    <h5>Хуудасны хаяг</h5>
                    <div class="input-group" style="margin-top:8px">
                        <input type="text" id="regPageLink" class="form-control input-sm" readonly value="<?php echo RegistrationCore::esc($regPageLink);?>">
                        <span class="input-group-btn">
                            <button class="btn btn-sm btn-white" type="button" id="regCopyLink">Хуулах</button>
                        </span>
                    </div>
                    <small>QR код үүсгэхэд энэ хаягийг ашиглана</small>
                </div>
            </div>
        </div>
    </div>

    <div class="ibox">
        <div class="ibox-title">
            <h5>Бүртгүүлсэн хүмүүс <span class="badge badge-primary"><?php echo $regTotal;?></span></h5>
            <div class="ibox-tools">
                <form method="get" action="/registration/list" class="form-inline" style="display:inline-block">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control input-sm" placeholder="Нэр, утас, и-мэйл..." value="<?php echo RegistrationCore::esc($regQ);?>">
                        <span class="input-group-btn">
                            <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-search"></i></button>
                            <?php if($regQ!=""){ ?>
                            <a class="btn btn-sm btn-white" href="/registration/list">Цэвэрлэх</a>
                            <?php } ?>
                        </span>
                    </div>
                </form>
            </div>
        </div>
        <div class="ibox-content">

            <?php if(count($regRows)<1){ ?>
            <div class="text-center" style="padding:40px 0;color:#888">
                <?php echo $regQ!="" ? "Хайлтад тохирох бүртгэл олдсонгүй." : "Одоогоор бүртгэл алга байна.";?>
            </div>
            <?php } else { ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th style="width:50px">№</th>
                        <th>Нэр</th>
                        <th style="width:130px">Утас</th>
                        <th>И-мэйл</th>
                        <?php foreach($regExtraCols as $obj){ ?>
                        <th><?php echo RegistrationCore::esc($obj["fieldLabel"]);?></th>
                        <?php } ?>
                        <th style="width:145px">Огноо</th>
                        <th style="width:60px"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $rowNo = $regOffset;
                    foreach($regRows as $obj){
                        $rowNo++;
                        $extra = RegistrationCore::decode($obj["entryData"]);
                    ?>
                    <tr id="regRow<?php echo (int)$obj["entryID"];?>">
                        <td><?php echo $rowNo;?></td>
                        <td><strong><?php echo RegistrationCore::esc($obj["entryName"]);?></strong></td>
                        <td><?php echo RegistrationCore::esc($obj["entryPhone"]);?></td>
                        <td><?php echo RegistrationCore::esc($obj["entryEmail"]);?></td>
                        <?php
                        foreach($regExtraCols as $colObj){
                            $val = isset($extra[$colObj["fieldKey"]]) ? $extra[$colObj["fieldKey"]] : "";
                            if(is_array($val)) $val = implode(", ", $val);
                        ?>
                        <td><?php echo RegistrationCore::esc($val);?></td>
                        <?php } ?>
                        <td><small><?php echo RegistrationCore::esc($obj["entryDate"]);?></small></td>
                        <td class="text-right">
                            <a href="#" class="btn btn-xs btn-danger regDelBtn" data-id="<?php echo (int)$obj["entryID"];?>"
                               data-name="<?php echo RegistrationCore::esc($obj["entryName"]);?>"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if($regPageCount>1){ ?>
            <ul class="pagination">
                <?php for($i=1;$i<=$regPageCount;$i++){ ?>
                <li class="<?php if($i==$regPage) echo "active";?>">
                    <a href="/registration/list?p=<?php echo $i;?><?php if($regQ!="") echo "&q=".urlencode($regQ);?>"><?php echo $i;?></a>
                </li>
                <?php } ?>
            </ul>
            <?php } ?>

            <?php } ?>

        </div>
    </div>
</div>

<div class="modal inmodal" id="orderModalFrm" tabindex="-1" role="dialog" aria-hidden="true"></div>
