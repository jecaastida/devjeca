{include:header}
<!--<link rel="stylesheet" href="../recurly/checkout/examples.css" type="text/css" /> -->
<link rel="stylesheet" href="../recurly/themes/default/recurly.css" type="text/css" /> 
<!--<script src="../recurly/lib/jquery-1.7.1.js"></script> -->
<script src="../recurly/build/recurly.js"></script> 

<script>
  
  
    
  Recurly.config({
    subdomain: '{sDomain}',
    currency: 'USD',
    country: 'US'
  });
  

  
  Recurly.buildTransactionForm({
    target: '#recurly-transaction',
    // Signature must be generated server-side with a utility method provided
    // in client libraries.
    signature: '{signature}',
    successURL: '{payment:shop_return}',
    account: {
      firstName: '{payment:shop_firstName}',
      lastName: '{payment:shop_lastName}',
      email: '{payment:shop_email}',
      phone: '{payment:shop_phone}',
      companyName: ''
    },
    billingInfo: {
      firstName: '{payment:shop_firstName}',
      lastName: '{payment:shop_lastName}',
      address1: '{payment:shop_address1}',
      address2: '',
      city: '{payment:shop_city}',
      zip: '{payment:shop_zip}',
      state: '{payment:shop_state}',
      country: '{payment:shop_country}',
      cardNumber: '',
      CVV: ''
    }
  });
</script>


             <div id="single-column-wrapper">

		<h1>Payment Page</h1>
                
		<h3>Total Amount: ${payment:shop_amount}</h3>
                <div id="recurly-transaction">
                </div>
             </div>

{include:footer}