<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Сайтын тохиргоо</h2>
        <ol class="breadcrumb">
            <li>
                <a href="<?php echo "/"?>">Эхлэл</a>
            </li>
             <li>
                <a href="<?php echo "/settings"?>">Тохиргоо</a>
            </li>
            <li class="active">
                <strong>Сайтын тохиргоо</strong>
            </li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content animated ">
		<div class="row">
        <form action="<?php echo "/userPost/settings";?>" method="post" enctype="multipart/form-data">
        
            <div class="col-lg-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Ерөнхий тохиргоо <small>* тэмдэглэсэн хэсгийг заавал бөглөнө үү</small></h5>
                        
                    </div>
                    <div class="ibox-content">
                       
						<div class="form-group" >
                            <label class="font-noraml">Сайтын гарчиг</label>
                            <div >
                                <input type="text" class="form-control" name="frmTitle" value="<?php echo $configObj["site_name"];?>">
                            </div>
                        </div>
                        <div class="form-group" >
                            <label class="font-noraml">Админы и-мэйл</label>
                            <div >
                                <input type="text" class="form-control" name="frmMail" value="<?php echo $configObj["adminMail"];?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-noraml">Хайлтын түлхүүр үгс</label>
                            <div >
                                <input type="text" class="form-control" name="frmKeys" value="<?php echo $configObj["keywords"];?>">
                            </div>
                        </div>  
						<div class="form-group" >
                            <label class="font-noraml">Сайтны тайлбар</label>
                            <div >
                                <textarea class="form-control" name="frmDes" ><?php echo $configObj["siteDes"];?></textarea>
                            </div>
                        </div> 	
                        <div class="form-group" >
                            <label class="font-noraml">Албан ёсны хаяг</label>
                            <div >
                                <input type="text" class="form-control" name="frmTL" value="<?php echo $configObj["socialTL"];?>">
                            </div>
                        </div>
                        <div class="form-group" >
                            <label class="font-noraml">Байршил GOOGLE</label>
                            <div >
                                <input type="text" class="form-control" name="frmWS" value="<?php echo $configObj["socialWS"];?>">
                            </div>
                        </div>
                        <div class="form-group" >
                            <label class="font-noraml">Холбоо барих и-мэйл</label>
                            <div >
                                <input type="text" class="form-control" name="frmVB" value="<?php echo $configObj["socialVB"];?>">
                            </div>
                        </div>
                        <div class="form-group" >
                            <label class="font-noraml">Шууд залгах утас</label>
                            <div >
                                <input type="text" class="form-control" name="frmPH" value="<?php echo $configObj["socialPhone"];?>">
                            </div>
                        </div>
                        <div class="form-group" >
                            <label class="font-noraml">Холбоо барих утас</label>
                            <div >
                                <input type="text" class="form-control" name="frmWC" value="<?php echo $configObj["socialWC"];?>">
                            </div>
                        </div> 					
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Сошиал мэдээлэл <small>* тэмдэглэсэн хэсгийг заавал бөглөнө үү</small></h5>
                        
                    </div>
                    <div class="ibox-content">
                       
						
                        <div class="form-group" >
                            <label class="font-noraml">Facebook хаяг</label>
                            <div >
                                <input type="text" class="form-control" name="frmFB" value="<?php echo $configObj["socialFB"];?>">
                            </div>
                        </div>
                        <div class="form-group" >
                            <label class="font-noraml">Twitter хаяг</label>
                            <div >
                                <input type="text" class="form-control" name="frmTW" value="<?php echo $configObj["socialTW"];?>">
                            </div>
                        </div>
                         <div class="form-group" >
                                <label class="font-noraml">Youtube хаяг</label>
                                <div >
                                    <input type="text" class="form-control" name="frmYT" value="<?php echo $configObj["socialYT"];?>">
                                </div>
                            </div> 
                        <div class="form-group" >
                            <label class="font-noraml">Instagram хаяг</label>
                            <div >
                                <input type="text" class="form-control" name="frmIN" value="<?php echo $configObj["socialIN"];?>">
                            </div>
                        </div>
                        <div class="form-group" >
                            <label class="font-noraml">pinterest хаяг</label>
                            <div >
                                <input type="text" class="form-control" name="frmTR" value="<?php echo $configObj["socialTR"];?>">
                            </div>
                        </div>
                         
                        
                        

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <input name="frmEditID" type="hidden" value="<?php echo $configObj["id"];?>" />
                            <input name="frmPost" type="hidden" value="<?php if($configObj["id"]!="") echo "edit"; else echo "add";?>" />
                            <button class="btn btn-primary" type="submit">Хадгалах</button>
                        </div>
                    </div>
                </div>
            </div>
         
        </form>   
        </div>
        
</div>