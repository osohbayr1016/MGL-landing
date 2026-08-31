<section id="hero">
    <div class="hero" style="background-image: linear-gradient(rgba(24, 33, 43, 0.3),rgba(24, 33, 43, 0.1)), url(<?php echo newsPicFnc(0,$selSchBody["pic"]);?>);">
        <h1 class="hero-title"><?php echo $selSchBody["title"];?></h1>
        <h2 class="hero-subheading"><?php echo $selSchBody["sub"];?></h2>
        <a class="white-btn" href="#hero<?php echo $key?>"><?php echo $selSchBody["btn"];?></a>
    </div>
</section><!-- #hero -->

<section class="text-section" id="hero<?php echo $key?>">
    <div class="row text">
        <div class="col-100">
        <?php echo $selSchBody["note"];?>
        </div>
    </div>
</section>