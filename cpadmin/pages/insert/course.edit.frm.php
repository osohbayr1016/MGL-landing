
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Аялал оруулах</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/">Эхлэл</a>
            </li>
            <li>
                <a href="/insert/promo">Агуулга оруулах</a>
            </li>
            <li class="active">
                <strong>Аялалын бүртгэл</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated ">
	<form action="<?php echo "/userPost/insert";?>" method="post" enctype="multipart/form-data">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Үндсэн мэдээлэл </h5>
            </div>
            <div class="ibox-content">
                    
                <div class="form-group">
                    <label class="font-noraml">Аялалын статус</label>
                    <div >
                        <label><input type="checkbox"  name="frmStatus" <?php if($selNewObj["isPublish"]=="y") echo "checked"?> value="y"> Нээлттэй</label>
                    </div>
                </div>
               <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="font-noraml">Аялалын нэр</label>
                            <div >
                                <input type="text" class="form-control input-sm" name="frmTitle" value="<?php echo $selNewObj["courseTitle"];?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="font-noraml">Товчлол</label>
                            <div >
                                <input type="text" class="form-control input-sm" name="frmDesc" value="<?php echo $selNewObj["courseNote"];?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group" >
                            <label class="font-noraml">Ангилал</label>
                            <div >
                                <select class="chosen-select" name="frmCat" id="frmCat" >
                                    <option value="">Сонгох</option>
                                    <?php 
                                    foreach($typesArr as $key=>$obj){
                                    ?>
                                    <option <?php if($selNewObj["menuID"]==$obj["id"]) echo 'selected="selected"';?> value="<?php echo $obj["id"]?>"><?php echo $obj["name"]?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group" >
                            <label class="font-noraml">Хамааралтай үзэсгэлэнт газар сонгох</label>
                            <div >
                                <select class="chosen-select" multiple name="frmDest[]" id="frmDest" >
                                    <option value="">Сонгох</option>
                                    <?php 
                                    foreach($destArr as $key=>$obj){
                                    ?>
                                    <option <?php if($setlTourDestArr[$obj["ceoID"]]) echo 'selected="selected"';?> value="<?php echo $obj["ceoID"]?>"><?php echo $obj["ceoName"]?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                    	<div class="form-group" >
                            <label class="font-noraml">Аялах нийт км</label>
                            <div >
                                <input type="text" class="form-control input-sm" name="frmKm" value="<?php echo $selNewObj["tourKm"];?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                    	<div class="form-group" >
                            <label class="font-noraml">Тээврийн хэрэгслүүд</label>
                            <div >
                                <input type="text" class="form-control input-sm" name="frmCar" value="<?php echo $selNewObj["tourCar"];?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group" >
                            <label for="frmIncs" class="font-noraml">Аялалд багтсан зүйлс</label>
                            <div >
                                <select class="chosen-select" multiple name="frmInc[]" id="frmIncs" >
                                    <option value="">Сонгох</option>
                                    <?php 
                                    foreach($tourIncArr as $key=>$obj){
                                    ?>
                                    <option <?php if($selTourIncArr[$obj["id"]]) echo 'selected="selected"';?> value="<?php echo $obj["id"]?>"><?php echo $obj["name"]?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                    	<div class="form-group" >
                            <label for="frmNotInc" class="font-noraml">Аялалд багтаагүй зүйлс</label>
                            <div >
                                <select class="chosen-select" multiple name="frmNotInc[]" id="frmNotInc" >
                                    <option value="">Сонгох</option>
                                    <?php 
                                    foreach($tourIncArr as $key=>$obj){
                                    ?>
                                    <option <?php if($selTourNotIncArr[$obj["id"]]) echo 'selected="selected"';?> value="<?php echo $obj["id"]?>"><?php echo $obj["name"]?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group " >
                    <label class="font-noraml">Дэлгэрэнгүй танилцуулга</label>
                    <textarea class="wp-editor-area" style="height: 300px" autocomplete="off" cols="40" name="frmBody" id="contentTxtArea"><?php echo newsrollTextImg($selNewObj["courseBody"]);?></textarea>
                </div>
                <div class="form-group" >
                    <label class="font-noraml">Үндсэн зураг</label>
                    <div class="input-group">
                      <input type="text" name="frmPicUrl" id="postFileLink" value="<?php echo $selNewObj["coursePic"];?>" class="form-control" placeholder="Зураг...">
                      <span class="input-group-btn">
                        <button class="btn btn-default" onclick="open_popup('/assets/plugins/filemanager/dialog.php?popup=1&field_id=postFileLink')" type="button">Зураг сонгох</button>
                      </span>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Үнийн мэдээлэл </h5>
            </div>
            <div class="ibox-content">
                    
            <?php
            for($i=0;$i<5;$i++){
            ?> 
               <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="font-noraml">Хүний тоо</label>
                            <div >
                                <input type="text" class="form-control input-sm" name="frmPriceInd[]" value="<?php echo $selPriceInd[$i+1];?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="font-noraml">Үнэ $</label>
                            <div >
                                <input type="text" class="form-control input-sm" name="frmPriceVal[]" value="<?php echo $selPriceVal[$i+1];?>">
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>   
                
            </div>
        </div>
		<div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Аялалын өдрүүдийн мэдээлэл</h5>
                <div class="ibox-tools">
                    <a href="/insert/courseSch/<?php echo $courseID;?>" class="btn btn-primary btn-md accessModBtn">Өдөр нэмэх</a>
                </div>
            </div>
            <div class="ibox-content">                        
                
                <div class="panel-body">
                    <div id="courseListID">
					<?php include "course.sch.list.php"; ?>
                    </div>
                </div>
                
                
                
                
            
            <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <input name="frmPost" type="hidden" value="coursePost" />
                    <?php 
                    if($editID>0){
                    ?>
                    <input name="frmEditID" type="hidden" value="<?php echo $editID;?>" />
                    <?php } ?>
                    <button class="btn btn-primary" type="submit">Хадгалах</button>
                    <button class="btn btn-white" type="button">Болих</button>
                </div>
                
            </div>
        </div>
	</form>
        
</div>
        
<div class="modal  inmodal" id="orderModalFrm" tabindex="-1" role="dialog" aria-hidden="true">
    
</div>