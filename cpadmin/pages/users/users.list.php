<div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-12">
                    <h2>Бүртгэл</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/">Эхлэл</a>
                        </li>
                        <li class="active">
                            <strong>Бүртгэл</strong>
                        </li>
                    </ol>
                </div>
            </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp">

                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Харилцагчийн жагсаалт</h5>
                            <div class="ibox-tools">
                                <a href="/users/add" class="btn btn-primary btn-xs accessModBtn">Шинэ харилцагч нэмэх</a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div class="project-list">

                                <table class="table table-hover">
                                	<thead>
                                	<tr>
                                        <th>Төлөв</th>
                                        <th>Харилцагч</th>
                                        <th>Утас, мэйл</th>
                                        <th>Нэвтрэх нэр</th>
                                        <th>Нэвтрэх үг</th>
                                        <th>Файл</th>
                                        <th> </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
									if(count($usersArr)>0)
                                    foreach($usersArr as $key=>$obj){
									?>
                                    <tr>
                                    	<td class="project-status">
                                        	<?php
                                            if($obj["userActive"]=="a"){
											?>
                                            <span class="label label-success">Зөвшөөрөгдсөн</span>
                                            <?php }
											if($obj["userActive"]=="n"){
											?>
                                            <span class="label label-danger">Хандах эрхгүй</span>
                                            <?php } ?>
                                        </td>
                                        <td class="project-title">
                                            <?php echo $obj["userName"];?> /<?php echo $obj["companyName"];?>/
                                        </td>                                        
                                        <td >
                                            <?php echo $obj["companyPhone"];?>
                                        </td>
                                        <td class="project-completion">
											<small><?php echo $obj["loginMail"];?></small>
                                        </td>
                                        <td class="project-completion">
											<small><?php echo $obj["loginPass"];?></small>
                                        </td>
                                        <td class="project-actions">
                                            <a href="<?php echo "/users/files/".$obj["userID"];?>" class="btn btn-default btn-sm"><i class="fa fa-file-text-o"></i> Ажлын файл </a>
                                        </td>
                                        <td class="project-actions">
                                            <a href="<?php echo "/users/edit/".$obj["userID"];?>" class="btn btn-warning btn-sm accessModBtn"><i class="fa fa-pencil"></i> засах </a>
											<a href="<?php echo "/users/delete/".$obj["userID"];?>" class="btn btn-danger btn-sm accessModBtn"><i class="fa fa-close"></i> Устгах </a>
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
        
<div class="modal inmodal" id="orderModalFrm" tabindex="-1" role="dialog" aria-hidden="true">
    
</div>