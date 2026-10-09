<?php
/**
 * Өдөрлөг — /openday хуудсыг ЯГ харагдах байдлаар нь дээр нь засах.
 * Доорх цонх (iframe) нь нийтийн хуудасны өөрийнх нь код — засах горимтой.
 */
?>
<link rel="stylesheet" href="/?incPageType=openday&amp;subPage=asset&amp;asset=editor.css&amp;v=<?php echo $odeAssetVer("editor.css");?>">

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-6">
        <h2>Өдөрлөг <small>хуудсыг дээр нь засах</small></h2>
        <ol class="breadcrumb">
            <li><a href="/">Эхлэл</a></li>
            <li>Өдөрлөг</li>
            <li class="active"><strong>Хуудас засах</strong></li>
        </ol>
    </div>
    <div class="col-sm-6 text-right ode-actions">
        <span id="ode-status" class="ode-status text-muted"></span>
        <button type="button" class="btn btn-white" id="ode-discard" disabled title="Хадгалаагүй бүх өөрчлөлтийг болих"><i class="fa fa-undo"></i> Болих</button>
        <a class="btn btn-white" href="<?php echo OpenDayCore::esc($odeConfig["publicUrl"]);?>" target="_blank" rel="noopener"><i class="fa fa-external-link"></i> Хуудсыг харах</a>
        <button type="button" class="btn btn-primary" id="ode-save" disabled><i class="fa fa-check"></i> Хадгалах</button>
    </div>
</div>

<div class="wrapper wrapper-content" id="ode-app">

<?php if (!empty($odeConfig["fallback"])) { ?>
    <div class="alert alert-danger">Өгөгдлийн сангаас уншиж чадсангүй — жишээ агуулга харагдаж байна. Хадгалахаас өмнө өгөгдлийн сангийн холболтыг шалгана уу.</div>
<?php } ?>

    <div class="ode-toolbar">
        <div class="btn-group" role="group" aria-label="Хуудас" id="ode-pages">
            <button type="button" class="btn btn-sm btn-white" data-page="home"><i class="fa fa-home"></i> Нүүр</button>
            <button type="button" class="btn btn-sm btn-white" data-page="schedule"><i class="fa fa-clock-o"></i> Хөтөлбөр</button>
            <button type="button" class="btn btn-sm btn-white" data-page="map"><i class="fa fa-map-marker"></i> Схем</button>
            <button type="button" class="btn btn-sm btn-white" data-page="info"><i class="fa fa-info-circle"></i> Мэдээлэл</button>
            <button type="button" class="btn btn-sm btn-white" data-page="faq"><i class="fa fa-question-circle"></i> Асуулт</button>
        </div>
        <div class="btn-group" role="group" aria-label="Засах хэл" id="ode-langs" title="Аль хэлний текстийг засах">
            <button type="button" class="btn btn-sm btn-white" data-lang="mn">MN</button>
            <button type="button" class="btn btn-sm btn-white" data-lang="en">EN</button>
        </div>
        <div class="btn-group" role="group" aria-label="Дэлгэц" id="ode-devices">
            <button type="button" class="btn btn-sm btn-white" data-device="desktop" title="Компьютер"><i class="fa fa-desktop"></i></button>
            <button type="button" class="btn btn-sm btn-white" data-device="phone" title="Гар утас"><i class="fa fa-mobile"></i></button>
        </div>
        <span class="ode-help text-muted"><i class="fa fa-hand-pointer-o"></i> Текст дээр дарж шууд бичнэ · мөр дээр очиход <i class="fa fa-arrow-up"></i> <i class="fa fa-arrow-down"></i> <i class="fa fa-eye-slash"></i> <i class="fa fa-trash"></i> гарна · огноо, цаг, байршил, icon дээр дарж сонгоно</span>
    </div>

    <div id="ode-alerts"></div>

    <div class="ode-stagewrap">
        <div class="ode-device" id="ode-device" data-device="desktop">
            <div class="ode-loading" id="ode-loading"><i class="fa fa-spinner fa-spin"></i></div>
        </div>
    </div>
</div>

<script type="application/json" id="ode-data"><?php echo OpenDayCore::jsonForHtml($odeConfig);?></script>
<script src="/?incPageType=openday&amp;subPage=asset&amp;asset=editor.js&amp;v=<?php echo $odeAssetVer("editor.js");?>"></script>
