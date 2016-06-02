{include:thead}
<div class="container">
	<div class="row">
	
	{include:trackprofile-menu}

	<div class="col-xs-12 col-md-9">
			<h2>Track Details</h2>
			{if message}
			<div class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				{message}
			</div>
			{/if}
			{if errors}
			<div class="alert alert-danger">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				{errors}
			</div>
			{/if}

			<form enctype="multipart/form-data" method='post' >

			<div class='row'>
			{if profile}
			<img class='center-block img-thumbnail' src='/static/uploads/{profile}' style='max-height: 250px;max-width:350px;'/>
			{else}
			<img class='center-block img-thumbnail' src='holder.js/400x300/#eee/text:No Image' style='max-height: 250px;max-width:350px;'/>
			{/if}
			<br/>
			<label>Profile Image</label>
			{image}
			</div>
			<br/>
			<label>Track Name</label>
			{trackname}
			<br/>
			<label>Address</label>
			{address}
			<br/>
			<div class='row'>
				<div class='col-xs-12 col-sm-6'>
				<label>City</label>
				{city}
				</div>
				<div class='col-xs-12 col-sm-6'>
					<div class='row'>
					<div class='col-xs-12 col-sm-6'>
						<label>State</label>
						{state}
					</div>
					<div class='col-xs-12 col-sm-6'>
						<label>Country</label>
						{country}
					</div>

					</div>
				</div>
			</div>
			<br/>
				<label>Phone</label>
				{phone}
			<br/>
			<div class='row'>
			<div class='col-xs-12 col-sm-6'>
				<label>Email</label>
				{email}
			</div>
			<div class='col-xs-12 col-sm-6'>
				<label>Website</label>
				{website}
				</div>
			</div>
			<br/>
			<div class='row'>
			<div class='col-xs-12 col-sm-6'>
				<label>Facebook</label>
				{facebook}
				<span class="help-block">Enter a valid Facebook Page link</span>
			</div>
			<div class='col-xs-12 col-sm-6'>
				<div class='row'>
				<div class='col-xs-12 col-sm-6'>
					<label>Twitter</label>
					<div class="input-group">
						  <span class="input-group-addon">@</span>
						{twitter}
					</div>
					<span class="help-block">Enter a valid Twitter Username</span>
				</div>
				<div class='col-xs-12 col-sm-6'>
					<label>Data Widget ID</label>
					{twitter_widgetID}
					<span class="help-block">You can get your Data Widget ID by clicking <a target='_blank' href='https://twitter.com/settings/widgets'>HERE.</a></span>
				</div>
				</div>
			</div>
			</div>
			<br/>
			<div class='row'>
			<div class='col-xs-12 col-sm-6'>
				<label>Instagram</label>
				{instagram}
				<span class="help-block">Enter a valid Instagram Page link</span>
				</div>
			<div class='col-xs-12 col-sm-6'>
				<label>Youtube</label>
				{youtube}
				<span class="help-block">Enter a valid Youtube Channel link</span>
			</div>
			</div>
			<br/>



			<button type='submit' class='btn btn-success'>Save Changes</button>
			</form>


<br /><br /><br /><br />


		</div>
	</div>
</div>


{include:new-footer}