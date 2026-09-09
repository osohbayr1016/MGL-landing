<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-6">
        <h2>Ирсэн хүсэлт</h2>
        <ol class="breadcrumb">
            <li><a href="/">Эхлэл</a></li>
            <li class="active"><strong>Дадлага / ажлын байр</strong></li>
        </ol>
    </div>
    <div class="col-sm-6 text-right" style="padding-top:22px;">
        <a href="/internship/export?type=xlsx<?php echo $applyQuery;?>" class="btn btn-primary">
            <i class="fa fa-file-excel-o"></i> Excel татах (.xlsx)
        </a>
        <a href="/internship/export?type=csv<?php echo $applyQuery;?>" class="btn btn-white">
            <i class="fa fa-download"></i> CSV
        </a>
        <a href="/internship/settings" class="btn btn-white"><i class="fa fa-cog"></i> Тохиргоо</a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp">

    <div class="row">
        <div class="col-lg-3 col-sm-6">
            <div class="ibox">
                <div class="ibox-content">
                    <h5>Нийт хүсэлт</h5>
                    <h1 class="no-margins"><?php echo $applyAllCount;?></h1>
                    <small>вэб сайтаар ирсэн бүх хүсэлт</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="ibox">
                <div class="ibox-content">
                    <h5>Шинэ</h5>
                    <h1 class="no-margins" style="color:#1ab394"><?php echo $applyNewCount;?></h1>
                    <small>хараахан үзээгүй</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="ibox">
                <div class="ibox-content">
                    <h5>Дадлага</h5>
                    <h1 class="no-margins"><?php echo $applyInternCount;?></h1>
                    <small><a href="/internship/list?t=intern">зөвхөн эдгээрийг харах</a></small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="ibox">
                <div class="ibox-content">
                    <h5>Ажлын байр</h5>
                    <h1 class="no-margins"><?php echo $applyJobCount;?></h1>
                    <small><a href="/internship/list?t=job">зөвхөн эдгээрийг харах</a></small>
                </div>
            </div>
        </div>
    </div>

    <div class="ibox">
        <div class="ibox-title">
            <h5>Хүсэлтүүд <span class="badge badge-primary"><?php echo $applyTotal;?></span></h5>
            <div class="ibox-tools">
                <form method="get" action="/internship/list" class="form-inline" style="display:inline-block">
                    <select name="t" class="form-control input-sm" style="width:auto;display:inline-block">
                        <option value="">Бүх төрөл</option>
                        <?php foreach($applyTypes as $tk=>$tl){ ?>
                        <option value="<?php echo $tk;?>"<?php if($applyFType==$tk) echo ' selected';?>><?php echo ApplyCore::esc($tl);?></option>
                        <?php } ?>
                    </select>
                    <select name="s" class="form-control input-sm" style="width:auto;display:inline-block">
                        <option value="">Бүх төлөв</option>
                        <?php foreach($applyStates as $sk=>$sl){ ?>
                        <option value="<?php echo $sk;?>"<?php if($applyFState==$sk) echo ' selected';?>><?php echo ApplyCore::esc($sl);?></option>
                        <?php } ?>
                    </select>
                    <div class="input-group">
                        <input type="text" name="q" class="form-control input-sm" placeholder="Нэр, утас, и-мэйл..." value="<?php echo ApplyCore::esc($applyQ);?>">
                        <span class="input-group-btn">
                            <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-search"></i></button>
                            <?php if($applyQuery!=""){ ?>
                            <a class="btn btn-sm btn-white" href="/internship/list">Цэвэрлэх</a>
                            <?php } ?>
                        </span>
                    </div>
                </form>
            </div>
        </div>
        <div class="ibox-content">

            <?php if(count($applyRows)<1){ ?>
            <div class="text-center" style="padding:40px 0;color:#888">
                <?php echo $applyQuery!="" ? "Шүүлтэд тохирох хүсэлт олдсонгүй." : "Одоогоор хүсэлт алга байна.";?>
            </div>
            <?php } else { ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th style="width:50px">№</th>
                        <th style="width:110px">Төрөл</th>
                        <th>Нэр</th>
                        <th style="width:130px">Утас</th>
                        <th>И-мэйл</th>
                        <th style="width:70px">Файл</th>
                        <th style="width:120px">Төлөв</th>
                        <th style="width:145px">Огноо</th>
                        <th style="width:120px"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $rowNo = $applyOffset;
                    foreach($applyRows as $obj){
                        $rowNo++;
                        $fileArr = ApplyCore::files($obj);
                    ?>
                    <tr id="applyRow<?php echo (int)$obj["entryID"];?>">
                        <td><?php echo $rowNo;?></td>
                        <td><span class="label label-<?php echo $obj["entryType"]=="job" ? "info" : "default";?>"><?php echo ApplyCore::esc(ApplyCore::typeName($obj["entryType"]));?></span></td>
                        <td>
                            <strong><?php echo ApplyCore::esc($obj["entryName"]);?></strong>
                            <?php if($obj["entryPosition"]!=""){ ?>
                            <br><small class="text-muted"><?php echo ApplyCore::esc($obj["entryPosition"]);?></small>
                            <?php } ?>
                        </td>
                        <td><?php echo ApplyCore::esc($obj["entryPhone"]);?></td>
                        <td><?php echo ApplyCore::esc($obj["entryEmail"]);?></td>
                        <td>
                            <?php if(count($fileArr)>0){ ?>
                            <i class="fa fa-paperclip"></i> <?php echo count($fileArr);?>
                            <?php } ?>
                        </td>
                        <td><span class="label <?php echo ApplyCore::stateClass($obj["entryState"]);?>"><?php echo ApplyCore::esc(ApplyCore::stateName($obj["entryState"]));?></span></td>
                        <td><small><?php echo ApplyCore::esc($obj["entryDate"]);?></small></td>
                        <td class="text-right">
                            <a href="/internship/view/<?php echo (int)$obj["entryID"];?>" class="btn btn-xs btn-primary applyModBtn"><i class="fa fa-eye"></i> Харах</a>
                            <a href="#" class="btn btn-xs btn-danger applyDelBtn" data-id="<?php echo (int)$obj["entryID"];?>"
                               data-name="<?php echo ApplyCore::esc($obj["entryName"]);?>"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if($applyPageCount>1){ ?>
            <ul class="pagination">
                <?php for($i=1;$i<=$applyPageCount;$i++){ ?>
                <li class="<?php if($i==$applyPage) echo "active";?>">
                    <a href="/internship/list?p=<?php echo $i;echo $applyQuery;?>"><?php echo $i;?></a>
                </li>
                <?php } ?>
            </ul>
            <?php } ?>

            <?php } ?>

        </div>
    </div>
</div>

<div class="modal inmodal" id="orderModalFrm" tabindex="-1" role="dialog" aria-hidden="true"></div>
