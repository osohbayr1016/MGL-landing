<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-8">
        <h2>Тохиргоо</h2>
        <ol class="breadcrumb">
            <li><a href="/">Эхлэл</a></li>
            <li><a href="/registration/list">Арга хэмжээний бүртгэл</a></li>
            <li class="active"><strong>Тохиргоо</strong></li>
        </ol>
    </div>
    <div class="col-sm-4 text-right" style="padding-top:22px;">
        <a href="<?php echo RegistrationCore::esc($regPageLink);?>" target="_blank" class="btn btn-white">
            <i class="fa fa-external-link"></i> Хуудсыг харах
        </a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp">
<form action="/userPost/registration" method="post">

    <div class="ibox">
        <div class="ibox-title">
            <h5>Арга хэмжээ</h5>
            <div class="ibox-tools"><button type="submit" class="btn btn-primary btn-xs">Хадгалах</button></div>
        </div>
        <div class="ibox-content">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="font-noraml">Арга хэмжээний нэр</label>
                        <input type="text" class="form-control input-sm" name="frmSet[eventTitle]" value="<?php echo RegistrationCore::esc($regSet["eventTitle"]);?>">
                        <small class="text-muted">Excel файлын sheet-ийн нэр болно.</small>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="font-noraml">Болох огноо, цаг</label>
                        <input type="datetime-local" class="form-control input-sm" name="frmSet[eventDate]" value="<?php echo RegistrationCore::esc(regDtLocal($regSet["eventDate"]));?>">
                        <small class="text-muted">Countdown блок үүнийг ашиглана.</small>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="font-noraml">Байршил</label>
                        <input type="text" class="form-control input-sm" name="frmSet[eventLocation]" value="<?php echo RegistrationCore::esc($regSet["eventLocation"]);?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ibox">
        <div class="ibox-title">
            <h5>Бүртгэлийн хяналт</h5>
            <div class="ibox-tools">
                <span class="label <?php echo $regStatus["open"] ? "label-primary" : "label-danger";?>">
                    <?php echo $regStatus["open"] ? "Нээлттэй" : "Хаалттай";?>
                </span>
            </div>
        </div>
        <div class="ibox-content">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="font-noraml">Бүртгэл авах эсэх</label>
                        <select class="form-control input-sm" name="frmSet[regOpen]">
                            <option value="1"<?php if($regSet["regOpen"]=="1") echo ' selected';?>>Нээлттэй</option>
                            <option value="0"<?php if($regSet["regOpen"]!="1") echo ' selected';?>>Хаасан</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="font-noraml">Эхлэх огноо</label>
                        <input type="datetime-local" class="form-control input-sm" name="frmSet[regOpenFrom]" value="<?php echo RegistrationCore::esc(regDtLocal($regSet["regOpenFrom"]));?>">
                        <small class="text-muted">Хоосон = хязгааргүй</small>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="font-noraml">Дуусах огноо</label>
                        <input type="datetime-local" class="form-control input-sm" name="frmSet[regOpenTo]" value="<?php echo RegistrationCore::esc(regDtLocal($regSet["regOpenTo"]));?>">
                        <small class="text-muted">Хоосон = хязгааргүй</small>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="font-noraml">Багтаамж (хүн)</label>
                        <input type="number" min="0" class="form-control input-sm" name="frmSet[regLimit]" value="<?php echo (int)$regSet["regLimit"];?>">
                        <small class="text-muted">0 = хязгааргүй. Одоо: <strong><?php echo $regAllCount;?></strong> бүртгэл</small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="font-noraml">Давхардал шалгах</label>
                        <select class="form-control input-sm" name="frmSet[regDupCheck]">
                            <option value="0"<?php if($regSet["regDupCheck"]!="1") echo ' selected';?>>Үгүй — дахин бүртгүүлж болно</option>
                            <option value="1"<?php if($regSet["regDupCheck"]=="1") echo ' selected';?>>Тийм — нэг утас/и-мэйл нэг удаа</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ibox">
        <div class="ibox-title"><h5>Мессежүүд</h5></div>
        <div class="ibox-content">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="font-noraml">Илгээх товчны текст</label>
                        <input type="text" class="form-control input-sm" name="frmSet[submitLabel]" value="<?php echo RegistrationCore::esc($regSet["submitLabel"]);?>">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="font-noraml">Амжилттай — гарчиг</label>
                        <input type="text" class="form-control input-sm" name="frmSet[successTitle]" value="<?php echo RegistrationCore::esc($regSet["successTitle"]);?>">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="font-noraml">Амжилттай — текст</label>
                        <input type="text" class="form-control input-sm" name="frmSet[successText]" value="<?php echo RegistrationCore::esc($regSet["successText"]);?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="font-noraml">Хаагдсан — гарчиг</label>
                        <input type="text" class="form-control input-sm" name="frmSet[closedTitle]" value="<?php echo RegistrationCore::esc($regSet["closedTitle"]);?>">
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="form-group">
                        <label class="font-noraml">Хаагдсан — текст</label>
                        <input type="text" class="form-control input-sm" name="frmSet[closedText]" value="<?php echo RegistrationCore::esc($regSet["closedText"]);?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="font-noraml">Дүүрсэн — гарчиг</label>
                        <input type="text" class="form-control input-sm" name="frmSet[fullTitle]" value="<?php echo RegistrationCore::esc($regSet["fullTitle"]);?>">
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="form-group">
                        <label class="font-noraml">Дүүрсэн — текст</label>
                        <input type="text" class="form-control input-sm" name="frmSet[fullText]" value="<?php echo RegistrationCore::esc($regSet["fullText"]);?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="font-noraml">"Заавал бөглөнө үү" текст</label>
                        <input type="text" class="form-control input-sm" name="frmSet[requiredText]" value="<?php echo RegistrationCore::esc($regSet["requiredText"]);?>">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="font-noraml">Ерөнхий алдааны текст</label>
                        <input type="text" class="form-control input-sm" name="frmSet[errorText]" value="<?php echo RegistrationCore::esc($regSet["errorText"]);?>">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="font-noraml">Давхардсан үеийн текст</label>
                        <input type="text" class="form-control input-sm" name="frmSet[dupText]" value="<?php echo RegistrationCore::esc($regSet["dupText"]);?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ibox">
        <div class="ibox-title"><h5>Хуудасны толгой (meta)</h5></div>
        <div class="ibox-content">
            <div class="alert alert-info">
                <i class="fa fa-lock"></i> Энэ хуудсыг сайтын цэс, footer-т харуулаагүй бөгөөд
                <code>noindex, nofollow</code> тавьсан тул Google-д индекслэгдэхгүй.
                Зөвхөн <code><?php echo RegistrationCore::esc($regPageLink);?></code> линк болон QR кодоор нээгдэнэ.
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="font-noraml">Вэб сайтын үндсэн хаяг</label>
                        <input type="text" class="form-control input-sm" name="frmSet[siteBase]" value="<?php echo RegistrationCore::esc($regSet["siteBase"]);?>" placeholder="https://mglenc.com">
                        <small class="text-muted">QR код болон линк үүсгэхэд ашиглана. Төгсгөлд нь <code>/registration</code> нэмэгдэнэ.</small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-5">
                    <div class="form-group">
                        <label class="font-noraml">Browser tab-ын гарчиг</label>
                        <input type="text" class="form-control input-sm" name="frmSet[metaTitle]" value="<?php echo RegistrationCore::esc($regSet["metaTitle"]);?>">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="font-noraml">Тайлбар</label>
                        <input type="text" class="form-control input-sm" name="frmSet[metaDesc]" value="<?php echo RegistrationCore::esc($regSet["metaDesc"]);?>">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="font-noraml">Favicon</label>
                        <div class="input-group">
                            <input type="text" class="form-control input-sm" id="regFavicon" name="frmSet[favicon]" value="<?php echo RegistrationCore::esc($regSet["favicon"]);?>">
                            <span class="input-group-btn">
                                <button class="btn btn-default btn-sm" type="button" onclick="regOpenPicker('regFavicon')">Сонгох</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="frmPost" value="regSettings">
    <button type="submit" class="btn btn-primary">Хадгалах</button>

</form>
</div>
