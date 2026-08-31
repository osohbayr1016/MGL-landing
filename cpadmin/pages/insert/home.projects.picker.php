<div class="modal inmodal" id="hpPicker" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Төсөл сонгох</h4>
            </div>
            <div class="modal-body">
                <input type="text" id="hpPickerSearch" class="form-control" placeholder="Хайх..." style="margin-bottom:16px;">
                <div class="hp-picker-grid">
                    <?php foreach ($hpCatalog as $item) { ?>
                    <button type="button" class="hp-pick" data-ceo-id="<?php echo (int)$item["id"];?>" data-name="<?php echo htmlspecialchars($item["name"]);?>">
                        <img src="<?php echo htmlspecialchars($item["pic"]);?>" alt="">
                        <span><?php echo htmlspecialchars($item["name"]);?></span>
                    </button>
                    <?php } ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="hpChangePic" class="btn btn-warning">Зураг солих</button>
                <button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
            </div>
        </div>
    </div>
</div>
