<script>
$(document).ready(function() {
	var controller = new ScrollMagic.Controller({
		  globalSceneOptions: {
			duration: 1000,
			triggerHook: .025,
			reverse: true
		  }
		});


		var scenes = {
		<?php
		$aCls = "active";
		if(count($allSchArr)>0)
		foreach($allSchArr as $keys=>$objs){
			if($objs["schPic"]!=""){
		?>
		  'wid<?php echo $keys?>': {
			'widhas<?php echo $objs["schID"]?>': 'widid-<?php echo $objs["schID"]?>'
		  },
		  <?php }} ?>
		}

		for(var key in scenes) {
		  // skip loop if the property is from prototype
		  if (!scenes.hasOwnProperty(key)) continue;

		  var obj = scenes[key];

		  for (var prop in obj) {
			// skip loop if the property is from prototype
			if(!obj.hasOwnProperty(prop)) continue;

			new ScrollMagic.Scene({ triggerElement: '#' + prop })
				.setClassToggle('#' + obj[prop], 'active')
				.addTo(controller);
		  }
		}


		// Change behaviour of controller
		// to animate scroll instead of jump
		controller.scrollTo(function(target) {

		  TweenMax.to(window, 0.5, {
			scrollTo : {
			  x : target,
			  autoKill : true // Allow scroll position to change outside itself
			},
			ease : Cubic.easeInOut
		  });

		});


		//  Bind scroll to anchor links using Vanilla JavaScript
		var anchor_nav = document.querySelector('.aboutsubmenu');

		anchor_nav.addEventListener('click', function(e) {
		  var target = e.target,
			  id     = target.getAttribute('href');

		  if(id !== null && id.length > 0) {
			e.preventDefault();
			controller.scrollTo(id);

			if(window.history && window.history.pushState) {
			  history.pushState("", document.title, id);
			}
		  }
		});

});
</script>