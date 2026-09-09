<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-8">
        <h2>Тохиргоо</h2>
        <ol class="breadcrumb">
            <li><a href="/">Эхлэл</a></li>
            <li><a href="/internship/list">Дадлага</a></li>
            <li class="active"><strong>Тохиргоо</strong></li>
        </ol>
    </div>
    <div class="col-sm-4 text-right" style="padding-top:22px;">
        <a href="/internship/fields" class="btn btn-white"><i class="fa fa-list"></i> Формын асуулт</a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp">
<form action="/userPost/internship" method="post">
<input type="hidden" name="frmPost" value="applySettings">

    <div class="row">
        <div class="col-lg-6">

            <div class="ibox">
                <div class="ibox-title"><h5>Дадлагын хуудас — /internship</h5></div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label class="font-noraml">Гарчиг</label>
                        <input type="text" class="form-control" name="frmSet[internTitle]" value="<?php echo ApplyCore::esc($applySet["internTitle"]);?>">
                    </div>
                    <div class="form-group">
                        <label class="font-noraml">Тайлбар</label>
                        <textarea class="form-control" name="frmSet[internText]" rows="4"><?php echo ApplyCore::esc($applySet["internText"]);?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="font-noraml">Хүсэлт хүлээн авах эсэх</label>
                        <select class="form-control" name="frmSet[internOpen]">
                            <option value="1"<?php if((string)$applySet["internOpen"]==="1") echo ' selected';?>>Нээлттэй</option>
                            <option value="0"<?php if((string)$applySet["internOpen"]!=="1") echo ' selected';?>>Хаалттай</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-noraml">Формыг харуулах хуудсууд (/page/N)</label>
                        <input type="text" class="form-control" name="frmSet[embedIntern]" value="<?php echo ApplyCore::esc($applySet["embedIntern"]);?>" placeholder="ж: 7">
                        <small class="text-muted">Таслалаар тусгаарлана. Тухайн хуудсын агуулгын доор форм автоматаар гарна.</small>
                    </div>
                </div>
            </div>

            <div class="ibox">
                <div class="ibox-title"><h5>Ажлын байрны хуудас — /career</h5></div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label class="font-noraml">Гарчиг</label>
                        <input type="text" class="form-control" name="frmSet[jobTitle]" value="<?php echo ApplyCore::esc($applySet["jobTitle"]);?>">
                    </div>
                    <div class="form-group">
                        <label class="font-noraml">Тайлбар</label>
                        <textarea class="form-control" name="frmSet[jobText]" rows="4"><?php echo ApplyCore::esc($applySet["jobText"]);?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="font-noraml">Хүсэлт хүлээн авах эсэх</label>
                        <select class="form-control" name="frmSet[jobOpen]">
                            <option value="1"<?php if((string)$applySet["jobOpen"]==="1") echo ' selected';?>>Нээлттэй</option>
                            <option value="0"<?php if((string)$applySet["jobOpen"]!=="1") echo ' selected';?>>Хаалттай</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-noraml">Формыг харуулах хуудсууд (/page/N)</label>
                        <input type="text" class="form-control" name="frmSet[embedJob]" value="<?php echo ApplyCore::esc($applySet["embedJob"]);?>" placeholder="ж: 6">
                        <small class="text-muted">Таслалаар тусгаарлана.</small>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-lg-6">

            <div class="ibox">
                <div class="ibox-title"><h5>Мессежүүд</h5></div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label class="font-noraml">Илгээх товчны бичвэр</label>
                        <input type="text" class="form-control" name="frmSet[submitLabel]" value="<?php echo ApplyCore::esc($applySet["submitLabel"]);?>">
                    </div>
                    <div class="form-group">
                        <label class="font-noraml">Амжилттай илгээсний гарчиг</label>
                        <input type="text" class="form-control" name="frmSet[successTitle]" value="<?php echo ApplyCore::esc($applySet["successTitle"]);?>">
                    </div>
                    <div class="form-group">
                        <label class="font-noraml">Амжилттай илгээсний бичвэр</label>
                        <textarea class="form-control" name="frmSet[successText]" rows="3"><?php echo ApplyCore::esc($applySet["successText"]);?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="font-noraml">Хаалттай үеийн гарчиг</label>
                        <input type="text" class="form-control" name="frmSet[closedTitle]" value="<?php echo ApplyCore::esc($applySet["closedTitle"]);?>">
                    </div>
                    <div class="form-group">
                        <label class="font-noraml">Хаалттай үеийн бичвэр</label>
                        <textarea class="form-control" name="frmSet[closedText]" rows="3"><?php echo ApplyCore::esc($applySet["closedText"]);?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="font-noraml">Ерөнхий алдааны бичвэр</label>
                        <input type="text" class="form-control" name="frmSet[errorText]" value="<?php echo ApplyCore::esc($applySet["errorText"]);?>">
                    </div>
                    <div class="form-group">
                        <label class="font-noraml">"Заавал бөглөнө үү" бичвэр</label>
                        <input type="text" class="form-control" name="frmSet[requiredText]" value="<?php echo ApplyCore::esc($applySet["requiredText"]);?>">
                    </div>
                </div>
            </div>

            <div class="ibox">
                <div class="ibox-title"><h5>Хавсралт ба давхардал</h5></div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label class="font-noraml">Файлын дээд хэмжээ (MB)</label>
                        <input type="number" min="1" class="form-control" name="frmSet[fileMaxMb]" value="<?php echo (int)$applySet["fileMaxMb"];?>">
                        <small class="text-muted">Энэ сервер дээр нэг удаад <?php echo ApplyCore::esc($applyMaxText);?> хүртэл багтана. Түүнээс их бичсэн ч серверийн хязгаар давамгайлна.</small>
                    </div>
                    <div class="form-group">
                        <label class="font-noraml">Зөвшөөрөх файлын төрөл</label>
                        <input type="text" class="form-control" name="frmSet[fileTypes]" value="<?php echo ApplyCore::esc($applySet["fileTypes"]);?>">
                        <small class="text-muted">Таслалаар: pdf, jpg, png ...</small>
                    </div>
                    <div class="form-group">
                        <label class="font-noraml">Давхар хүсэлт хориглох</label>
                        <select class="form-control" name="frmSet[dupCheck]">
                            <option value="0"<?php if((string)$applySet["dupCheck"]!=="1") echo ' selected';?>>Үгүй</option>
                            <option value="1"<?php if((string)$applySet["dupCheck"]==="1") echo ' selected';?>>Тийм — нэг и-мэйлээс 30 хоногт нэг удаа</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-noraml">Давхардсан үеийн бичвэр</label>
                        <input type="text" class="form-control" name="frmSet[dupText]" value="<?php echo ApplyCore::esc($applySet["dupText"]);?>">
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="ibox">
        <div class="ibox-content text-right">
            <a href="/internship/list" class="btn btn-white">Буцах</a>
            <button type="submit" class="btn btn-primary">Хадгалах</button>
        </div>
    </div>

</form>
</div>
