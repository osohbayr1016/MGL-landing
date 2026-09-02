<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-8">
        <h2>Хуудасны дизайн</h2>
        <ol class="breadcrumb">
            <li><a href="/">Эхлэл</a></li>
            <li><a href="/registration/list">Арга хэмжээний бүртгэл</a></li>
            <li class="active"><strong>Хуудасны дизайн</strong></li>
        </ol>
    </div>
    <div class="col-sm-4 text-right" style="padding-top:22px;">
        <a href="<?php echo RegistrationCore::esc($regPageLink);?>" target="_blank" class="btn btn-white">
            <i class="fa fa-external-link"></i> Хуудсыг харах
        </a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp">

    <!-- ---------------- Блокууд ---------------- -->
    <div class="ibox">
        <div class="ibox-title">
            <h5>Хуудасны блокууд</h5>
            <div class="ibox-tools">
                <div class="btn-group">
                    <button class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" type="button">
                        <i class="fa fa-plus"></i> Блок нэмэх <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <?php foreach($regBlockTypes as $typeKey=>$typeObj){ ?>
                        <li>
                            <a href="/registration/blockEdit/0?type=<?php echo urlencode($typeKey);?>" class="regModBtn">
                                <i class="<?php echo RegistrationCore::esc($typeObj["icon"]);?>"></i>
                                <?php echo RegistrationCore::esc($typeObj["label"]);?>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="ibox-content">
            <p class="text-muted">
                Блок бүр хуудасны нэг хэсэг. Дээрээс доош байрлана — сум товчоор байрыг сольж,
                "Засах" дээр дарж агуулга, зураг, өнгийг өөрчилнө.
            </p>

            <?php if(count($regBlockList)<1){ ?>
            <div class="alert alert-warning">Блок алга байна. Дээрх "Блок нэмэх" товчоор эхэлнэ үү.</div>
            <?php } else { ?>

            <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th style="width:70px">Эрэмбэ</th>
                    <th style="width:210px">Төрөл</th>
                    <th>Агуулга</th>
                    <th style="width:90px">Төлөв</th>
                    <th style="width:290px"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($regBlockList as $i=>$obj){
                    $typeKey = $obj["blockType"];
                    $typeObj = isset($regBlockTypes[$typeKey]) ? $regBlockTypes[$typeKey] : null;
                    $data    = $obj["data"];

                    /* Жагсаалтад харагдах богино тайлбар */
                    $preview = "";
                    foreach(array("title","eyebrow","body","caption","height") as $pk){
                        if(isset($data[$pk]) && trim(strip_tags($data[$pk]))!=""){
                            $preview = trim(strip_tags($data[$pk]));
                            break;
                        }
                    }
                    if(function_exists("mb_substr") && $preview!=""){
                        $preview = mb_substr($preview, 0, 90, "UTF-8");
                    }
                ?>
                <tr id="regBlockRow<?php echo (int)$obj["blockID"];?>"<?php if((int)$obj["blockStatus"]!=1) echo ' class="text-muted" style="opacity:.55"';?>>
                    <td>
                        <a href="#" class="btn btn-xs btn-white regMoveBtn" data-id="<?php echo (int)$obj["blockID"];?>" data-dir="up" title="Дээш"><i class="fa fa-angle-up"></i></a>
                        <a href="#" class="btn btn-xs btn-white regMoveBtn" data-id="<?php echo (int)$obj["blockID"];?>" data-dir="down" title="Доош"><i class="fa fa-angle-down"></i></a>
                    </td>
                    <td>
                        <i class="<?php echo $typeObj ? RegistrationCore::esc($typeObj["icon"]) : "fa fa-question";?>"></i>
                        <strong><?php echo $typeObj ? RegistrationCore::esc($typeObj["label"]) : RegistrationCore::esc($typeKey);?></strong>
                    </td>
                    <td><small><?php echo RegistrationCore::esc($preview);?></small></td>
                    <td>
                        <?php if((int)$obj["blockStatus"]==1){ ?>
                        <span class="label label-primary">Идэвхтэй</span>
                        <?php } else { ?>
                        <span class="label label-default">Унтраасан</span>
                        <?php } ?>
                    </td>
                    <td class="text-right">
                        <?php if(RegistrationCore::hasSub($typeKey)){ ?>
                        <a href="/registration/subList/<?php echo (int)$obj["blockID"];?>" class="btn btn-xs btn-white">
                            <i class="fa fa-list"></i> Дэд мөр (<?php echo count($obj["sub"]);?>)
                        </a>
                        <?php } ?>
                        <a href="#" class="btn btn-xs btn-white regToggleBtn" data-id="<?php echo (int)$obj["blockID"];?>">
                            <?php echo (int)$obj["blockStatus"]==1 ? "Унтраах" : "Асаах";?>
                        </a>
                        <a href="/registration/blockEdit/<?php echo (int)$obj["blockID"];?>" class="btn btn-xs btn-warning regModBtn">
                            <i class="fa fa-pencil"></i> Засах
                        </a>
                        <?php if($typeKey!="form"){ ?>
                        <a href="#" class="btn btn-xs btn-danger regBlockDelBtn" data-id="<?php echo (int)$obj["blockID"];?>">
                            <i class="fa fa-trash"></i>
                        </a>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>

            <?php } ?>
        </div>
    </div>

    <!-- ---------------- Загвар (theme) ---------------- -->
    <form action="/userPost/registration" method="post">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Загвар — өнгө, шрифт, хэмжээ</h5>
            <div class="ibox-tools"><button type="submit" class="btn btn-primary btn-xs">Хадгалах</button></div>
        </div>
        <div class="ibox-content">

            <h4 style="margin-top:0">Өнгө</h4>
            <div class="row">
                <?php
                $regColorFields = array(
                    "themeBg"         => "Хуудасны дэвсгэр",
                    "themeSurface"    => "Хайрцгийн дэвсгэр",
                    "themeText"       => "Үндсэн текст",
                    "themeMuted"      => "Бүдэг текст",
                    "themeBorder"     => "Хүрээний өнгө",
                    "themeAccent"     => "Онцлох өнгө (товч)",
                    "themeAccentText" => "Товчны текст",
                    "themeInputBg"    => "Input дэвсгэр",
                    "themeInputText"  => "Input текст"
                );
                foreach($regColorFields as $ck=>$cl){
                ?>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="font-noraml"><?php echo $cl;?></label>
                        <div class="input-group">
                            <input type="color" class="form-control input-sm regColorPick" style="padding:2px;width:46px"
                                   value="<?php echo RegistrationCore::esc($regSet[$ck]);?>" data-target="reg_<?php echo $ck;?>">
                            <input type="text" class="form-control input-sm" id="reg_<?php echo $ck;?>"
                                   name="frmSet[<?php echo $ck;?>]" value="<?php echo RegistrationCore::esc($regSet[$ck]);?>">
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>

            <h4>Бичиг, хэмжээ</h4>
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="font-noraml">Гарчгийн зузаан</label>
                        <select class="form-control input-sm" name="frmSet[themeTitleWeight]">
                            <?php foreach(array("600"=>"600 SemiBold","700"=>"700 Bold","800"=>"800 ExtraBold","900"=>"900 Black") as $wk=>$wl){ ?>
                            <option value="<?php echo $wk;?>" <?php if($regSet["themeTitleWeight"]==$wk) echo "selected";?>><?php echo $wl;?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="font-noraml">Энгийн текстийн зузаан</label>
                        <select class="form-control input-sm" name="frmSet[themeBodyWeight]">
                            <?php foreach(array("300"=>"300 Light","400"=>"400 Regular","500"=>"500 Medium","600"=>"600 SemiBold") as $wk=>$wl){ ?>
                            <option value="<?php echo $wk;?>" <?php if($regSet["themeBodyWeight"]==$wk) echo "selected";?>><?php echo $wl;?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="font-noraml">Гарчгийн хэмжээ (px)</label>
                        <input type="number" min="20" max="140" class="form-control input-sm" name="frmSet[themeTitleSize]" value="<?php echo (int)$regSet["themeTitleSize"];?>">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="font-noraml">Үсэг хоорондын зай (em)</label>
                        <input type="text" class="form-control input-sm" name="frmSet[themeLetterSpacing]" value="<?php echo RegistrationCore::esc($regSet["themeLetterSpacing"]);?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="font-noraml">Агуулгын өргөн (px)</label>
                        <input type="number" min="600" max="1800" class="form-control input-sm" name="frmSet[themeMaxWidth]" value="<?php echo (int)$regSet["themeMaxWidth"];?>">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="font-noraml">Булангийн дугуйрал (px)</label>
                        <input type="number" min="0" max="60" class="form-control input-sm" name="frmSet[themeRadius]" value="<?php echo (int)$regSet["themeRadius"];?>">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="font-noraml">Гарчгийг том үсгээр</label>
                        <select class="form-control input-sm" name="frmSet[themeUppercase]">
                            <option value="1" <?php if($regSet["themeUppercase"]=="1") echo "selected";?>>Тийм</option>
                            <option value="0" <?php if($regSet["themeUppercase"]!="1") echo "selected";?>>Үгүй</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="font-noraml">Footer текст</label>
                        <input type="text" class="form-control input-sm" name="frmSet[footerText]" value="<?php echo RegistrationCore::esc($regSet["footerText"]);?>">
                    </div>
                </div>
            </div>

            <h4>Дизайнерын өөрийн код</h4>
            <div class="form-group">
                <label class="font-noraml">Нэмэлт CSS</label>
                <small class="text-muted"> — бүх стандарт загварыг дарж бичнэ. Жишээ: <code>.reg-hero-title{font-size:90px}</code></small>
                <textarea class="form-control regCode" name="frmSet[customCss]" rows="10" spellcheck="false"><?php echo RegistrationCore::esc($regSet["customCss"]);?></textarea>
            </div>
            <div class="form-group">
                <label class="font-noraml">&lt;head&gt; дотор нэмэх код</label>
                <small class="text-muted"> — нэмэлт шрифт, analytics, meta г.м.</small>
                <textarea class="form-control regCode" name="frmSet[customHeadHtml]" rows="5" spellcheck="false"><?php echo RegistrationCore::esc($regSet["customHeadHtml"]);?></textarea>
            </div>

            <input type="hidden" name="frmPost" value="regTheme">
            <button type="submit" class="btn btn-primary">Хадгалах</button>
        </div>
    </div>
    </form>

</div>

<div class="modal inmodal" id="orderModalFrm" tabindex="-1" role="dialog" aria-hidden="true"></div>
