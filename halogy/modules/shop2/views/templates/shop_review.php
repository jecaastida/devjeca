{include:header}
<div class="container">
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <h1>Review Product</h1>

            {if errors}
                        
                        <div class="alert alert-danger error">
                         <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            {errors}
                        </div>
                    {/if}
                        

            <form method="post" action="/shop/review/{product:id}" class="default" id="reviewsform">

                <label for="fullName">Your Name</label>
                <input type="text" name="fullName" value="{form:name}" id="fullName" class="form-control" />
                <br class="clear" />

                <label for="email">Your Email</label>
                <input type="text" name="email" value="{form:email}" id="email" class="form-control" />
                <br class="clear" />

                <label for="rating">Rating</label>
                <select name="rating" class="form-control">
                    <option value="1">1 / 5</option>
                    <option value="2">2 / 5</option>
                    <option value="3">3 / 5</option>
                    <option value="4">4 / 5</option>
                    <option value="5">5 / 5</option>																
                </select>
                <br class="clear" />

                <label for="reviewform">Review</label>
                <textarea name="review" id="reviewform" class="form-control small">{form:review}</textarea>
                <br class="clear" /><br />

                <!--label for="captcha">Enter the word:</label>		
                {captcha}<br class="clear" />
                <input type="text" name="captcha" id="captcha" class="form-control nolabel" />
                <br class="clear" /><br /-->
                
                <div class="g-recaptcha" data-sitekey="6LePsxwTAAAAAN8zy5scpOCj3BLDBGms-i0knbsO"></div>

                <input type="submit" value="Post Review" class="btn btn-primary" />
                <br class="clear" />

            </form>
        </div>
    </div>
</div>
{include:footer}