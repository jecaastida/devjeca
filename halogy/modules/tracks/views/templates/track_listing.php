{include:thead}
<script>
//$(function() {
    // Stick the #nav to the top of the window
   //var item = $('.float-advert');
    //var HomeY = item.offset().top;
    //var isFixed = false;
    //var $w = $(window);
    //$w.scroll(function() {
    //    var scrollTop = $w.scrollTop();
    //    var shouldBeFixed = scrollTop > HomeY;
    //    if (shouldBeFixed && !isFixed) {
    //        item.css({
    //            position: 'fixed',
    //            top: 0,
    //            left: item.offset().left,
    //            width: item.width()
    //        });
    //        isFixed = true;
    //    }
    //    else if (!shouldBeFixed && isFixed)
    //    {
    //        item.css({
    //            position: 'static'
    //        });
    //        isFixed = false;
    //    }
    //});
//});



</script>

<style>
.panel {
    margin-bottom: -22px;
}
.hide{display:none;}
.track-list li img{margin-right:6px !important;}
.track-list li {font-weight:bold;}
.track-list li a{color:#9bbf07; font-weight:bold;}
@media  screen  and (max-width:767px){

	.track-advert img{
	
		width:100% !important;
	}
} 
@media  screen  and (max-width:479px){
.float-advert img{height:300px !important;}
}
</style>

<div class='row' style='min-height:800px;'>
	<div class='col-xs-12 col-md-12'>
		<!--img class='center-block img-thumbnail' data-src='holder.js/100%x100/text:Track Owners Get Listed btn' /-->
		<div class='float-advert desktop col-sm-3' style="margin-top: 30px;">
			{advert:side}
		</div>
	

		<div class='col-sm-9' style="margin-top:5px;" id="list">
			<h2 class='pull-left'>Tracks</h2>
				<!--{pagination}-->
			<!--track listing-->
			<div class="clearfix"></div>

			
				{tracks}


					{data}
							<br /><br />
						 <div class='track-advert' style='margin-bottom:15px;'>
						 
						 
						 
							{ads_track} 
						 
						 </div>


				<!--{pagination}-->
				{/tracks}
				
				
				
			{if track_not_found}
					<p style="color:red">{track_not_found}</p>

			{else}

			<div class='col-xs-12 col-md-9' id='load-more'>
					<button class="btn btn-success load" > Load More Tracks </button>
					<div class='loading hidden'><i class='fa fa-cog fa-spinner'></i> Loading ...</div>
			</div>
		{/if}

			
		</div>
	</div>		
 <br /><br /><br /><br /><br /><br /><br />
<script>

var offset = 0;
$(function(){
	
	
		
	if(offset == 0){
		$('#load-more').addClass('hide');
	}else{
	$('#load-more').on('click',function(){
		$('.loading').removeClass('.hidden');
	//get via ajax
		offset = offset + 20;  //20 is the pagination set in controller 
		
		$.ajax({
			//url: window.location.href+"&offset="+offset,
			url: window.location.href,
			method: 'GET',
			data: {'offset':offset},
			beforeSend: function() {
				$('.loading').removeClass('.hidden');
			}
		})
		

				
		.done(function( data ) {
			if(data.length == 0){
				$('#load-more').addClass('hide');
				//$('#load-more').before(data);
				//alert('You\'ve reached the end ');
			}else{
				$('.loading').addClass('.hidden');
				$('#load-more').before(data);
			}
		 });
	
	});
	
	}
	
});

</script>           
</div>

<div class='container' style="padding:0px !important">
{include:new-footer}
</div>