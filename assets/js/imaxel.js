jQuery(document).ready( function() {

   jQuery("a.editor_imaxel").click( function() {
      var productCode = jQuery(this).attr("data-productCode");
      var productsID = jQuery(this).attr("data-productsID");
      var variation_id = jQuery(this).attr("data-variation_id");
      var nonce = jQuery(this).attr("data-nonce");
	  //alert('clicado html5');
      jQuery.ajax({
         url : myAjax.ajaxurl,
         type : 'POST',
         datatype: 'json',
         data : {
	         action: 'imaxel_editor', 
	         productCode : productCode,
	         productsID: productsID,
	         variation_id: 
	         variation_id,nonce: nonce
	     },
         success: function(imaxelresponse,myAjax) {
	        //console.log(myAjax);
            if(myAjax == "success") {
               console.log(imaxelresponse);
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