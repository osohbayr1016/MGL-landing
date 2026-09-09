<?php include "clients.css.php"; ?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-8">
        <h2>Харилцагч байгууллага</h2>
        <ol class="breadcrumb">
            <li><a href="/">Эхлэл</a></li>
            <li><a href="/insert/promo">Агуулга оруулах</a></li>
            <li class="active"><strong>Харилцагч байгууллага</strong></li>
        </ol>
    </div>
    <div class="col-sm-4 text-right" style="padding-top:20px;">
        <button type="button" id="clSave" class="btn btn-primary" <?php if($clSelID<1) echo "disabled";?>>Хадгалах</button>
    </div>
</div>
<div class="wrapper wrapper-content">
    <?php if(count($clSections)<1){ ?>
    <div class="alert alert-warning">
        Харилцагчийн хэсэг олдсонгүй. Эхлээд <a href="/insert/promo">Агуулга оруулах</a> хэсгээс тухайн
        хуудсанд "Харилцагч" агуулга нэмээд, "Харагдах хэлбэр"-ийг логоны жагсаалт болгож сонгоно уу.
    </div>
    <?php } else { ?>
    <div class="ibox">
        <div class="ibox-title">
            <h5>Логонууд — дарахад тухайн компанийн вэб сайт нээгдэнэ</h5>
        </div>
        <div class="ibox-content">
            <div class="cl-bar">
                <?php if(count($clSections)>1){ ?>
                <div class="form-group">
                    <label class="font-noraml">Хэсэг</label>
                    <select class="form-control input-sm" id="clSection">
                        <?php foreach($clSections as $obj){ ?>
                        <option value="<?php echo $obj["id"];?>"<?php if($obj["id"]==$clSelID) echo ' selected="selected"';?>><?php echo htmlspecialchars($obj["title"]);?><?php if($obj["page"]!="") echo " — ".htmlspecialchars($obj["page"]);?></option>
                        <?php } ?>
                    </select>
                </div>
                <?php } ?>
                <div class="form-group">
                    <label class="font-noraml">Хэсгийн гарчиг</label>
                    <input type="text" class="form-control input-sm" id="clTitle" value="<?php echo htmlspecialchars($clSelName);?>">
                </div>
                <div class="form-group">
                    <label class="font-noraml">Эффект</label><br>
                    <label style="font-weight:400;margin-right:14px;">
                        <input type="checkbox" id="clHover"<?php if($clHover) echo ' checked="checked"';?>> Хар цагаан, hover үед өнгөтэй
                    </label>
                    <label style="font-weight:400;">
                        <input type="checkbox" id="clNewTab"<?php if($clNewTab) echo ' checked="checked"';?>> Шинэ цонхонд нээх
                    </label>
                </div>
            </div>

            <p class="text-muted">Чирж дарааллыг солино. Лого оруулахдаа файл сонгож байршуулах эсвэл зургийн хаягийг шууд бичиж болно.</p>

            <div id="clList"></div>

            <div style="margin-top:16px;">
                <button type="button" id="clAdd" class="btn btn-white"><i class="fa fa-plus"></i> Харилцагч нэмэх</button>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
<input type="file" id="clFile" accept="image/*" style="display:none;">
