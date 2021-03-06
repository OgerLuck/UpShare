<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User as Users;
use Session;
use Illuminate\Support\Facades\Storage;
use App\UploadedFile;
use App\UploadedFileLink;

class MainProcessForJS extends Controller{
    
    public function processBackground(){
        $date = date("Y-m-d");
        $exists = Storage::disk('background')->exists($date.'.jpg');
        if(!$exists){
            $url = "http://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1&mkt=en-US";
            $json = json_decode(file_get_contents($url), TRUE);
            $image_url = $json["images"][0]["url"];
            $image_link = "www.bing.com".$image_url;
            $split_image = pathinfo($image_link);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL , $image_link);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            $response= curl_exec ($ch);
            curl_close($ch);
            
            $file_name = $date.".".$split_image['extension'];
            Storage::disk('background')->put($file_name, $response);
            $previous_date = date('Y-m-d', strtotime('-1 day', strtotime($date)));
            Storage::disk('background')->delete($previous_date.'.jpg');
        } else{
            $file_name = $date.".jpg";
        }
                
        $result = array();
        array_push($result, array("image_path"=>$file_name));
        echo json_encode($result);
    }

    public function uploadFile(Request $request){
        //$file = $request->file('file_upload');
        $file_name =  $request->file_name;
        $mime = $request->mime;
        $size = $request->size;
        $file = array(
                    "file_name"=> $file_name,
                    "mime"=> $mime,
                    "size"=> $size 
                );

        //echo $file_name." ".$tmp_name." ".$mime." ".$ext." ".$size;
        //$split_image = fileinfo($image_link);
        $access_token = $this->get_access_token_using_saved_refresh_token();
        $result = $this->uploadProcess($access_token, $file);
        //Storage::disk('background')->put("xxx", $file);

        echo json_encode($result);
    }

    public function get_access_token_using_saved_refresh_token() {
        // from the oauth playground
        $refresh_token = "1/ZKs_drV6JpE31zZ4LywbJzPa_zdM3b84YvtKN8gKV1K6leCgmaMVs9hSwhDAfrcu";
        // from the API console
        $client_id = "461697840948-ad8q710s83ru837pqk89lfrrc2g44kop.apps.googleusercontent.com";
        // from the API console
        $client_secret = "zVwa9WezFycec-o-33ZTyq9g";
        // from https://developers.google.com/identity/protocols/OAuth2WebServer#offline
        $refresh_url = "https://www.googleapis.com/oauth2/v4/token";

        $post_body = array(
            "grant_type"=>"refresh_token",
            "client_id"=> $client_id,
            "client_secret"=> $client_secret,
            "refresh_token"=> $refresh_token
          );
        //CEK AKSES TOKEN DI DATABASE, KALO KURANG DARI SEJAM PAKE, KALO NGK REQUEST LAGI.
        //echo http_build_query($post_body)."<br>";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $refresh_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_body));  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $headers = array("Content-Type: application/x-www-form-urlencoded");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //Kalo ngk pake ini, harus install certificate di xampp C;/xampp/cacert.pem
        $server_output = curl_exec ($ch);
        //echo curl_getinfo($ch, CURLINFO_HTTP_CODE)." ".curl_errno($ch) ;
        curl_close ($ch);
        $json = json_decode($server_output, TRUE);
        //echo $server_output."<br>";
        $access_token = $json["access_token"];
        //SIMPEN AKSES TOKEN DI DATABASE
        $result = array('access_token'=>$access_token);
        //echo json_encode($result);
        return $access_token;
        //filesList($access_token);
    }

    private function uploadProcess ($access_token, $file) {
        //INITIAL REQUEST
        $result = array();
        $drive_init_upload_url = "https://www.googleapis.com/upload/drive/v3/files?uploadType=resumable";
        $file_metadata = array(
                "name"=> $file["file_name"],
                "mimeType"=> $file["mime"]
            );
        $file_metadata = json_encode($file_metadata);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $drive_init_upload_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $file_metadata);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $headers = array("Authorization: Bearer ".$access_token,
                         "X-Upload-Content-Type: ".$file["mime"],
                         "X-Upload-Content-Length: ".$file["size"],
                         "Content-Type: application/json; charset=UTF-8",
                         "Content-Length: ".strlen($file_metadata));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $server_output = curl_exec ($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //echo $http_code."<br>".strlen($file_metadata)."<br>";
        $curl_info = curl_getinfo($ch);
        curl_close ($ch);
        if ($http_code == "200"){
            preg_match("!\r\n(?:Location): *(.*?) *\r\n!", $server_output, $matches);
            $location_uri = $matches[1];
            $result = array("Location"=>$location_uri, "http_code"=>$http_code);
            /*$data_sent   = file_get_contents($file["tmp_name"]);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $location_uri);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $headers = array("Content-Length: ".strlen($data_sent));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_sent);
            
            $server_output = curl_exec ($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_info = curl_getinfo($ch);
            //echo $server_output."<br>";
            //var_dump($curl_info);
            echo $http_code;
            curl_close ($ch);
            //fclose($fp);
            $json = json_decode($server_output, TRUE);
            //var_dump($json);
            $drive_file_id = $json["id"];
            echo date("Y-m-d H:i:s");
            $expired_date = date("Y-m-d H:i:s", strtotime("0 day 1 hour 0 minute 0 second"));
            $uploadedFile = new UploadedFile;
            $uploadedFile->user_ID = 1; //1 Untuk free user, sianya sesuaiin.
            $uploadedFile->file_name = $file["file_name"];
            $uploadedFile->mime = $file["mime"];
            $uploadedFile->size = $file["size"];
            $uploadedFile->drive_file_id = $drive_file_id ;
            $uploadedFile->expired_date = $expired_date;
            $uploadedFile->save();
            $this->generate_link_for_file($uploadedFile->ID);
*/
            //echo $drive_file_id;
        } else{
            $result = array("Location"=>"NULL", "http_code"=>$http_code);
        }
        //echo $server_output;
        return $result;
    }

    public function generate_link_for_file($file_id){
        $link_stat = false;
        while(!$link_stat){
            $rand_link = $this->random_str(8);
            $is_link_exist = UploadedFileLink::where('link', $rand_link)->get()->count();
             if($is_link_exist>0){
                $link_stat = false;
            } else{
                $link_stat = true;
            }
        }
       
        $uploadedFileLink = new UploadedFileLink;
        $uploadedFileLink->file_ID = $file_id;
        $uploadedFileLink->link = $rand_link;
        $uploadedFileLink->save();
    }

    public function viewFile($link){
        $access_token = $this->get_access_token_using_saved_refresh_token();
        $file_link = UploadedFileLink::where('link', $link)->first();
        $file_id = $file_link->file_ID;
        $file = UploadedFile::where('ID', $file_id)->first();
        $file_name = $file->file_name;
        $file_content_type = $file->mime;
        $drive_file_id = UploadedFile::where('ID', $file_id)->first()->drive_file_id;
        $drive_download_url = "https://www.googleapis.com/drive/v3/files/".$drive_file_id."?alt=media";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $drive_download_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $headersx = array("Authorization: Bearer ".$access_token);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headersx);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $content_disposition = "form-data; file='".$file_name."'; filename='".$file_name."'";
        header("Content-type: ".$file_content_type);
        header("Content-Disposition: ".$content_disposition);
        echo $server_output;
    }

    private function curlWriteFile($cp, $data) {
        global $GlobalFileHandle;
        $len = fwrite($GlobalFileHandle, $data);
        return $len;
    }

    private function filesList ($access_token) {
        $drive_url = "https://www.googleapis.com/drive/v3/files";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $drive_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $headers = array("Authorization: Bearer ".$access_token);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $server_output = curl_exec ($ch);
        echo curl_getinfo($ch, CURLINFO_HTTP_CODE)." ".curl_errno($ch) ;
        curl_close ($ch);
        echo $server_output;
    }

    private function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'){
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }

    public function signin(Request $request){
    	$user = Users::where('username', $request->email)
    				->where('password', $request->password)->first();
    	if (!empty($user)){
    		$userID = $user->ID;
    		if ($userID==1){
    			Session::put('USER_ID', $userID);
    			return redirect()->route('ViewAdmin');
    		} else{
    			//User lain.
    		}
    	} else{
    		return redirect()->route('ViewSignin');
    		//return view('signin');
    	} 
    }

    public function signout(){
    	Session::forget('USER_ID');
    	return redirect()->route('ViewSignin');
    }
}