<?php
/*
	Харилцагч / түншүүдийн лого.

	Лого бүр нь энэ хэсгийн дэд бичлэг (db_pagesch), schNote нь
	{title, pic, link}. Линк өгсөн лого дээр дарахад тухайн компанийн
	вэб сайт нээгдэнэ. CP admin -> Мэдээлэл оруулах -> Харилцагч байгууллага.

	Хэсгийн тохиргоо (эцэг schNote):
		hover  = "0" бол хар цагаан -> өнгөт эффектийг унтраана
		newtab = "0" бол линкийг мөн цонхонд нээнэ
*/

$partnerHover  = !isset($selSchBody["hover"])  || $selSchBody["hover"]!="0";
$partnerNewTab = !isset($selSchBody["newtab"]) || $selSchBody["newtab"]!="0";
?>
<div class="wrapper">
	<div class="pageHeader" id="widhas<?php echo $objs["schID"]?>">
		<div class="header-bt wrapper">
			<h1><?php echo $selSchBody["title"];?></h1>
			 
		</div>
		<picture>
		<source type="image/jpeg" srcset="<?php echo newsPicFnc(0,$selSchBody["pic"]);?>">
		<img src="<?php echo newsPicFnc(0,$selSchBody["pic"]);?>">
		</picture>
	</div>
   	<div>
        <ul class="clients-list<?php if($partnerHover) echo " clients-hover";?>">
        	<?php
			if(count($objs["sub"])>0)
			foreach($objs["sub"] as $key=>$objss){
                $subSchBody = json_decode($objss["schNote"],true);

				$clientName = isset($subSchBody["title"]) ? $subSchBody["title"] : "";
				$clientPic  = isset($subSchBody["pic"])   ? $subSchBody["pic"]   : "";
				$clientLink = partnerLinkFnc(isset($subSchBody["link"]) ? $subSchBody["link"] : "");

				if($clientPic=="")
					continue;
			?>
            <li class="client">
				<?php if($clientLink!=""){ ?>
                <a href="<?php echo htmlspecialchars($clientLink);?>" title="<?php echo htmlspecialchars($clientName);?>"<?php if($partnerNewTab) echo ' target="_blank" rel="noopener noreferrer"';?>><img alt="<?php echo htmlspecialchars($clientName);?>" src="<?php echo newsPicFnc(0,$clientPic);?>"></a>
				<?php } else { ?>
                <span class="client-nolink"><img alt="<?php echo htmlspecialchars($clientName);?>" src="<?php echo newsPicFnc(0,$clientPic);?>"></span>
				<?php } ?>
            </li>
			<?php } ?>
        </ul>
    </div>
</div>
