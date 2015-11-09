jQuery(document).ready(function($){
 
    $('#gitcommit').click(function(e){
        // On désactive le comportement par défaut du navigateur
        // (qui consiste à appeler la page action du formulaire)
        e.preventDefault();
         $('#retour').html('<img src="http://www.rts.ch/img/rsr/ajax-bar.gif">');
        // On envoi la requête AJAX
		$.ajax({
			url: '/admincp/gitcommit/',
            type: 'GET',
            data: {checkUrl: $(this).attr('href')},
            cache: false,
            dataType: 'json',
            success: function(data, textStatus, jqXHR)
            {
            	if(typeof data.error === 'undefined')
            	{
            		// Success so call function to process the form
            		console.log('SUCCESS: ' + data.success);
		            $('#gitList').hide();                
 
		            $('#nextForm').show("slow");
					$("#nextForm").html(data);
					$('#retour').hide(); 
 						$.get( "/admincp/gitcommit/",{ gitUrl: data.gitUrl }, function( data ) {
							// alert( "Data Loaded: " + data );
							$("#nextForm").html(data);
						});
            	}
            	else
            	{
            		// Handle errors here
            		console.log('ERRORS: ' + data.error);
            	}
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
            	// Handle errors here
            	console.log('ERRORS: ' + textStatus);
            },
            complete: function()
            {
            	// STOP LOADING SPINNER
            }
		});
    });

    $('#closeGit').click(function(e){
        // On désactive le comportement par défaut du navigateur
        // (qui consiste à appeler la page action du formulaire)
        e.preventDefault();
		    $('#gitList').show("slow");
			$('#nextForm').hide(); 
    });
});
