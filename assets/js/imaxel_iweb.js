jQuery(document).ready( function() {

   jQuery("a.editor_imaxel_iweb").click( function() {
      var productCode = jQuery(this).attr("data-productCode");
      var productsID = jQuery(this).attr("data-productsID");
      var variation_id = jQuery(this).attr("data-variation_id");
      var nonce = jQuery(this).attr("data-nonce");
	  //alert('clicado iweb');
      jQuery.ajax({
         url : myAjax.ajaxurl,
		 crossDomain: false,
         type : 'POST',
         datatype: 'json',
         data : {
	         action: 'imaxel_editor_iweb', 
	         productCode : productCode,
	         productsID: productsID,
	         variation_id: 
	         variation_id,nonce: nonce
	     },
         success: function(imaxelresponse,myAjax) {
	        //console.log(myAjax);
            if(myAjax == "success") {
				//alert(imaxelresponse);
               //console.log(imaxelresponse);
			   //window.location.replace("http://www.google.com");
               window.location.replace(imaxelresponse);
            }
            else {
               console.log(imaxelresponse);
               window.location.replace(imaxelresponse);
            }
         },
         error: function(imaxelresponse,myAjax) {
	     	console.log(imaxelresponse);
	     	//window.location.replace(imaxelresponse);
	     }
      })   
	  return false;
   })
   
       
	
});