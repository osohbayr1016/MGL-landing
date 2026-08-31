<link href="/assets/plugins/tagsinput/bootstrap-tagsinput.css" rel="stylesheet">
<script src="/assets/plugins/tagsinput/bootstrap-tagsinput.js"></script>
<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">Ангилал</div>
        </div>
        <form action="/userPost/info" method="post">
        <div class="modal-body">
            
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Гарчиг</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmName" value="<?php echo $selTypeObj["name"];?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <label class="font-noraml">Төлөв</label><br />
                    <div class="btn-group" data-toggle="buttons">

                      <div class="btn btn-default btn-sm <?php if($selTypeObj["cityIs"]==1) echo "active"?>">
                        <input type="radio" name="frmType" value="1" <?php if($selTypeObj["cityIs"]==1) echo "checked=\"checked\""?> autocomplete="off"> Үндсэн
                      </div>

                      <div class="btn btn-default btn-sm <?php if($selTypeObj["cityIs"]==2) echo "active"?>">
                        <input type="radio" name="frmType" value="2" <?php if($selTypeObj["cityIs"]==2) echo "checked=\"checked\""?> autocomplete="off"> Туслах
                      </div>
                      
                    </div>
                </div>                    
           </div>
           <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Төрөл</label>
                        <div >
                           <select class="form-control input-sm" name="frmTypes">
                           	<option value="t">Текст</option>
                            <option value="l">Тайлбар</option>
                            <option value="d">Огноо</option>
                            <option value="s">Сонголт</option>
                            <option value="v">Тийм үгүй</option>
                            <option value="e">Чагт</option>
                           </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-noraml">Эрэмбэ</label>
                        <div >
                            <input type="text" class="form-control input-sm" name="frmOrder" value="<?php echo $lastTypeOrder;?>">
                        </div>
                    </div>
                </div>                   
           </div>
           <hr />
           <div class="row">
                <div class="col-lg-3">
                    <label class="font-noraml">Эрэмбэ</label>
                    <br />
                    <input  class="form-control input-sm" type="text"  name="frmTags" value="<?php echo $selTypeObj["typeKeys"];?>" />
                </div>
                <div class="col-lg-6">
                    <label class="font-noraml">Сонголт</label>
                    <br />
                    <input  class="form-control input-sm" type="text"  name="frmTags" value="<?php echo $selTypeObj["typeKeys"];?>" />
                </div>
                <div class="col-lg-3">
                    <label class="font-noraml">&nbsp;</label>
                    <br />
                    <input  class="btn btn-primary btn-sm" type="button" value="Хадгалах" />
                </div>
            </div>
            <hr />
            <div>
            <table class="table table-striped table-bordered table-hover dataTables-chois">
                <thead>
                <tr>
                    <th>Эрэмбэ</th>
                    <th>Сонголт</th>
                    <th > </th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(count($citiesArr)>0)
                foreach($citiesArr as $key=>$obj){
                ?>
                <tr>
                    <td class="project-status">
                        <span class="label label-primary"><?php echo $gloCityStatus[$obj["cityIs"]]?></span>
                    </td>
                    <td class="project-title">
                        <?php echo $obj["cityName"];?>
                    </td>
                    <td class="project-actions">                                        
                        <a href="<?php echo "/insert/mapEdit/".$obj["cityID"];?>" class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i></a>
                        <a href="#" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="detailPost" />
            <?php 
			if($editDetailID>0){
			?>
			<input name="frmEditID" type="hidden" value="<?php echo $editDetailID;?>" />
			<?php } ?>
            <input name="frmTypeID" type="hidden" value="<?php echo $selTypeID;?>" />
                            
            <button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
            <button type="submit" class="btn btn-primary">Хадгалах</button>
        </div>
        </form>
    </div>
</div>
<link href="/assets/plugins/dataTables/css/dataTables.bootstrap.css" rel="stylesheet">
<link href="/assets/plugins/dataTables/css/dataTables.responsive.css" rel="stylesheet">
<link href="/assets/plugins/dataTables/css/dataTables.tableTools.min.css" rel="stylesheet">
<script src="/assets/plugins/dataTables/js/jquery.dataTables.js"></script>
<script src="/assets/plugins/dataTables/js/dataTables.bootstrap.js"></script>
<script src="/assets/plugins/dataTables/js/dataTables.responsive.js"></script>
<script src="/assets/plugins/dataTables/js/dataTables.tableTools.min.js"></script>
    
<script>
$(document).ready(function() {
	
	$('.dataTables-chois').dataTable({
		bFilter: false, bInfo: false, bPaginate: false,
		responsive: true,
		"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 2 ] }
       ]
	});
	
});
</script>