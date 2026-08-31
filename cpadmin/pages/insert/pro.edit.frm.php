
<script language="JavaScript" type="text/javascript">
function BrowseServer(){
	
	var finder = new CKFinder() ;
	finder.BasePath = 'editor/ckfinder/';	
	finder.selectActionFunction = SetFileField ;
	finder.popup();
	
}
function SetFileField( fileUrl )
{
	document.getElementById( 'postFileLink' ).value = fileUrl ;
}
</script>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Мэдээ оруулах</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/">Эхлэл</a>
            </li>
            <li>
                <a href="/insert/pro">Мэдээний жагсаалт</a>
            </li>
            <li class="active">
                <strong>Мэдээ оруулах</strong>
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
                            <label class="font-noraml">Гарчиг</label>
                            <div >
                                <input type="text" class="form-control input-sm" name="frmTitle" value="<?php echo $selNewObj["newsTitle"];?>">
                            </div>
                        </div>
                       <div class="form-group" >
                            <div><label >Төрөл </label></div>
                            <div class="btn-group btn-group-justified" data-toggle="buttons">
                              <div class="btn btn-primary btn-sm <?php if($selNewObj["newsType"]=="n") echo 'active';?>">
                                <input type="radio" name="frmType" <?php if($selNewObj["newsType"]=="n") echo 'checked="checked"';?> value="n"  autocomplete="off"> Мэдээ
                              </div>
                               <div class="btn btn-primary btn-sm <?php if($selNewObj["newsType"]=="y") echo 'active';?>">
                                <input type="radio" name="frmType" <?php if($selNewObj["newsType"]=="y") echo 'checked="checked"';?> value="y"  autocomplete="off"> Ярилцлага
                              </div>  
                              <div class="btn btn-primary btn-sm <?php if($selNewObj["newsType"]=="v") echo 'active';?>">
                                <input type="radio" name="frmType" <?php if($selNewObj["newsType"]=="v") echo 'checked="checked"';?> value="v"  autocomplete="off"> Видео
                              </div>         
                              <div class="btn btn-primary btn-sm <?php if($selNewObj["newsType"]=="p") echo 'active';?>">
                                <input type="radio" name="frmType" <?php if($selNewObj["newsType"]=="p") echo 'checked="checked"';?> value="p"  autocomplete="off"> Фото
                              </div>  
                              <div class="btn btn-primary btn-sm <?php if($selNewObj["newsType"]=="s") echo 'active';?>">
                                <input type="radio" name="frmType" <?php if($selNewObj["newsType"]=="s") echo 'checked="checked"';?> value="s"  autocomplete="off"> Слайд
                              </div>  
                              <div class="btn btn-primary btn-sm <?php if($selNewObj["newsType"]=="l") echo 'active';?>">
                                <input type="radio" name="frmType" <?php if($selNewObj["newsType"]=="l") echo 'checked="checked"';?> value="l"  autocomplete="off"> Live
                              </div> 
                            </div>
                            <small class="frmErr">Төрөл сонгоно уу</small>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group" >
                                    <label class="font-noraml">Үндсэн ангилал</label>
                                    <div >
                                        <select class="chosen-select" name="frmCat" id="frmCat" >
                                        	<option value="">Сонгох</option>
											<?php 
                                            foreach($typesArr as $key=>$obj){
                                            ?>
                                        	<option <?php if($selMainCat==$obj["id"]) echo 'selected="selected"';?> value="<?php echo $obj["id"]?>"><?php echo $obj["name"]?></option>
                                        	<?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group" >
                                    <label class="font-noraml">Дэд ангилал</label>
                                    <div >
                                        <select class="chosen-select" name="frmSubCat" id="frmSubCat" >
                                        	<option value="">Сонгох</option>
                                            <?php 
											if(count($subTypesArr)>0)
                                            foreach($subTypesArr as $key=>$obj){
                                            ?>
                                        	<option <?php if($selNewObj["newsSubCat"]==$obj["id"]) echo 'selected="selected"';?> value="<?php echo $obj["id"]?>"><?php echo $obj["name"]?></option>
                                        	<?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">                            
                            <div class="col-sm-6">
                                <div class="form-group" >
                                <label class="font-noraml">Топ мэдээ</label>
                                <div >
                                    <select class="chosen-select" id="frmTop" name="frmTop" >
                                        <option value="">Топ мэдээ биш</option>
                                        <?php 
                                        for($i=1;$i<6;$i++){
                                        ?>
                                        <option <?php if($selNewObj["newsTop"]==$i) echo 'selected="selected"';?> value="<?php echo $i?>">Топ <?php echo $i?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group" >
                                    <label class="font-noraml">Онцлох мэдээ</label>
                                    <div >
                                        <select class="chosen-select" name="frmSep"  >
                                        	<option value="">Онцлох мэдээ биш</option>
                                        	<?php 
											for($i=1;$i<4;$i++){
											?>
											<option <?php if($selNewObj["newsSep"]=="s".$i) echo 'selected="selected"';?> value="s<?php echo $i?>">Онцлох <?php echo $i?></option>
											<?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group" >
                            <label class="font-noraml">Мэдээний товч хэсэг</label>
                            <div >
                                <textarea name="frmDesc" class="form-control input-sm"><?php echo $selNewObj["newsDesc"];?></textarea>
                            </div>
                        </div>
                        
                    <label class="font-noraml">Дэлгэрэнгүй танилцуулга</label>
                       <div class="form-group " >
                       <textarea class="wp-editor-area" style="height: 300px" autocomplete="off" cols="40" name="frmBody" id="contentTxtArea"><?php echo newsrollTextImg($selNewObj["newsBody"]);?></textarea>
                       
						</div>
                        <div class="form-group" >
                            <div><label class="font-noraml">Сэтгэгдэл авах</label></div>
                            <div >
							 <label><input type="checkbox" name="frmCmd" value="y" <?php if($selNewObj["cmdIs"]!="n"){?> checked="checked" <?php } ?> /> тийм</label>
                            </div>
                        </div>
                        <div class="form-group" >
                            <div><label class="font-noraml">Видео embed code</label></div>
                            <div >
							 <textarea name="frmVideo" class="form-control input-sm"><?php echo $selNewObj["newsVideo"];?></textarea>
                            </div>
                        </div>
                        <div class="form-group" >
                            <label class="font-noraml">Зураг</label>
                            <div >
                                <input type="file" class="form-control" name="frmNewsPic" value="">
                            </div>
                        </div>
                        
                        <div class="form-group" >
                            <label class="font-noraml">Слайд видео</label>
                            <div class="input-group">
                              <input type="text" name="frmVidUrl" id="postVidLink" value="<?php echo $selNewObj["newsVid"];?>" class="form-control" placeholder="Видео...">
                              <span class="input-group-btn">
                                <button class="btn btn-default" onclick="open_popup('/assets/plugins/filemanager/dialog.php?popup=1&field_id=postVidLink')" type="button">Видео сонгох</button>
                              </span>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <input name="frmPost" type="hidden" value="prodPost" />
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
