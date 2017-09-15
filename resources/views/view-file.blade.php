@extends('welcome')

@section('upload-formx')
    <div class="row" id="main-content">
        <div class="col-md-12" id="well-container">
            <div class="well" id="well-form">
                <h2 id="well-title">{{$file_name}}</h2>
                <div class="preview-area">
                    <div class="preview-list" id="preview-list-{{$link}}">
                        <img class="preview-other-file" src="{{$icon_url}}" />  
                        <a class="file-name">{{$file_name}}</a> 
                        <a class="file-size" id="file-size">{{$file_size}}</a>
                    </div>
                    <button id= "download-button" type="submit" class="btn btn-primary btn-large btn-block">Download</button>
                </div>
            </div>
        </div>
    </div>
    <svg xmlns="http://www.w3.org/2000/svg"  version="1.1">
        <defs>
            <filter id="blur">
                <feGaussianBlur stdDeviation="1"/>
            </filter>
        </defs>
    </svg>
@stop
                    