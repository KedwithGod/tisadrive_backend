<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Admin\Setting;
use App\Models\ThirdPartySetting;

class MailConfigurationController  extends Controller
{
    public function index() 
    {

        $settings = ThirdPartySetting::where('module', 'mail_config')->pluck('value', 'name')->toArray(); // firebase data
// dd($settings);
        return Inertia::render('pages/mail_configuration/index', [
            'app_for'=>env('APP_FOR'),
            'settings' => $settings,
        ]);

    }


    public function update(Request $request) 
    {
        // dd($request->all());
        ThirdPartySetting::where('module', 'mail_config')->delete(); // corrected delete command


        $settings = $request->only(['mail_mailer','mail_host','mail_port','mail_username','mail_password','mail_encryption','mail_from_address','mail_from_name']);
        
        if($settings['mail_password']) {
            $settings['mail_password'] = str_replace(' ','',$settings['mail_password']);
        }
        foreach ($settings as $key => $setting) 
        {
            // dd($setting);

            ThirdPartySetting::create(['name' => $key, 'value' => $setting, 'module' => 'mail_config']);                 
        }

         // Update the .env file with the new settings
         $this->updateEnvFile($settings);
                     
       return response()->json(['message' => 'Mail  Destails updated successfully'], 201);


    }

      /**
 * Update the .env file with new settings.
 *
 * @param array $settings
 * @return void
 */
private function updateEnvFile(array $settings)
{
    // Get the path to the .env file
    $envPath = base_path('.env');

    // Check if the .env file exists
    if (file_exists($envPath)) {
        // Read the current content of the .env file
        $envContent = file_get_contents($envPath);

        // Update or add each setting in the .env file
        foreach ($settings as $key => $value) {
            $envKey = strtoupper($key); // Convert the key to uppercase to match the .env convention

            // Create a regex pattern to match the existing key-value pair
            $pattern = "/^{$envKey}=[^\r\n]*/m";

            // If the key exists, replace it; otherwise, append the new key-value pair
            if (preg_match($pattern, $envContent)) {
                if($envKey == "MAIL_FROM_NAME"){
                    $envContent = preg_replace($pattern, "{$envKey}='{$value}'", $envContent);
                }else{
                    $envContent = preg_replace($pattern, "{$envKey}={$value}", $envContent);
                }
            } else {

                if($envKey == "MAIL_FROM_NAME"){
                    $envContent .= "\n{$envKey}='{$value}'";
                }else{
                    $envContent .= "\n{$envKey}={$value}";
                }
            }
        }

        // Write the updated content back to the .env file
        file_put_contents($envPath, $envContent);
    }
}
}
