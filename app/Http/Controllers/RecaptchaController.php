<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\ThirdPartySetting;
use Illuminate\Http\Request;

class RecaptchaController extends Controller
{
    public function index() {
        $settings = ThirdPartySetting::where('module', 'recaptcha')->pluck('value', 'name')->toArray();
        return Inertia::render('pages/recaptcha/index', [
            'app_for'=>env('APP_FOR'),
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $settings = $request->only([
            'enable_recaptcha',
            'reacptcha_site_key',
            'reacptcha_secret_key',
        ]);

        // dd($settings);

        ThirdPartySetting::where('module', 'recaptcha')->delete(); // corrected delete command


        foreach ($settings as $key => $setting) 
        {
            // dd($setting);

            ThirdPartySetting::create(['name' => $key, 'value' => $setting, 'module' => 'recaptcha']);                 
        }

        // Update the .env file with the new settings
             $this->updateEnvFile($settings);
             
        return response()->json(['message' => 'Recaptcha  Destails updated successfully'], 201);

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
                $envContent = preg_replace($pattern, "{$envKey}={$value}", $envContent);
            } else {
                $envContent .= "\n{$envKey}={$value}";
            }
        }

        // Write the updated content back to the .env file
        file_put_contents($envPath, $envContent);
    }
}
}
