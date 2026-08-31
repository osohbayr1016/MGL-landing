<footer>
		<div class="clearfix mainfooter">
			<div class="followsection">
				<label>Follow us on instagram</label>
				<!-- Social media -->
				<div class="sociallinks">
					<a href="<?php echo $selConfig["socialFB"];?>" target="_blank" title="Find us on Facebook">
						<svg width="30" height="30" aria-hidden="true">
							<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#facebook"></use>
						</svg>
					</a>

					<a href="<?php echo $selConfig["socialIN"];?>" target="_blank"
						title="Find us on Instagram">
						<svg width="30" height="30" aria-hidden="true">
							<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#insta"></use>
						</svg>
					</a>
				</div>
			</div>

			<!-- Menu -->
			<div class="menudataset">
				<?php
				foreach($bottAllMenuArr as $key=>$obj){
				?>
				<!--  -->
				<div class="sinmenuarea">
					<div class="toplabel">
						<span><?php echo $obj["name"]?></span>
						<div class="iconwrap">
							<svg width="30" height="30" aria-hidden="true">
								<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#uparrow"></use>
							</svg>
						</div>
					</div>
					<div class="dropmenu">
						<ul>
						<?php
						foreach($obj["sub"] as $keys=>$objs){
							$menuLink = menuLinkFunc($objs);
						?>
							<li><a title="<?php echo $objs["name"]?>" href="<?php echo $menuLink?>"><?php echo $objs["name"]?></a></li>
						<?php } ?>
						</ul>
					</div>
				</div>
				<?php } ?>
				

				<div class="contantres">
					<div class="sinmenuarea contactsec">
						<div class="toplabel">
							<span>Contact us</span>
						</div>
						<div class="address">
							<address><?php echo $selConfig["socialTL"];?></address>
						</div>
					</div>
					<div class="sinmenuarea contactsec resver">
						<div class="toplabel">
							<span>Reservations</span>
						</div>
						<div class="resdata">
							<a href="tel:<?php echo $selConfig["socialPhone"];?>" class="tel"><?php echo $selConfig["socialPhone"];?></a>
							<a href="mailto:<?php echo $selConfig["socialVB"];?>"
								class="email"><?php echo $selConfig["socialVB"];?></a>
						</div>
					</div>
				</div>
			</div>

			<div class="clearfix"></div>
			<!-- Copyrights -->
			<div class="sinrowcopy">
				<div class="logosection">
					<img src="/assets/images/logo-white.svg?v=1"
						class="img-fluid" alt="<?php echo $siteMainTitle;?>" title="<?php echo $siteMainTitle;?>">
				</div>
				<div class="concept">
					<span>© All Rights Reserved. Nomun Medical Wellness Resort  | Concept & Design by <br></span>
					<a href="https://fb.com/baachka" target="_blank">BaA4ka</a>
				</div>
			</div>
		</div>
	</footer>