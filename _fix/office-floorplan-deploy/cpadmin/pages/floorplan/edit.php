<?php
/** Офис схем — засварлагч (Офис хуудасны интерактив схемийн цэг, текст, камер). */

if (!function_exists("fpeCameraBlock")) {
	/** Нэг төхөөрөмжийн камерын талбарууд (desktop / mobile). */
	function fpeCameraBlock($set, $title, $hint)
	{
		?>
		<fieldset class="fpe-cam" data-set="<?php echo $set;?>">
			<legend><?php echo $title;?> <small class="text-muted"><?php echo $hint;?></small></legend>
			<div class="row">
				<div class="col-xs-12 fpe-row">
					<label>Zoom <span class="text-muted">(1.2 – 16)</span></label>
					<div class="fpe-pair">
						<input type="range" min="1.2" max="16" step="0.05" data-range-for="<?php echo $set;?>.zoom">
						<input type="number" class="form-control input-sm" min="1.2" max="16" step="0.05" data-field="<?php echo $set;?>.zoom">
					</div>
				</div>
				<div class="col-xs-12 fpe-row">
					<label>Offset X <span class="text-muted">(дэлгэцийн хувь, -0.4 – 0.4)</span></label>
					<div class="fpe-pair">
						<input type="range" min="-0.4" max="0.4" step="0.005" data-range-for="<?php echo $set;?>.offsetX">
						<input type="number" class="form-control input-sm" min="-0.4" max="0.4" step="0.005" data-field="<?php echo $set;?>.offsetX">
					</div>
				</div>
				<div class="col-xs-12 fpe-row">
					<label>Offset Y <span class="text-muted">(дэлгэцийн хувь, -0.4 – 0.4)</span></label>
					<div class="fpe-pair">
						<input type="range" min="-0.4" max="0.4" step="0.005" data-range-for="<?php echo $set;?>.offsetY">
						<input type="number" class="form-control input-sm" min="-0.4" max="0.4" step="0.005" data-field="<?php echo $set;?>.offsetY">
					</div>
				</div>
				<div class="col-xs-12 fpe-row">
					<label>Фокус цэг <span class="text-muted">(камер юуг төвлөх)</span></label>
					<div class="checkbox" style="margin:0 0 6px;"><label><input type="checkbox" data-field="<?php echo $set;?>.focusDot"> Цэгийн байршлыг дагах (өгөгдмөл)</label></div>
					<div class="fpe-pair2">
						<input type="number" class="form-control input-sm" min="0" max="1" step="0.0001" data-field="<?php echo $set;?>.focusX" placeholder="X">
						<input type="number" class="form-control input-sm" min="0" max="1" step="0.0001" data-field="<?php echo $set;?>.focusY" placeholder="Y">
					</div>
				</div>
			</div>
		</fieldset>
		<?php
	}
}
?>
<link rel="stylesheet" href="/?incPageType=floorplan&amp;subPage=asset&amp;asset=floorplan.css&amp;v=<?php echo $fpeAssetVer(false, "assets/css/floorplan.css");?>">
<link rel="stylesheet" href="/?incPageType=floorplan&amp;subPage=asset&amp;asset=editor.css&amp;v=<?php echo $fpeAssetVer(true, "editor.css");?>">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,500&amp;family=Montserrat:wght@500;600;700&amp;display=swap">

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-8">
        <h2>Офис схем</h2>
        <ol class="breadcrumb">
            <li><a href="/">Эхлэл</a></li>
            <li class="active"><strong>Офис схем</strong></li>
        </ol>
    </div>
    <div class="col-sm-4 text-right" style="padding-top:22px;">
        <span id="fpe-status" class="fpe-status text-muted"></span>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp" id="fpe-app">

    <div id="fpe-alerts"></div>

<?php if (!empty($fpeConfig["fallback"])) { ?>
    <div class="alert alert-danger">Өгөгдлийн сангаас схемийг уншиж чадсангүй — өгөгдмөл утга харагдаж байна. Хадгалахаас өмнө өгөгдлийн сангийн холболтыг шалгана уу.</div>
<?php } ?>

    <div class="row">
        <!-- ============ Схем (зүүн) ============ -->
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Схем</h5>
                </div>
                <div class="ibox-content">

                    <div class="fpe-toolbar">
                        <div class="btn-group" role="group" aria-label="Давхар" id="fpe-floor-tabs"></div>
                        <div class="btn-group" role="group" aria-label="Харагдац">
                            <button type="button" class="btn btn-sm btn-primary" data-mode="overview" title="Бүтэн схем — цэг чирж байрлуулах хамгийн тохиромжтой харагдац">Бүтэн схем</button>
                            <button type="button" class="btn btn-sm btn-white" data-mode="desktop" title="Компьютер дээр сонгогдсон өрөө хэрхэн харагдахыг үзэх">Компьютер</button>
                            <button type="button" class="btn btn-sm btn-white" data-mode="mobile" title="Гар утсан дээр сонгогдсон өрөө хэрхэн харагдахыг үзэх">Гар утас</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-white" id="fpe-add" title="Дараа нь схем дээр дарж цэг нэмнэ"><i class="fa fa-plus"></i> Цэг нэмэх</button>
                        <button type="button" class="btn btn-sm btn-white" id="fpe-undo" disabled title="Сүүлийн чирэлтийг буцаах"><i class="fa fa-undo"></i> Буцаах</button>
                        <span class="text-muted fpe-hint" id="fpe-hint">Цэгийг чирж байрлуулна. Сонгосон цэг дээр сумны товчоор нарийн зөөнө (Shift = том алхам).</span>
                    </div>

                    <div id="fpe-framewrap" class="fpe-framewrap">
                        <div id="fpe-frame" class="fp-hero fpe-frame">
                            <div class="fp-stage fp-edit" data-fp-stage>
                                <!-- the floor layer is rebuilt by editor.js for each floor -->
                                <div class="fp-floors" id="fpe-floor-host"></div>

                                <div class="fp-title"><p class="fp-title-text" id="fpe-title-preview"></p></div>

                                <div class="fp-info" data-fp-info aria-live="polite">
                                    <div class="fp-info-inner">
                                        <h2 class="fp-info-en" data-fp-en></h2>
                                        <p class="fp-info-mn" data-fp-mn></p>
                                        <p class="fp-info-desc" data-fp-desc></p>
                                    </div>
                                </div>

                                <div class="fp-nav" role="group" aria-label="Урьдчилсан харагдац">
                                    <button type="button" class="fp-btn" data-fp-prev aria-label="Өмнөх"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></button>
                                    <button type="button" class="fp-btn fp-btn-down" data-fp-back aria-label="Бүх схем рүү буцах"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 9l7 7 7-7"/></svg></button>
                                    <button type="button" class="fp-btn" data-fp-next aria-label="Дараах"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-muted fpe-note">
                        "Компьютер" / "Гар утас" харагдац нь сонгосон өрөөг жинхэнэ дэлгэцийн хэмжээгээр (1440×780 / 390×780) урьдчилан харуулна.
                        Камерын утгыг өөрчлөхөд урьдчилсан харагдац шууд шинэчлэгдэнэ.
                    </p>
                </div>
            </div>
        </div>

        <!-- ============ Жагсаалт + форм (баруун) ============ -->
        <div class="col-lg-4">

            <div class="ibox">
                <div class="ibox-title"><h5>Давхар <small id="fpe-floor-name"></small></h5></div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label>Давхрын гарчиг <span class="text-muted">(схемийн дээд хэсэгт гарна; дарахад нөгөө давхар руу шилжинэ)</span></label>
                        <input type="text" class="form-control" id="fpe-floor-title" maxlength="60">
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" id="fpe-plan-enabled"> Энэ давхрыг Офис хуудсанд харуулах</label>
                    </div>
                    <p class="text-muted fpe-stair-note" style="margin:0;">
                        <i class="fa fa-level-up"></i> Шат: <span id="fpe-stair-pos">—</span><br>
                        Схем дээрх шатны тэмдгийг чирж давхар хоорондын шатан дээр байрлуулна.
                        Зочин тэр тэмдэг эсвэл давхрын гарчиг дээр дарахад камер шат руу орж, нөгөө давхарт гарна.
                    </p>
                </div>
            </div>

            <div class="ibox">
                <div class="ibox-title"><h5>Цэгүүд <small id="fpe-count"></small></h5></div>
                <div class="ibox-content no-padding">
                    <ul class="fpe-list" id="fpe-list"></ul>
                </div>
            </div>

            <div class="ibox" id="fpe-form" hidden>
                <div class="ibox-title">
                    <h5 id="fpe-form-title">Цэг</h5>
                    <div class="ibox-tools">
                        <button type="button" class="btn btn-xs btn-white" id="fpe-up" title="Дараалалд дээш"><i class="fa fa-arrow-up"></i></button>
                        <button type="button" class="btn btn-xs btn-white" id="fpe-down" title="Дараалалд доош"><i class="fa fa-arrow-down"></i></button>
                        <button type="button" class="btn btn-xs btn-white" id="fpe-dup" title="Хувилах"><i class="fa fa-files-o"></i></button>
                        <button type="button" class="btn btn-xs btn-danger" id="fpe-del" title="Устгах"><i class="fa fa-trash"></i></button>
                    </div>
                </div>
                <div class="ibox-content">

                    <div class="checkbox" style="margin-top:0;">
                        <label><input type="checkbox" data-field="enabled"> Идэвхтэй (нийтэд харагдана)</label>
                    </div>

                    <div class="form-group">
                        <label>English</label>
                        <input type="text" class="form-control" data-field="titleEn" maxlength="80">
                    </div>
                    <div class="form-group">
                        <label>Монгол</label>
                        <input type="text" class="form-control" data-field="titleMn" maxlength="120">
                    </div>
                    <div class="form-group">
                        <label>Тайлбар — монгол <span class="text-muted">(богино; сайтын монгол хувилбар)</span></label>
                        <textarea class="form-control" rows="3" data-field="descriptionMn" maxlength="300"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Description — English <span class="text-muted">(сайтын англи хувилбар; хоосон бол монгол тайлбар гарна)</span></label>
                        <textarea class="form-control" rows="3" data-field="descriptionEn" maxlength="300"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Төрөл</label>
                                <select class="form-control input-sm" data-field="kind">
                                    <option value="room">Өрөө</option>
                                    <option value="team">Алба / баг</option>
                                    <option value="entrance">Орц</option>
                                    <option value="terrace">Террас</option>
                                    <option value="other">Бусад</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Slug <span class="text-muted">(?area=)</span></label>
                                <input type="text" class="form-control input-sm" data-field="slug" maxlength="48" pattern="[a-z0-9-]+">
                            </div>
                        </div>
                    </div>

                    <fieldset class="fpe-pos">
                        <legend>Байрлал <small class="text-muted">(зургийн харьцаа, 0 – 1)</small></legend>
                        <div class="fpe-pair2">
                            <div><label>X</label><input type="number" class="form-control input-sm" min="0" max="1" step="0.0001" data-field="x"></div>
                            <div><label>Y</label><input type="number" class="form-control input-sm" min="0" max="1" step="0.0001" data-field="y"></div>
                        </div>
                    </fieldset>

                    <?php fpeCameraBlock("desktop", "Компьютерын камер", "(өргөн дэлгэц)"); ?>
                    <?php fpeCameraBlock("mobile", "Гар утасны камер", "(босоо дэлгэц)"); ?>

                    <div class="fpe-previews">
                        <button type="button" class="btn btn-sm btn-white" id="fpe-preview-desktop"><i class="fa fa-desktop"></i> Компьютер</button>
                        <button type="button" class="btn btn-sm btn-white" id="fpe-preview-mobile"><i class="fa fa-mobile"></i> Гар утас</button>
                        <button type="button" class="btn btn-sm btn-white" id="fpe-preview-overview"><i class="fa fa-th-large"></i> Бүтэн схем</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="ibox">
        <div class="ibox-content fpe-savebar">
            <span id="fpe-errors" class="text-danger"></span>
            <span class="fpe-savebtns">
                <button type="button" class="btn btn-white" id="fpe-reset" disabled>Өөрчлөлтийг цуцлах</button>
                <button type="button" class="btn btn-primary" id="fpe-save" disabled>Хадгалах</button>
            </span>
        </div>
    </div>
</div>

<script type="application/json" id="fpe-data"><?php echo FloorPlanCore::jsonForHtml($fpeConfig);?></script>
<script src="/?incPageType=floorplan&amp;subPage=asset&amp;asset=core.js&amp;v=<?php echo $fpeAssetVer(false, "assets/js/floorplan/core.js");?>"></script>
<script src="/?incPageType=floorplan&amp;subPage=asset&amp;asset=viewer.js&amp;v=<?php echo $fpeAssetVer(false, "assets/js/floorplan/viewer.js");?>"></script>
<script src="/?incPageType=floorplan&amp;subPage=asset&amp;asset=editor.js&amp;v=<?php echo $fpeAssetVer(true, "editor.js");?>"></script>
