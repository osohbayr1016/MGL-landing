<?php include "home.projects.css.php"; ?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-8">
        <h2>Нүүр хуудасны төслүүд</h2>
        <ol class="breadcrumb">
            <li><a href="/">Эхлэл</a></li>
            <li><a href="/insert/promo">Агуулга оруулах</a></li>
            <li class="active"><strong>Нүүр хуудасны төслүүд</strong></li>
        </ol>
    </div>
    <div class="col-sm-4 text-right" style="padding-top:20px;">
        <button type="button" id="hpSave" class="btn btn-primary" <?php if($hpSchId<1) echo "disabled";?>>Хадгалах</button>
    </div>
</div>
<div class="wrapper wrapper-content">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Нүүр хуудас — 4 мөр × 3 багана</h5>
        </div>
        <div class="ibox-content">
            <p class="text-muted">Чирж байрыг солино. Карт дээр дарж өөр төсөл эсвэл зураг сонгоно.</p>
            <?php if($hpSchId<1){ ?>
            <div class="alert alert-warning">Нүүр хуудасны төслийн хэсэг олдсонгүй. Эхлээд нүүр хуудсанд төслийн агуулга нэмнэ үү.</div>
            <?php } ?>
            <div id="hpGrid" class="hp-grid">
                <?php
                foreach ($hpSlots as $i => $obj) {
                    $isEmpty = ($obj === null);
                    $ceoId = $isEmpty ? 0 : (int)$obj["ceoID"];
                    $pic = $isEmpty ? "" : $obj["_pic"];
                    $name = $isEmpty ? "Төсөл сонгох" : $obj["ceoName"];
                    $brand = $isEmpty ? "" : (isset($obj["brandName"]) ? $obj["brandName"] : "");
                ?>
                <div class="hp-slot<?php if($isEmpty) echo " hp-empty";?>" data-ceo-id="<?php echo $ceoId;?>">
                    <span class="hp-index"><?php echo str_pad($i+1, 2, "0", STR_PAD_LEFT);?></span>
                    <img class="hp-img" alt=""<?php if($pic) echo ' src="'.htmlspecialchars($pic).'"';?>>
                    <div class="hp-copy">
                        <h4 class="hp-name"><?php echo htmlspecialchars($name);?></h4>
                        <span class="hp-brand"><?php echo htmlspecialchars($brand);?></span>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php include "home.projects.picker.php"; ?>
<input type="hidden" id="hpPicField" value="">
