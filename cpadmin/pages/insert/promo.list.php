<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Хуудасны агуулга</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/">Эхлэл</a>
            </li>
            <li>
                <a href="/insert/promo">Агуулга оруулах</a>
            </li>
            <li class="active">
                <strong>Хуудасны агуулга</strong>
            </li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInUp">
<div class="row">
	<div class="col-md-3">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <div class="file-manager">
					<?php
					foreach($gloMenuType as $menuType=>$menuName){
					?>
                    <div class="hr-line-dashed"></div>
                    <h5><?php echo $menuName;?></h5>
                    <ul class="folder-list" style="padding: 0">
                    <?php
                    if(count($menuArr[$menuType])>0)
					foreach($menuArr[$menuType] as $key=>$obj){
					?>
                        <li <?php if($obj["id"]==$selMenuID) echo "class=\"active\"";?>><a href="<?php echo "/insert/promo/".$obj["id"];?>"><i class="fa fa-folder"></i> <?php echo $obj["name"];?></a></li>
                        <?php
							if(count($obj["sub"])>0)
							foreach($obj["sub"] as $keys=>$objs){
							?>
								<li style="padding-left:20px;" <?php if($objs["id"]==$selMenuID) echo "class=\"active\"";?>><a href="<?php echo "/insert/promo/".$objs["id"];?>"><i class="fa fa-file"></i> <?php echo $objs["name"];?></a></li>
							<?php } ?>
					<?php } ?>
                    </ul>
                    <?php } ?>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-9">
    
    	<?php
		switch($selMenuObj["pageType"]){
			case "video":
				include "page.content.php";
				include "videos.content.php";
			break;
			case "projects":
				include "page.content.php";
				include "persons.content.php";
			break;
			default:
				include "page.content.php";
			break;
		}
		?>
        
        
            
    </div>
</div>
</div>
<div class="modal inmodal" id="orderModalFrm" tabindex="-1" role="dialog" aria-hidden="true">
    
</div>