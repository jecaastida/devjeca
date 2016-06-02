{include:header}
<div id="single-column-wrapper">

		{if errors}
			<div class="error">
				{errors}
			</div>
		{/if}

		<form action="{shop:gateway}" method="post" class="default">

			{shop:checkout}

			<input type="submit" value="Continue to Payment Page &gt;" style="font-weight: bold;" class="button" />
			<br class="clear" />

		</form>
</div>
{include:footer}