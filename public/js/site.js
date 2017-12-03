$(document).ready(function(){
	$.ajaxSetup({
	    headers: {
	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    }
	})
	loadBingBackground();
	function loadBingBackground(){
		$.ajax({
            url: "p/process-background",
            type: "POST",
            dataType:"json",
            data: {},
            success: function(data){
            	var image_path = "/storage/background/"+data[0]["image_path"];
            	var image = "url("+image_path+")";
            	var desc = data[0]["desc"];
                $(".main").css("background-image", image);     
                $(".main-2").css("background-color", "rgba(0, 0, 0, 0)");
                $(".background-desc").text(desc);
                console.log(data);                    
            },
            error: function(xhr, ajaxOptions, thrownError){
                console.log(xhr.responseText);
            }
        });
	}

	//Preview File
	var fileCount;
	var input_upload;
	if($("#file-upload").length !=0){
		input_upload = $("#file-upload")[0];
		input_upload.addEventListener("change",previewImages,false); //bind the function to the input
	}
	function previewImages(event){
	    var fileList = this.files;
	    var anyWindow = window.URL || window.webkitURL;
	    fileCount = fileList.length;
	    if (fileCount>0){
	    	$("#upload-button").attr("disabled", false);
	    }
	    console.log(fileList.length);
	    var j=0;
	    var total_preview = $(".preview-list").length;
        for(var i = total_preview; i < total_preview+fileList.length; i++){
          	var objectUrl = anyWindow.createObjectURL(fileList[i-total_preview]);
          	var objectName = event.target.files[i-total_preview].name;
          	var objectMime = objectName.substr((~-objectName.lastIndexOf(".") >>> 0) + 2);
          	if (objectName.length>20){
				//objectName=objectName.substr(0, 19)+"...";
          	}
          	var objectSize = event.target.files[i-total_preview].size; //Math.round(event.target.files[i].size/1048576*100)/100; //MegaByte
          	if (objectSize<1024*1024){
	            objectSize = Math.round(objectSize/1024*100)/100;
	            objectSize+=" KB";
	        } else{
	            objectSize = Math.round(objectSize/(1024*1024)*100)/100;
	            objectSize+=" MB";
	        }
          	
          	console.log(objectMime);
          	if (objectMime.match(/(jpg|jpeg|png|gif)$/)){
          		$('.preview-area').append('<div class="preview-list" id="preview-list-'+i+'"><img class="preview-img" src="' + 
          			objectUrl + '" />  <a class="file-name">' + objectName + '</a> <a class="file-size" id="file-size-'+i+'">' + objectSize + '</a></div>');
          	} else{
          		var iconUrl = "img/icon/"+objectMime+".png";
          		$('.preview-area').append('<div class="preview-list" id="preview-list-'+i+'"><img class="preview-other-file" src="'+iconUrl+'" />  <a class="file-name">' + 
          			objectName + '</a> <a class="file-size" id="file-size-'+i+'">' + objectSize + '</a></div>');
          	}
          	
          	window.URL.revokeObjectURL(fileList[i]);
        }
	}

	$("#upload-button").on("click", function(e){
		e.preventDefault();
		console.log("OK");
		$(".file-size").empty();
		$(".file-size").css("margin-top", "0px");
		$(".file-size").append('<div class="loader"></div>');
		var user_type = "free_user";
		var fileList = $("#file-upload").get(0).files;
		console.log(fileList[0]);
		
		//Get Access Token
		for(var x = 0; x < fileList.length; x++){
			if ($("#preview-list-"+x).css("display")!="none"){
				uploadFile(x);
			}
			
		}
		function uploadFile(x){
			var formData = new FormData();
			var loader = "#file-size-"+x+" > .loader";
			$(loader).css("border-top","5px solid #3498db");
		    console.log(loader);
			formData.append('file_upload', fileList[x], fileList[x].name);
			formData.append('user_type', user_type);
			$.ajax({
		        url: 'p/upload-file',
		        type: 'POST',
		        dataType: 'json',
		        data: formData,
		        cache: false,
		        contentType: false,
		        processData: false,

		        /*xhr: function() {
		            var myXhr = $.ajaxSettings.xhr();
		            
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
		        },*/

		        success: function(data){
		        	var link = data["link"];
		        	console.log(link);
	            	$("#file-size-"+x).empty();
	            	var show_link = '<div class="file-link"  style="display:none;">\
	            						<div class="input-group">\
										    <input type="text" id="copy-link-'+x+'" value="upfla.me/'+link+'" class="form-control"/>\
										        <span class="input-group-btn" >\
										            <button class="btn btn-default copy-btn" id="copy-btn-'+x+'" type="button"><i class="glyphicon glyphicon-duplicate"></i></button>\
										        </span>\
										</div>\
									</div>';
					$("#file-size-"+x).append('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>');
					//$(show_link).appendTo(".preview-list").fadeIn("slow");
					var previewList = "#preview-list-"+x;
					//console.log(previewList);
					$(previewList).append(show_link);
					$(previewList+" > .file-link").slideDown();
	                //console.log(data);                    
	            },
	            error: function(xhr, ajaxOptions, thrownError){
	                console.log(xhr.responseText);
	            }
		    });
		}
	});

	$("#well-form").on("click", ".copy-btn", function(){
		console.log("Copy Link");
		var id = $(this).attr("id");
		id = id.substr(id.indexOf("n-")+2);
		//console.log(id);
		$("#copy-link-"+id).select();
		try {
	    	succeed = document.execCommand("copy");
	    } catch(e) {
	        succeed = false;
	    }
		
	});

	$("#download-button").on("click", function(){
		console.log("Download");
		var id = $(".preview-list").attr("id");
		var link = id.substr(id.indexOf("preview-list-")+13);
		//console.log(link);
		$.ajax({
            url: "p/download-file",
            type: "POST",
            dataType:"json",
            data: {
            	"link": link
            },
            success: function(data){
            	var one_time_link = data["otl"];
            	var url = "http://localhost/upflame/public/download/"+one_time_link+"/"+link;
            	window.location = url;
            	console.log(url);          
                console.log(data);                    
            },
            error: function(xhr, ajaxOptions, thrownError){
                console.log(xhr.responseText);
            }
        });
	})

	$("#well-form").on({
		mouseenter: function () {
			id_preview_list = $(this).attr("id");
			file_size_text = $("#"+id_preview_list+" > .file-size").text();
			//console.log(id_preview_list);
			if($("#"+id_preview_list+" > .file-size > .glyphicon-ok").length == 0){
				$("#"+id_preview_list+" > .file-size").empty();
				$("#"+id_preview_list+" > .file-size").append('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>');
				$("#"+id_preview_list+" > .file-size").css('cursor', 'pointer');
			}
		    //console.log("Mouse Over");
	    },
	    mouseleave: function () {
	    	if($("#"+id_preview_list+" > .file-size > .glyphicon-ok").length == 0){
		        $("#"+id_preview_list+" > .file-size").empty();
				$("#"+id_preview_list+" > .file-size").append(file_size_text);
				$("#"+id_preview_list+" > .file-size").css('cursor', 'auto');
		    	//console.log("Mouse Over");
		    }
	    }
		
	}, ".preview-list");
	
	$("#well-form").on("click", ".preview-list > .file-size > .glyphicon-remove", function(){
		var id = $(this).parents().parents().attr("id");

		$("#"+id).css("display","block");
		$("#"+id).slideUp();
		check_display();
		console.log(id);
	    
	})

	function check_display(){
		var file_display_none = $(".preview-list").filter(function() {
		    return $(this).css('display') !== 'none';
		}).length;
		if((file_display_none-1)==0){
			$("#upload-button").attr("disabled", true);
		}
		console.log(file_display_none);
	}

	$("#file-cnf-btn").on("click", function(){
		$(".file-cnf").slideToggle();
	})
})
