<?php
/** Өдөрлөг — /openday тур хуудасны бүх агуулгыг нэг дороос засах хуудас. */
?>
<link rel="stylesheet" href="/?incPageType=openday&amp;subPage=asset&amp;asset=floorplan.css&amp;v=<?php echo $odeAssetVer(false, "assets/css/floorplan.css");?>">
<link rel="stylesheet" href="/?incPageType=openday&amp;subPage=asset&amp;asset=editor.css&amp;v=<?php echo $odeAssetVer(true, "editor.css");?>">

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-7">
        <h2>Өдөрлөг <small>Open Office Day — зочдын тур хуудас</small></h2>
        <ol class="breadcrumb">
            <li><a href="/">Эхлэл</a></li>
            <li class="active"><strong>Өдөрлөг</strong></li>
        </ol>
    </div>
    <div class="col-sm-5 text-right ode-actions">
        <span id="ode-status" class="ode-status text-muted"></span>
        <a class="btn btn-white" href="<?php echo OpenDayCore::esc($odeConfig["publicUrl"]);?>" target="_blank" rel="noopener"><i class="fa fa-external-link"></i> Хуудсыг харах</a>
        <button type="button" class="btn btn-primary" id="ode-save" disabled><i class="fa fa-check"></i> Хадгалах</button>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp" id="ode-app">

    <div id="ode-alerts"></div>

<?php if (!empty($odeConfig["fallback"])) { ?>
    <div class="alert alert-danger">Өгөгдлийн сангаас уншиж чадсангүй — жишээ агуулга харагдаж байна. Хадгалахаас өмнө өгөгдлийн сангийн холболтыг шалгана уу.</div>
<?php } ?>

    <p class="text-muted ode-intro">
        Зочид QR уншуулж <b><?php echo OpenDayCore::esc($odeConfig["publicUrl"]);?></b> хуудсыг нээнэ. Энд оруулсан бүх зүйл тэнд шууд гарна.
        Монгол талбарыг заавал, англи талбарыг хүсвэл бөглөнө (хоосон бол монголоор харагдана).
        Өөрчлөлт хийсний дараа <b>Хадгалах</b> товчийг дарна.
    </p>

    <ul class="nav nav-tabs ode-tabs" role="tablist">
        <li class="active"><a href="#ode-general" data-pane="general"><i class="fa fa-info-circle"></i> Ерөнхий</a></li>
        <li><a href="#ode-agenda" data-pane="agenda"><i class="fa fa-clock-o"></i> Хөтөлбөр <span class="badge" data-count="agenda"></span></a></li>
        <li><a href="#ode-spot" data-pane="spot"><i class="fa fa-map-marker"></i> Схемийн цэг <span class="badge" data-count="spot"></span></a></li>
        <li><a href="#ode-activity" data-pane="activity"><i class="fa fa-list-ol"></i> Хийж болох зүйлс <span class="badge" data-count="activity"></span></a></li>
        <li><a href="#ode-info" data-pane="info"><i class="fa fa-th-large"></i> Практик мэдээлэл <span class="badge" data-count="info"></span></a></li>
        <li><a href="#ode-faq" data-pane="faq"><i class="fa fa-question-circle"></i> Түгээмэл асуулт <span class="badge" data-count="faq"></span></a></li>
    </ul>

    <div class="tab-content ode-panes">

        <!-- ============ Ерөнхий ============ -->
        <div class="tab-pane active" id="ode-general">
            <div class="ibox"><div class="ibox-content">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Огноо</label>
                        <input type="date" class="form-control" data-set="eventDate">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Эхлэх цаг</label>
                        <input type="time" class="form-control" data-set="startTime">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Дуусах цаг</label>
                        <input type="time" class="form-control" data-set="endTime">
                    </div>
                </div>
                <p class="help-block ode-help">Огноо, цагаар өдөрлөгийн өдөр хуудасны дээд хэсэгт "Одоо / Дараа нь" автоматаар гарна (Улаанбаатарын цагаар).</p>

                <div class="ode-pair">
                    <div class="form-group"><label>Дээд жижиг текст — MN</label><input type="text" class="form-control" maxlength="80" data-set="eyebrowMn"></div>
                    <div class="form-group"><label>Дээд жижиг текст — EN</label><input type="text" class="form-control" maxlength="80" data-set="eyebrowEn"></div>
                </div>
                <div class="ode-pair">
                    <div class="form-group"><label>Гарчиг — MN <span class="text-danger">*</span></label><input type="text" class="form-control" maxlength="120" data-set="titleMn"></div>
                    <div class="form-group"><label>Гарчиг — EN</label><input type="text" class="form-control" maxlength="120" data-set="titleEn"></div>
                </div>
                <div class="ode-pair">
                    <div class="form-group"><label>Байршил — MN</label><input type="text" class="form-control" maxlength="120" data-set="locationMn"></div>
                    <div class="form-group"><label>Байршил — EN</label><input type="text" class="form-control" maxlength="120" data-set="locationEn"></div>
                </div>
                <div class="ode-pair">
                    <div class="form-group"><label>Мэндчилгээ / танилцуулга — MN</label><textarea class="form-control" rows="4" maxlength="1200" data-set="introMn"></textarea></div>
                    <div class="form-group"><label>Мэндчилгээ / танилцуулга — EN</label><textarea class="form-control" rows="4" maxlength="1200" data-set="introEn"></textarea></div>
                </div>
                <div class="ode-pair">
                    <div class="form-group"><label>Хөл хэсгийн текст — MN</label><input type="text" class="form-control" maxlength="160" data-set="footerMn"></div>
                    <div class="form-group"><label>Хөл хэсгийн текст — EN</label><input type="text" class="form-control" maxlength="160" data-set="footerEn"></div>
                </div>
                <p class="help-block ode-help">Дэвсгэр зураг, лого, өнгө нь бүртгэлийн хуудасныхтай (/registration) ижил — "Арга хэмжээний бүртгэл > Хуудасны дизайн"-аас солигдоно.</p>
            </div></div>
        </div>

        <!-- ============ Хөтөлбөр ============ -->
        <div class="tab-pane" id="ode-agenda">
            <p class="text-muted ode-pane-help">Цагийн дарааллаар бичнэ. Байршил сонговол зочин тэр мөрөөс шууд схем дээр очиж харна.</p>
            <div class="ode-list" data-list="agenda"></div>
            <button type="button" class="btn btn-white" data-add="agenda"><i class="fa fa-plus"></i> Хөтөлбөр нэмэх</button>
        </div>

        <!-- ============ Схемийн цэг ============ -->
        <div class="tab-pane" id="ode-spot">
            <div class="row">
                <div class="col-lg-7">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="ode-maptools">
                                <div class="btn-group" id="ode-floor-tabs" role="group" aria-label="Давхар"></div>
                                <button type="button" class="btn btn-sm btn-white" id="ode-add-spot"><i class="fa fa-plus"></i> Цэг нэмэх</button>
                                <span class="text-muted ode-maphint" id="ode-maphint">Цэгийг чирж байрлуулна. Цэг нэмэхдээ товчийг дараад схем дээр дарна.</span>
                            </div>
                            <div class="fp-hero ode-map">
                                <div class="fp-stage fp-edit" data-fp-stage id="ode-stage">
                                    <div class="fp-floors" id="ode-floor-host"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="ode-list ode-spot-list" data-list="spot"></div>
                </div>
            </div>
        </div>

        <!-- ============ Хийж болох зүйлс ============ -->
        <div class="tab-pane" id="ode-activity">
            <p class="text-muted ode-pane-help">Тур маршрутын алхмууд — энэ дарааллаараа дугаарлагдаж харагдана. Цаг бичээгүй бол "Өдөржин" гэж гарна.</p>
            <div class="ode-list" data-list="activity"></div>
            <button type="button" class="btn btn-white" data-add="activity"><i class="fa fa-plus"></i> Алхам нэмэх</button>
        </div>

        <!-- ============ Практик мэдээлэл ============ -->
        <div class="tab-pane" id="ode-info">
            <p class="text-muted ode-pane-help">WiFi, хувцасны өлгүүр, тусламж гэх мэт карт. Мөр шилжүүлж бичиж болно.</p>
            <div class="ode-list" data-list="info"></div>
            <button type="button" class="btn btn-white" data-add="info"><i class="fa fa-plus"></i> Карт нэмэх</button>
        </div>

        <!-- ============ Түгээмэл асуулт ============ -->
        <div class="tab-pane" id="ode-faq">
            <div class="ode-list" data-list="faq"></div>
            <button type="button" class="btn btn-white" data-add="faq"><i class="fa fa-plus"></i> Асуулт нэмэх</button>
        </div>

    </div>
</div>

<script type="application/json" id="ode-data"><?php echo OpenDayCore::jsonForHtml($odeConfig);?></script>
<script src="/?incPageType=openday&amp;subPage=asset&amp;asset=core.js&amp;v=<?php echo $odeAssetVer(false, "assets/js/floorplan/core.js");?>"></script>
<script src="/?incPageType=openday&amp;subPage=asset&amp;asset=viewer.js&amp;v=<?php echo $odeAssetVer(false, "assets/js/floorplan/viewer.js");?>"></script>
<script src="/?incPageType=openday&amp;subPage=asset&amp;asset=editor.js&amp;v=<?php echo $odeAssetVer(true, "editor.js");?>"></script>
