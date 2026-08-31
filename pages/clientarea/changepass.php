<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Нууц үг солих</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/clientarea">Эхлэл</a>
            </li>
            <li>
                <a href="/clientarea/changepass">Тохиргоо</a>
            </li>
            <li class="active">
                <strong>Нууц үг солих</strong>
            </li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content">
	<div class="middle-box text-center loginscreen   animated fadeInDown">
        <div>
            <h3>Нууц үг солих</h3>
            <?php
            if($_REQUEST["err"]){
                switch($_REQUEST["err"]){
                    case "2":
            ?>
            <div class="alert alert-danger">Шинэ нууц үгийн баталгаажуулалт таарахгүй байна</div>
            <?php break; 
            case "1":
            ?>
            <div class="alert alert-danger">Одоогийн нууц үг таарахгүй байна</div>
            <?php break;
            case "done":
            ?>
            <div class="alert alert-success">Нууц үг амжилттай солигдлоо</div>
            <?php break;} }?>
			<form action="/modu/pagePost" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="password" name="oldPass" class="form-control" placeholder="Одоогийн нууц үг" required="">
                </div>
                <div class="form-group">
                    <input type="password" name="newPass" class="form-control" placeholder="Шинэ нууц үг" required="">
                </div>
                <div class="form-group">
                    <input type="password" name="newPass2" class="form-control" placeholder="Шинэ нууц үгийг баталгаажуул" required="">
                </div>
                <input type="hidden" name="selPage" value="clientarea">
                <input type="hidden" name="frmPost" value="changepass">
                <button type="submit" class="btn btn-primary block full-width m-b">Хадгалах</button>
            </form>
        </div>
    </div>
</div> 