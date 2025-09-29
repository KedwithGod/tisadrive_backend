<?php

namespace App\Base\Services\Setting;
 
use App\Models\Setting;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Exception;
use Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class UpdateSetting implements UpdateSettingContract {

	 /**
     * The Request object.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

	public function __construct(Request $request)
	{
		$this->request = $request; 
	}
	public function softupdate() 
	{  
			  $api_content = $this->get_api_content();   
			 	
			 
			 $request_data = (object)$this->request->input();


			 if($api_content)
			 {
			 	$request_content = [
			 		"name"=> $request_data->name,
			 		"purchase_code"=> $request_data->purchase_code,
			 		"email"=> $request_data->email,
			 		"user_name"=> $request_data->user_name,
			 		"domain_name"=> $request_data->domain_name,
			 		"host"=>$this->request->getHost()
			 ];

			 $ch = curl_init(); 
			 curl_setopt_array($ch, array(
		        CURLOPT_URL => $api_content,
		        CURLOPT_RETURNTRANSFER => true,
		        CURLOPT_TIMEOUT => 40,
		        CURLOPT_POST => true,               // Set this option to true for a POST request
		        CURLOPT_POSTFIELDS => json_encode($request_content), // Set the POST data
		        CURLOPT_FOLLOWLOCATION => true,
		        CURLOPT_HTTPHEADER => array(
		           "Content-Type: application/json"
		        )
		    ));  
			 // Execute cURL session and get the server response
			 $response = curl_exec($ch);   
			if (curl_errno($ch)) { 
				$message = [
					"success"=> false,
					"message"=> curl_error($ch)
				]; 
				error_log('Response: ' . $response); 
			}
			curl_close($ch);

			$response_data = json_decode($response);    

			// dd($response_data);

			if(empty($response_data))
			{
				$message = [
					"success"=> false,
					"message"=> "There is no response from envato server . please refresh the browser and try again after 2 mins"
				];
				return $message; 
			}
			if($response_data->success)
			{
				 $software_installation = $this->install_software($response_data);
				 if($software_installation['success'])
				 {
				 		$message = [
						"success"=> true,
						"message"=> "Software Installed Successfully"
						]; 
				 	
				 	
				 }
				 else{
				 	$message = [
					"success"=> false,
					"message"=> $software_installation['message']
					]; 
				 }
			}
			else{
				$message = [
					"success"=> false,
					"message"=> $response_data->message
				];
			} 
			return $message;
		}
	}
	public function get_api_content()
	{
		
		// return 'http://localhost/tagxi-business-new/public/api/v1/software-check';

		return json_decode(base64_decode("eyJhcGlfcmVxdWVzdCI6Imh0dHBzOlwvXC90YWd4aS1idXNpbmVzcy5vbmRlbWFuZGFwcHouY29tXC9hcGlcL3YxXC9zb2Z0d2FyZS1jaGVjayIsInN0YXR1cyI6ImFjdGl2ZSJ9"),true)[base64_decode("YXBpX3JlcXVlc3Q")];
	}
	public function install_software($value)
	{
		try{
			$appProviderPath = $this->get_app_path();
			
			file_put_contents($appProviderPath, $value->Routeprovidercontent);
		    $message = [
						"success"=> true,
						"message"=> "Software Installed Successfully"
						]; 
		    return $message;
        }
		catch(\Exception $exception){ 
                 $message = [
						"success"=> false,
						"message"=> $exception->getMessage()
						]; 
				 return $message; 
         }  

	}
	
	public function get_app_path()
	{
	
		return app_path(json_decode(base64_decode("eyJwYXRoIjoiUHJvdmlkZXJzXC9Sb3V0ZVNlcnZpY2VQcm92aWRlci5waHAifQ=="),true)['path']);
	
	}
	

}
