$(function()
{
	// Variable to store your files
	var files;

	// Add events
	$('input[type=file]').on('change', prepareUpload);
	$('#uploadTorrent').on('submit', uploadFiles);

	// Grab the files and set them to our variable
	function prepareUpload(event)
	{
		files = event.target.files;
	}

	// Catch the form submit and upload the files
	function uploadFiles(event)
	{
		event.stopPropagation(); // Stop stuff happening
        	event.preventDefault(); // Totally stop stuff happening

        // START A LOADING SPINNER HERE

        // Create a formdata object and add the files
		var data = new FormData();
		$.each(files, function(key, value)
		{
			data.append(key, value);
		});
        
        $.ajax({
            url: 'upload?files',
            type: 'POST',
            data: data,
            cache: false,
            dataType: 'json',
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success: function(data, textStatus, jqXHR)
            {
            	if(typeof data.error === 'undefined')
            	{
            		// Success so call function to process the form
            		submitForm(event, data);
            	}
            	else
            	{
            		// Handle errors here
            		console.log('ERRORS: ' + data.error);
		            $('#retourForm').hide();                
		            $('#retourForm').html('')
		                .append('<br /><div class="alert alert-danger" role="alert"><strong>Error:</strong> '+data.error+'</div>')
		            $('#retourForm').fadeIn();     
            	}
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
            	// Handle errors here
            	console.log('ERRORS: ' + textStatus);
		            $('#retourForm').hide();                
		            $('#retourForm').html('')
		                .append('<br /><div class="alert alert-danger" role="alert"><strong>Error:</strong>: '+textStatus+'</div>')
		            $('#retourForm').fadeIn();    
            	// STOP LOADING SPINNER
            }
        });
    }

    function submitForm(event, data)
	{
		// Create a jQuery object from the form
		$form = $(event.target);
		
		// Serialize the form data
		var formData = $form.serialize();
		
		// You should sterilise the file names
		$.each(data.files, function(key, value)
		{
			formData = formData + '&filenames[]=' + value;
		});

		$.ajax({
			url: 'upload?form',
            type: 'POST',
            data: {formData: formData, info_hash: data.info_hash},
            cache: false,
            dataType: 'json',
            success: function(data, textStatus, jqXHR)
            {
            	if(typeof data.error === 'undefined')
            	{
            		// Success so call function to process the form
            		console.log('SUCCESS: ' + data.success);
		            $('#retourForm').hide();                
		            $('#retourForm').html('')
		                .append('<div class="alert alert-success">'+data.success+'</div>')
		            $('#retourForm').fadeIn(); 
		            $('#nextForm').show("slow");
		            $('#defaultForm').hide(1000);
						$.get( "upload-form",{ info_hash: data.formData.info_hash }, function( data ) {
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
	}
});
