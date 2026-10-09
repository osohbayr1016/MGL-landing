<?php
/** Өдөрлөг — зочдын илгээсэн асуулт. questions.js 4 секунд тутам шинэчилнэ. */

$odqConfig = array(
	"csrf"    => openDayCsrf(),
	"feedUrl" => "/?incPageType=openday&subPage=qfeed",
	"postUrl" => "/userPost/openday"
);
?>
<link rel="stylesheet" href="/?incPageType=openday&amp;subPage=asset&amp;asset=editor.css&amp;v=<?php echo (int)@filemtime(__DIR__ . "/editor.css");?>">

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-7">
        <h2>Ирсэн асуулт <small>Open Office Day — зочид /openday хуудаснаас илгээсэн</small></h2>
        <ol class="breadcrumb">
            <li><a href="/">Эхлэл</a></li>
            <li>Өдөрлөг</li>
            <li class="active"><strong>Ирсэн асуулт</strong></li>
        </ol>
    </div>
    <div class="col-sm-5 text-right odq-head">
        <span class="odq-live" id="odq-live"><span class="odq-dot"></span> <span id="odq-live-text">Холбогдож байна…</span></span>
        <label class="odq-sound"><input type="checkbox" id="odq-sound"> <i class="fa fa-bell"></i> Дуугаар мэдэгдэх</label>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp" id="odq-app">
    <div id="odq-alerts"></div>

    <div class="odq-bar">
        <div class="btn-group" role="group" aria-label="Шүүлтүүр" id="odq-filter">
            <button type="button" class="btn btn-sm btn-primary" data-filter="new">Шинэ <span class="badge" data-count="new">0</span></button>
            <button type="button" class="btn btn-sm btn-white" data-filter="done">Хариулсан <span class="badge" data-count="done">0</span></button>
            <button type="button" class="btn btn-sm btn-white" data-filter="all">Бүгд <span class="badge" data-count="all">0</span></button>
        </div>
        <span class="text-muted odq-hint">Шинэ асуулт энд автоматаар нэмэгдэнэ — хуудсыг дахин ачаалах шаардлагагүй.</span>
    </div>

    <div class="odq-list" id="odq-list"></div>
    <div class="odq-empty text-muted" id="odq-empty" hidden>Одоогоор асуулт алга.</div>
</div>

<script type="application/json" id="odq-data"><?php echo OpenDayCore::jsonForHtml($odqConfig);?></script>
<script src="/?incPageType=openday&amp;subPage=asset&amp;asset=questions.js&amp;v=<?php echo (int)@filemtime(__DIR__ . "/questions.js");?>"></script>
