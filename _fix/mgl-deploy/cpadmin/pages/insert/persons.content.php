<div class="ibox">
    <div class="ibox-title">
        <h5>Бүтээгдэхүүний жагсаалт</h5>
    </div>
    <div class="ibox-content">
        <p class="text-muted">Дэс дугаар оруулж эрэмбэлнэ. Жишээ нь <strong>1</strong> — эхэнд, <strong>2</strong> — дараагийн байр.</p>
        <div class="project-list">
            <table id="ceoOrderTable" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th style="width:90px;">Дэс дугаар</th>
                    <th>Зураг</th>
                    <th>Бүтээгдэхүүн</th>
                    <th>Үзүүлэлт</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(count($ceoArr)>0)
                foreach($ceoArr as $key=>$obj){
                ?>
                <tr id="proTr<?php echo $obj["ceoID"]?>" data-ceo-id="<?php echo $obj["ceoID"]?>">
                    <td style="vertical-align:middle;">
                        <input type="number"
                            min="1"
                            class="form-control input-sm js-order-input"
                            data-post="ceoOrderSet"
                            data-ceo-id="<?php echo $obj["ceoID"];?>"
                            value="<?php echo (int)$obj["ceoOrder"];?>"
                            style="width:72px;">
                    </td>
                    <td>
                        <img src="<?php echo $obj["ceoPic"];?>" width="100" />
                    </td>
                    <td>
                        <strong><?php echo $obj["ceoName"];?></strong>
                        <div><?php echo $obj["ceoDegree"];?></div>
                    </td>
                    <td>
                        <?php echo $obj["ceoDesc"];?>
                    </td>
                    <td class="project-actions">
                        <a href="<?php echo "/insert/aboutCeo/".$obj["ceoID"]."?menuID=".$selMenuID;?>" class="btn btn-warning btn-sm  accessModBtn"><i class="fa fa-pencil"></i></a>
                        <a href="/insert/ceoDel/<?php echo $obj["ceoID"];?>" class="btn btn-danger btn-sm accessModBtn"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <a href="/insert/aboutCeo/<?php echo "?menuID=".$selMenuID;?>" class="btn btn-primary accessModBtn">Бүтээгдэхүүн нэмэх</a>
    </div>
</div>
