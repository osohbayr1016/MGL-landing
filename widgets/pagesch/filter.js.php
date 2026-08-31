<script>
$(document).ready(function () { 
	
	
	$(".filter-title").click(function() {
		$(this).parent().toggleClass("filter-close");	
		$(this).parent().toggleClass("filter-open");
		
	});
	
	$( ".filterinp" ).change(function() {
		var sectorFilter = false;
		$('.sectorinp').each(function () {
           if (this.checked) 
			   sectorFilter = true;
		});
		
		if(sectorFilter){
			$('.project-sector').attr("data-show","false");
			$('.sectorinp').each(function () {
			   if (this.checked) {
				   
				   $('div[data-'+$(this).attr("data-type")+'="'+$(this).val()+'"]').each(function(){
					   
					   $(this).attr("data-show","true");
					
					});
			   }
			});
		}
		else
			$('.project-sector').attr("data-show","true");
			
		sectorFilter = false;
		$('.typeinp').each(function () {
           if (this.checked) 
			   sectorFilter = true;
		});
		
		if(sectorFilter){
			
			$('.typeinp').each(function () {
			   if (this.checked) {
				   
				   $('div[data-'+$(this).attr("data-type")+'="'+$(this).val()+'"]').each(function(){
					   
					   if ($(this).attr("data-show")=="true")
					   		$(this).attr("data-show","true");
					   else
					   		$(this).attr("data-show","false");
					
					});
			   }
			    else
					$('div[data-'+$(this).attr("data-type")+'="'+$(this).val()+'"]').each(function(){
					   
					   if ($(this).attr("data-show")=="true")
					   		$(this).attr("data-show","false");
					
					});
			   
			});
			
			
		}
		
		sectorFilter = false;
		$('.locinp').each(function () {
           if (this.checked) 
			   sectorFilter = true;
		});
		
		if(sectorFilter){
			
			$('.locinp').each(function () {
			   if (this.checked) {
				   
				   $('div[data-'+$(this).attr("data-type")+'="'+$(this).val()+'"]').each(function(){
					   
					   if ($(this).attr("data-show")=="true")
					   		$(this).attr("data-show","true");
					   else
					   		$(this).attr("data-show","false");
					
					});
			   }
			    else
					$('div[data-'+$(this).attr("data-type")+'="'+$(this).val()+'"]').each(function(){
					   
					   if ($(this).attr("data-show")=="true")
					   		$(this).attr("data-show","false");
					
					});
			   
			});
			
			
		}
		
		sectorFilter = false;
		$('.statusinp').each(function () {
           if (this.checked) 
			   sectorFilter = true;
		});
		
		if(sectorFilter){
			
			$('.statusinp').each(function () {
			   if (this.checked) {
				   
				   $('div[data-'+$(this).attr("data-type")+'="'+$(this).val()+'"]').each(function(){
					   
					   if ($(this).attr("data-show")=="true")
					   		$(this).attr("data-show","true");
					   else
					   		$(this).attr("data-show","false");
					
					});
			   }
			    else
					$('div[data-'+$(this).attr("data-type")+'="'+$(this).val()+'"]').each(function(){
					   
					   if ($(this).attr("data-show")=="true")
					   		$(this).attr("data-show","false");
					
					});
			   
			});
			
			
		}
		
		$('.project-sector').each(function () {
		   if ($(this).attr("data-show")=="true") {
			   $(this).show("slow");
		   }
		   else
				$(this).hide("slow");
		});
			
		
	});
	
	
	
});
</script>