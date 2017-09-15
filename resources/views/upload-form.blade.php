@extends('welcome')

@section('upload-formx')
    <div class="row" id="main-content">
        <div class="col-md-12" id="well-container">
            <div class="well" id="well-form">
                <h2 id="well-title">Upload Your File</h2>
                <div class="preview-area"></div>
                <form action="process/" method="POST" id="upload-form" enctype="multipart/form-data">
                    
                        <label for="file-upload" class="custom-file-upload">
                            Choose File or Drag Here
                        </label>
                        <input id="file-upload" type="file" name="file-upload" multiple/>
                        {{-- <input class="form-control" id="file-message" type="text-area" name="file-message" placeholder="Type your message here"/> --}}
                        <textarea class="form-control" rows="5" id="file-message" name="file-message" placeholder="Type your message here" ></textarea>
                        <div id="file-cnf-btn">File Configuration</div>
                        <div class="file-cnf">
                            <div class="row" id="file-cnf-exp">
                                Expired After
                            </div>
                            <div class="row" id="file-cnf-pw">
                                Password
                            </div>
                        </div>
                    <button id= "upload-button" type="submit" class="btn btn-primary btn-large btn-block" disabled >Upload</button>
                </form>
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
                    