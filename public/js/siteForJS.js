$(document).ready(function(){
	$.ajaxSetup({
	    headers: {
	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    }
	})
	loadBingBackground();
	function loadBingBackground(){
		$.ajax({
            url: "process-background",
            type: "POST",
            dataType:"json",
            data: {},
            success: function(data){
            	var image_path = "/upflame/storage/background/"+data[0]["image_path"];
            	var image = "url("+image_path+")";
                $(".main").css("background-image", image);     
                $(".main-2").css("background-color", "rgba(0, 0, 0, 0)");                
                console.log(data);                    
            },
            error: function(xhr, ajaxOptions, thrownError){
                console.log(xhr.responseText);
            }
        });
	}

	//Preview File
	var fileCount;
	var inputLocalFont = document.getElementById("file-upload");
	inputLocalFont.addEventListener("change",previewImages,false); //bind the function to the input
	function previewImages(event){
	    var fileList = this.files;
	    var anyWindow = window.URL || window.webkitURL;
	    fileCount = fileList.length;
	    console.log(fileList.length);
        for(var i = 0; i < fileList.length; i++){
          	var objectUrl = anyWindow.createObjectURL(fileList[i]);
          	var objectName = event.target.files[i].name;
          	var objectSize = Math.round(event.target.files[i].size/1048576*100)/100; //MegaByte
          	var objectMime = objectName.substr((~-objectName.lastIndexOf(".") >>> 0) + 2);
          	console.log(objectMime);
          	if (objectMime.match(/(jpg|jpeg|png|gif)$/)){
          		$('.preview-area').append('<div class="preview-list"><img class="preview-img" src="' + 
          			objectUrl + '" />  <a class="file-name">' + objectName + '</a> <a class="file-size" id="file-size-'+i+'">' + objectSize + ' MB</a></div>');
          	} else{
          		var iconUrl = "img/icon/"+objectMime+".png";
          		$('.preview-area').append('<div class="preview-list"><img class="preview-other-file" src="'+iconUrl+'" />  <a class="file-name">' + 
          			objectName + '</a> <a class="file-size" id="file-size-'+i+'">' + objectSize + ' MB</a></div>');
          	}
          	
          	window.URL.revokeObjectURL(fileList[i]);
        }
	}

	$("#upload-button").on("click", function(e){
		e.preventDefault();
		console.log("OK");
		$(".file-size").empty();
		$(".file-size").append('<div class="loader"></div>');
		var fileList = $("#file-upload").get(0).files;
		console.log(fileList[0]);
		
		//Get Access Token
		/*var file_name = fileList[0].name;
        var mime = fileList[0].type;
        var size = fileList[0].size; 
		$.ajax({
	        url: 'get-upload-url',
	        type: 'POST',
	        dataType:'json',
	        data: {
	        	"file_name": file_name,
	        	"mime": mime,
	        	"size": size
	        },
	    	success: function(data){
                //console.log(data); 
                var location_uri = data["Location"];
                console.log(location_uri);
                var formData = new FormData(fileList[0]);
                //for(var x = 0; x < fileList.length; x++){
                
	                $.ajax({
				        url: location_uri,
				        beforeSend: function(xhr) {
				             xhr.setRequestHeader("X-HTTP-Method-Override", "PUT");
				        },
				        type: 'POST',
				        dataType:'json',
				        crossDomain: true,
				        data: formData,
				        cache: false,
		                contentType: false,
		                processData: false,
						success: function(data){
							var drive_file_id = data["id"];
							console.log(data); 
						},
						complete: function(xhr, textStatus) {
					        console.log(xhr.status);
					        console.log(xhr.responseText);
					    },
			            error: function(xhr, ajaxOptions, thrownError){
			                console.log(xhr.responseText);
			            }
				    });

                //}
            },
            error: function(xhr, ajaxOptions, thrownError){
                console.log(xhr.responseText);
            }
	    });*/


		for(var x = 0; x < fileList.length; x++){
			var formData = new FormData();
			formData.append('file_upload', fileList[x], fileList[x].name);
			$.ajax({
		        url: 'upload-file',
		        type: 'POST',
		        data: formData,
		        cache: false,
		        contentType: false,
		        processData: false,

		        xhr: function() {
		            var myXhr = $.ajaxSettings.xhr();
		            var loader = "#file-size-"+x+" > .loader";
		            if (myXhr.upload) {
		                // For handling the progress of the upload
		                myXhr.upload.addEventListener('progress', function(e) {
		                    if (e.lengthComputable) {
		                    	if (e.loaded/e.total>=0.25){
		                    		$(loader).css("border-top","5px solid #3498db");
		                    	} 
		                    	if(e.loaded/e.total>=0.50){
		                    		$(loader).css("border-right","5px solid #3498db");
		                    	} 
		                    	if(e.loaded/e.total>=0.75){
		                    		$(loader).css("border-bottom","5px solid #3498db");
		                    	} 
		                    	if(e.loaded/e.total==1){
		                    		$(loader).css("border-left","5px solid #3498db");
		                    	}
		                        
		                    }
		                } , false);
		            }
		            return myXhr;
		        },

		        success: function(data){
	            	$(".file-size").empty();
					$(".file-size").append('<div class="loader"></div>');
	                console.log(data);                    
	            },
	            error: function(xhr, ajaxOptions, thrownError){
	                console.log(xhr.responseText);
	            }
		    });
		}
	})
	
})