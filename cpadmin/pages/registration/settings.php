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

    <div class="ibox">
        <div class="ibox-title">
            <h5>Хуудсан дээр нь шууд засварлах</h5>
            <div class="ibox-tools">
                <span class="label <?php echo $regSet["liveEdit"]=="1" ? "label-primary" : "label-default";?>">
                    <?php echo $regSet["liveEdit"]=="1" ? "Асаалттай" : "Унтраасан";?>
                </span>
            </div>
        </div>
        <div class="ibox-content">
            <p class="text-muted">
                Асаалттай үед нэвтэрсэн админ бүртгэлийн хуудсыг нээхэд текст дээр нь шууд дарж
                засах, зураг/дэвсгэр дээр дарж шинэ зураг эсвэл видео байршуулах боломжтой болно.
                Арга хэмжээ дууссаны дараа <strong>унтраавал</strong> хуудас зөвхөн уншигдах болно.
            </p>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="font-noraml">Горим</label>
                        <select class="form-control input-sm" name="frmSet[liveEdit]">
                            <option value="1"<?php if($regSet["liveEdit"]=="1") echo ' selected';?>>Асаалттай — админ шууд засаж болно</option>
                            <option value="0"<?php if($regSet["liveEdit"]!="1") echo ' selected';?>>Унтраасан — зөвхөн CP Admin-аас</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-8" style="padding-top:24px">
                    <a href="/registration/design" class="btn btn-white btn-sm">
                        <i class="fa fa-magic"></i> Хуудасны дизайн хэсгээс шууд засаж эхлэх
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="ibox">
        <div class="ibox-title">
            <h5>Медиа хадгалалт — Cloudflare R2</h5>
            <div class="ibox-tools">
                <span class="label <?php echo $regMedia["ready"] ? "label-primary" : "label-warning";?>">
                    <?php echo $regMedia["ready"] ? "Холбогдсон" : "Тохируулаагүй";?>
                </span>
            </div>
        </div>
        <div class="ibox-content">

            <?php if(isset($_SESSION["regR2Result"])){ ?>
            <div class="alert <?php echo strpos($_SESSION["regR2Result"],"АЛДАА")!==false ? "alert-danger" : "alert-success";?>">
                <?php echo RegistrationCore::esc($_SESSION["regR2Result"]); unset($_SESSION["regR2Result"]); ?>
            </div>
            <?php } ?>

            <p class="text-muted">
                Хуудсан дээр байршуулсан зураг, видео Cloudflare R2 bucket-д хадгалагдана.
                Хоосон үлдээвэл файлууд серверийн диск дээр хадгалагдана (тэр ч ажиллана).
                Эдгээрийг <code>const.php</code>-д бичсэн бол тэр нь давуу эрхтэй.
            </p>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="font-noraml">Account ID</label>
                        <input type="text" class="form-control input-sm" name="frmSet[r2Account]" autocomplete="off"
                               value="<?php echo RegistrationCore::esc($regSet["r2Account"]);?>" placeholder="Cloudflare -> R2 -> Account ID">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="font-noraml">Bucket нэр</label>
                        <input type="text" class="form-control input-sm" name="frmSet[r2Bucket]" autocomplete="off"
                               value="<?php echo RegistrationCore::esc($regSet["r2Bucket"]);?>" placeholder="mglenc-media">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="font-noraml">Access Key ID</label>
                        <input type="text" class="form-control input-sm" name="frmSet[r2Key]" autocomplete="off"
                               value="<?php echo RegistrationCore::esc($regSet["r2Key"]);?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="font-noraml">Secret Access Key</label>
                        <input type="password" class="form-control input-sm" name="frmSet[r2Secret]" autocomplete="new-password"
                               placeholder="<?php echo $regSet["r2Secret"]!="" ? "•••••••• (хадгалагдсан — солихгүй бол хоосон үлдээнэ)" : "";?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8">
                    <div class="form-group">
                        <label class="font-noraml">Медиа CDN хаяг (Cloudflare Worker)</label>
                        <input type="text" class="form-control input-sm" name="frmSet[mediaCdn]"
                               value="<?php echo RegistrationCore::esc($regSet["mediaCdn"]);?>" placeholder="https://mglenc-media.<нэр>.workers.dev">
                        <small class="text-muted">
                            Хоосон бол зураг сервер дээрээс үйлчилнэ (R2-д зэрэг хуулагдана).
                            Хаяг оруулсан үед л зөвхөн R2-оос үйлчилж, серверийн хуулбар устана.
                        </small>
                    </div>
                </div>
                <div class="col-sm-4" style="padding-top:24px">
                    <button type="submit" class="btn btn-primary btn-sm">Хадгалах</button>
                    <button type="submit" class="btn btn-white btn-sm" name="frmPost" value="regR2Test">Холболт шалгах</button>
                </div>
            </div>

            <div class="alert alert-info" style="margin-bottom:0">
                <strong>Cloudflare дээр хийх зүйл:</strong>
                <ol style="margin:8px 0 0 18px;padding:0">
                    <li>R2 → <em>Create bucket</em> → нэр: <code>mglenc-media</code></li>
                    <li>R2 → <em>Manage R2 API Tokens</em> → <em>Create API token</em> → эрх: <em>Object Read &amp; Write</em> → Access Key ID / Secret Access Key-г энд буулгана</li>
                    <li>Account ID нь R2 хуудасны баруун талд байдаг</li>
                    <li>Зургийг R2-оос үйлчлүүлэхийн тулд <code>cloudflare/media-worker.js</code> worker-ийг deploy хийж, түүний хаягийг дээрх талбарт бичнэ</li>
                </ol>
            </div>

        </div>
    </div>

    <button type="submit" class="btn btn-primary">Хадгалах</button>

</form>
</div>
