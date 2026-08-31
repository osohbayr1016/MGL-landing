<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">Widget</div>
        </div>
        <form action="/userPost/info" enctype="multipart/form-data" method="post">
        <div class="modal-body">
            
		    <div class="form-group" >
				<label class="font-noraml">Нэр</label>
				<div >
					<input type="text" class="form-control input-sm" name="frmTitle" value="<?php echo $selWidgetObj["widgetTitle"];?>">
				</div>
			</div>  
            <div class="ibox float-e-margins">
        		<div class="ibox-title">
					<h5>Үндсэн мэдээлэл </h5>                      
				</div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group" >
                                <div><label class="font-noraml">Гарчиг</label></div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group" >
                                <label class="font-noraml">Key</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group" >
                                <label class="font-noraml">Төрөл</label>
                            </div>
                        </div>
                    </div>
                    <?php
                    for($i=0;$i<10;$i++){
                    ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="text" class="form-control input-sm" name="frmMainTitle[]" value="<?php echo $selMainRowArr[$i]["colName"];?>">
                        </div>
                        <div class="col-sm-3">
                            <input type="text" class="form-control input-sm" name="frmMainKey[]" value="<?php echo $selMainRowArr[$i]["colKey"];?>">
                        </div>
                        <div class="col-sm-3">
                            <select class="form-control input-sm" name="frmMainType[]" >
                                <option <?php if($selMainRowArr[$i]["colType"]=="text") echo "selected"?> value="text">Текст</option>
                                <option <?php if($selMainRowArr[$i]["colType"]=="file") echo "selected"?> value="file">Файл</option>
                                <option <?php if($selMainRowArr[$i]["colType"]=="textarea") echo "selected"?> value="textarea">Textarea</option>
                                <option <?php if($selMainRowArr[$i]["colType"]=="editor") echo "selected"?> value="editor">editor</option>
                                <option <?php if($selMainRowArr[$i]["colType"]=="loc") echo "selected"?> value="loc">Байршил</option>
                                <option <?php if($selMainRowArr[$i]["colType"]=="radio") echo "selected"?> value="radio">Тийм/Үгүй</option>
                            </select>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="form-group" >
                <div><label >Дэд агуулгатай эсэх </label></div>
                <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <div class="btn btn-primary btn-sm <?php if($selWidgetObj["widgetSub"]) echo 'active';?>">
                        <input type="radio" name="frmIsSub" <?php if($selWidgetObj["widgetSub"]) echo 'checked="checked"';?> value="1"  autocomplete="off"> Тийм
                    </div>
                    <div class="btn btn-primary btn-sm <?php if(!$selWidgetObj["widgetSub"]) echo 'active';?>">
                        <input type="radio" name="frmIsSub" <?php if(!$selWidgetObj["widgetSub"]) echo 'checked="checked"';?> value="0"  autocomplete="off"> Үгүй
                    </div>   
                </div>
            </div>
            <div class="ibox float-e-margins">
        		<div class="ibox-title">
					<h5>Дэд агуулга мэдээлэл </h5>                      
				</div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group" >
                                <div><label class="font-noraml">Гарчиг</label></div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group" >
                                <label class="font-noraml">Key</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group" >
                                <label class="font-noraml">Төрөл</label>
                            </div>
                        </div>
                    </div>
                    <?php
                    for($i=0;$i<10;$i++){
                    ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="text" class="form-control input-sm" name="frmSubTitle[]" value="<?php echo $selSubRowArr[$i]["colName"];?>">
                        </div>
                        <div class="col-sm-3">
                            <input type="text" class="form-control input-sm" name="frmSubKey[]" value="<?php echo $selSubRowArr[$i]["colKey"];?>">
                        </div>
                        <div class="col-sm-3">
                            <select class="form-control input-sm" name="frmSubType[]" >
                            <option <?php if($selSubRowArr[$i]["colType"]=="text") echo "selected"?> value="text">Текст</option>
                                <option <?php if($selSubRowArr[$i]["colType"]=="file") echo "selected"?> value="file">Файл</option>
                                <option <?php if($selSubRowArr[$i]["colType"]=="textarea") echo "selected"?> value="textarea">Textarea</option>
                                <option <?php if($selSubRowArr[$i]["colType"]=="editor") echo "selected"?> value="editor">editor</option>
                                <option <?php if($selSubRowArr[$i]["colType"]=="loc") echo "selected"?> value="loc">Байршил</option>
                            </select>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="widgetPost" />
            <?php 
			if($selWidgetID>0){
			?>
			<input name="frmEditID" type="hidden" value="<?php echo $selWidgetID;?>" />
			<?php } ?>
                            
            <button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
            <button type="submit" class="btn btn-primary">Хадгалах</button>
        </div>
        </form>
    </div>
</div>
    
