<article class="pdetail">
	<section class="pdetail-hero">
		<img src="<?php echo $pdetailHero;?>" alt="<?php echo htmlspecialchars($productObj["ceoName"], ENT_QUOTES, "UTF-8");?>" fetchpriority="high">
	</section>

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

		<?php if(count($pdetailGallery)>0){ ?>
		<section class="pdetail-photos pdetail-wrap" aria-label="Project photos">
			<div class="pdetail-photos__grid">
				<?php foreach($pdetailGallery as $imgurl){ ?>
				<div class="pdetail-photos__item">
					<img src="<?php echo picUrl("ceo",$imgurl);?>" alt="" loading="lazy" decoding="async">
				</div>
				<?php } ?>
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
