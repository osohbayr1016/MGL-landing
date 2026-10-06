<article class="pdetail">
	<?php $pdetailAlt = htmlspecialchars($productObj["ceoName"], ENT_QUOTES, "UTF-8"); ?>
	<?php if(count($pdetailSlides)>1){ ?>
	<section class="pdetail-hero pdetail-hero--slider" aria-label="Project photos">
		<div class="pdetail-hero__track owl-carousel">
			<?php foreach($pdetailSlides as $i=>$imgurl){ ?>
			<div class="pdetail-hero__slide">
				<img src="<?php echo $imgurl;?>" alt="<?php echo $pdetailAlt;?>"<?php echo $i==0 ? ' fetchpriority="high"' : ' decoding="async"';?>>
			</div>
			<?php } ?>
		</div>
	</section>
	<?php }else{ ?>
	<section class="pdetail-hero">
		<img src="<?php echo $pdetailHero;?>" alt="<?php echo $pdetailAlt;?>" fetchpriority="high">
	</section>
	<?php } ?>

	<div class="pdetail-main">
		<header class="pdetail-head pdetail-wrap">
			<p class="pdetail-eyebrow"><a href="/projects">Projects</a></p>
			<h1 class="pdetail-title"><?php echo $productObj["ceoName"];?></h1>
			<?php if(trim($productObj["typeName"])!=""){ ?>
			<p class="pdetail-location"><?php echo $productObj["typeName"];?></p>
			<?php } ?>
		</header>

		<?php if(count($pdetailFacts)>0){ ?>
		<section class="pdetail-facts" aria-label="Project details">
			<div class="pdetail-wrap">
				<dl class="pdetail-facts__grid">
					<?php foreach($pdetailFacts as $fact){ ?>
					<div class="pdetail-fact">
						<dt class="pdetail-fact__label"><?php echo $fact["label"];?></dt>
						<dd class="pdetail-fact__value"><?php echo $fact["value"];?></dd>
					</div>
					<?php } ?>
				</dl>
			</div>
		</section>
		<?php } ?>

		<?php if(trim(strip_tags($productObj["ceoBody"]))!=""){ ?>
		<section class="pdetail-content pdetail-wrap">
			<div class="pdetail-prose">
				<?php echo $productObj["ceoBody"];?>
			</div>
		</section>
		<?php } ?>

		<?php if($pdetailPrev || $pdetailNext){ ?>
		<nav class="pdetail-nav pdetail-wrap" aria-label="Project navigation">
			<?php if($pdetailPrev){ ?>
			<a class="pdetail-nav__link pdetail-nav__link--prev" href="/project/<?php echo $pdetailPrev["ceoID"];?>">
				<span class="pdetail-nav__hint">Previous project</span>
				<span class="pdetail-nav__name">&larr; <?php echo $pdetailPrev["ceoName"];?></span>
			</a>
			<?php }else{ ?>
			<span></span>
			<?php } ?>
			<?php if($pdetailNext){ ?>
			<a class="pdetail-nav__link pdetail-nav__link--next" href="/project/<?php echo $pdetailNext["ceoID"];?>">
				<span class="pdetail-nav__hint">Next project</span>
				<span class="pdetail-nav__name"><?php echo $pdetailNext["ceoName"];?> &rarr;</span>
			</a>
			<?php } ?>
		</nav>
		<?php } ?>
	</div>
</article>
