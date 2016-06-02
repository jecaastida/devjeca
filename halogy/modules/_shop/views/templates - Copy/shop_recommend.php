{include:header}
<div class="container">
    <div class="row">
<h1>Recommend Product</h1>

{if errors}
                
                <div class="alert alert-danger error">
                 <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {errors}
                </div>
            {/if}
        <div class="col-md-6 col-xs-12">
        <form method="post" action="/shop/recommend/{product:id}" class="default">
            <label>Your Name:</label>
            <input type="text" name="fullName" value="{form:name}" class="form-control" />
            <br class="clear" />

            <label>Your Email:</label>
            <input type="text" name="email" value="{form:email}" class="form-control" />
            <br class="clear" />

            <label>Their Name:</label>
            <input type="text" name="toName" value="{form:to-name}" class="form-control" />
            <br class="clear" />

            <label>Their Email:</label>
            <input type="text" name="toEmail" value="{form:to-email}" class="form-control" />
            <br class="clear" />

            <label>Message: <small>(optional)</small></label>
            <textarea name="message" class="form-control small">{form:message}</textarea>
            <br class="clear" /><br />

            <input type="submit" value="Send Message" class="btn btn-primary" />
            <br class="clear" />
                
        </form>

    </div>
  <div>
</div>

{include:footer}