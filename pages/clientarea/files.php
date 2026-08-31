<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Файлын жагсаалт</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/clientarea">Эхлэл</a>
            </li>
            <li class="active">
                <strong>Файлын жагсаалт</strong>
            </li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content">

    <table class="footable table table-hover" data-page-size="15">
        <thead>
        <tr>
            <th data-hide="">№</th>
            <th >Нэр</th>
            <th >Файлын хэмжээ</th>
            <th >Татах нууц үг</th>
            <th  >Оруулсан огноо</th>
            <th class="text-right"> </th>

        </tr>
        </thead>
        <tbody>
            <?php
            if(count($fileList)>0)
            foreach($fileList as $key=>$obj){
            ?>
            <tr>
                <td>
                   <?php echo ($key+1);?>
                </td>
                <td>
                    <?php echo $obj["fileName"]?>
                </td>
                <td>
                <?php echo $obj["fileSize"]?>
                </td>
                <td>
                <div class="input-group">
                    <input type="text" readonly id="filepass<?php echo $obj["id"]?>" class="form-control" value="<?php echo $obj["filepass"]?>">
                    <div class="input-group-btn">
                        <button type="button" onclick="copyPassBtn('filepass<?php echo $obj["id"]?>')" class="btn btn-default">Хуулах</button>
                    </div>
                </div>
                </td>
                <td>
                <?php echo $obj["createDate"]?>
                </td>
                <td class="text-right">
                    <div class="btn-group">
                        <a href="/clientfile/<?php echo $obj["id"];?>" target="_blank" class="btn-warning btn btn-sт">Татах</a>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
                    
</div>

<script>

function copyPassBtn(inputID) {
    /* Get the text field */
    var copyText = document.getElementById(inputID);

    /* Select the text field */
    copyText.select();
    copyText.setSelectionRange(0, 99999); /* For mobile devices */

    /* Copy the text inside the text field */
    navigator.clipboard.writeText(copyText.value);

    /* Alert the copied text */
    alert("Copied the text: " + copyText.value);
}
</script>