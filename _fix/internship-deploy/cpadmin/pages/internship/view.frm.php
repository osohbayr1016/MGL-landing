<div class="modal-dialog modal-lg">
	<div class="modal-content animated bounceInRight">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
			<div class="modal-title">
				<?php if($applyEntry===null){ ?>
				Хүсэлт олдсонгүй
				<?php } else { ?>
				<?php echo ApplyCore::esc($applyEntry["entryName"]);?>
				<span class="label label-<?php echo $applyEntry["entryType"]=="job" ? "info" : "default";?>"><?php echo ApplyCore::esc(ApplyCore::typeName($applyEntry["entryType"]));?></span>
				<?php } ?>
			</div>
		</div>

		<?php if($applyEntry===null){ ?>
		<div class="modal-body">
			<p class="text-muted">Энэ хүсэлт устсан эсвэл олдсонгүй.</p>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
		</div>
		<?php } else { ?>

		<form id="applyViewForm" action="/userPost/internship" method="post">
		<div class="modal-body">

			<table class="table table-striped">
				<tbody>
				<?php
				foreach($applyViewFields as $field){

					switch($field["fieldCore"]){
						case "name":     $val = $applyEntry["entryName"];     break;
						case "phone":    $val = $applyEntry["entryPhone"];    break;
						case "email":    $val = $applyEntry["entryEmail"];    break;
						case "position": $val = $applyEntry["entryPosition"]; break;
						default:
							$val = isset($applyViewExtra[$field["fieldKey"]]) ? $applyViewExtra[$field["fieldKey"]] : "";
							if(is_array($val)) $val = implode(", ", $val);
						break;
					}

					if($field["fieldType"]=="file"){
						continue;
					}

					if(trim((string)$val)==""){
						continue;
					}
				?>
				<tr>
					<td style="width:230px"><strong><?php echo ApplyCore::esc($field["fieldLabel"]);?></strong></td>
					<td><?php echo nl2br(ApplyCore::esc($val));?></td>
				</tr>
				<?php } ?>
				<tr>
					<td><strong>Илгээсэн огноо</strong></td>
					<td><?php echo ApplyCore::esc($applyEntry["entryDate"]);?> <small class="text-muted">(IP: <?php echo ApplyCore::esc($applyEntry["entryIP"]);?>)</small></td>
				</tr>
				</tbody>
			</table>

			<?php if(count($applyViewFiles)>0){ ?>
			<div class="form-group">
				<label class="font-noraml">Хавсралт</label>
				<div>
					<?php foreach($applyViewFiles as $fi=>$fileObj){ ?>
					<a class="btn btn-sm btn-white" style="margin:0 6px 6px 0"
						href="/internship/file/<?php echo (int)$applyEntry["entryID"];?>?f=<?php echo (int)$fi;?>" target="_blank">
						<i class="fa fa-paperclip"></i> <?php echo ApplyCore::esc($fileObj["name"]);?>
						<small class="text-muted">(<?php echo ApplyCore::esc(ApplyCore::sizeText($fileObj["size"]));?>)</small>
					</a>
					<?php } ?>
				</div>
			</div>
			<?php } ?>

			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						<label class="font-noraml">Төлөв</label>
						<select class="form-control input-sm" name="frmState">
							<?php foreach($applyStates as $sk=>$sl){ ?>
							<option value="<?php echo $sk;?>"<?php if($applyEntry["entryState"]==$sk) echo ' selected';?>><?php echo ApplyCore::esc($sl);?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col-sm-8">
					<div class="form-group">
						<label class="font-noraml">Дотоод тэмдэглэл (зөвхөн админд харагдана)</label>
						<textarea class="form-control input-sm" name="frmNote" rows="3"><?php echo ApplyCore::esc($applyEntry["entryNote"]);?></textarea>
					</div>
				</div>
			</div>

		</div>
		<div class="modal-footer">
			<input type="hidden" name="frmPost" value="applyEntryState">
			<input type="hidden" name="frmEntryID" value="<?php echo (int)$applyEntry["entryID"];?>">
			<a class="btn btn-white pull-left" href="mailto:<?php echo ApplyCore::esc($applyEntry["entryEmail"]);?>"><i class="fa fa-envelope-o"></i> И-мэйл бичих</a>
			<button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
			<button type="submit" class="btn btn-primary">Хадгалах</button>
		</div>
		</form>

		<?php } ?>
	</div>
</div>
