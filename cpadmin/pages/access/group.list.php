<div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-4">
                    <h2>Эрхийн бүлэг</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/">Эхлэл</a>
                        </li>
                        <li>
                            <a href="/access">Хандах эрх</a>
                        </li>
                        <li class="active">
                            <strong>Эрхийн бүлэг</strong>
                        </li>
                    </ol>
                </div>
            </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp">

                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Эрхийн бүлгийн жагсаалт</h5>
                            <div class="ibox-tools">
                                <a href="/access/add" class="btn btn-primary btn-xs accessModBtn">Шинэ бүлэг нэмэх</a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            

                            <div class="project-list">

                                <table class="table table-hover">
                                	<thead>
                                	<tr>
                                        <th>Нэр</th>
                                        <th>Хандах эрх</th>
                                        <th> </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
									if(count($groupArr)>0)
                                    foreach($groupArr as $key=>$obj){
										
										$accessArr = explode("-",$obj["adminGroupAction"]);
										
									?>
                                    <tr>
                                        <td class="project-title">
                                            <?php echo $obj["adminGroupName"];?>
                                        </td>
                                        <td class="project-title">
                                            <?php 
											if(count($accessArr)>0)
											foreach($accessArr as $k=>$o){
												if($o!=""){
													$objA = explode("_",$o);
											?>
                                            <div><?php echo "<b>".$gloMenuArr[$objA[0]]["label"]."</b> > ".$gloMenuArr[$objA[0]]["sub"][$objA[1]];?></div>
                                            <?php } } ?>
                                        </td>	
                                        <td class="project-actions">                                        
                                            <a href="<?php echo "/access/edit/".$obj["adminGroupID"];?>" class="btn btn-warning btn-sm accessModBtn"><i class="fa fa-pencil"></i> засах </a>
                                            <a href="<?php echo "/access/del/".$obj["adminGroupID"];?>" class="btn btn-danger btn-sm accessModBtn"><i class="fa fa-trash"></i> устгах </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
<div class="modal inmodal" id="accessModalFrm" tabindex="-1" role="dialog" aria-hidden="true">
    
</div>