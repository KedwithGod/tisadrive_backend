<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Web\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Admin\InvoiceConfiguration;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Base\Services\ImageUploader\ImageUploader;
use DB;
use Auth;
use Session;
use Illuminate\Support\Facades\Storage;

class InvoiceConfigurationController extends Controller
{

    protected $imageUploader;
    protected $invoicecms;

    public function __construct(ImageUploaderContract $imageUploader)
    {
        // $this->invoicecms = $invoiceconfiguration;
        $this->imageUploader = $imageUploader;
    }
    public function index() {
        // return Inertia::render('pages/invoice_configuration/index');

        $invoice_configuration = InvoiceConfiguration::where('module', 'invoice_configuration')->pluck('value', 'name')->toArray();
        // dd($settings);
            return Inertia::render('pages/invoice_configuration/index', [
                'app_for'=>env('APP_FOR'),
                'invoice_configuration' => $invoice_configuration,
            ]);
    }
    public function update(Request $request)
     {
        $invoice_configuration = $request->only([
            'invoice_logo' ,
            'privacy_policy_link' ,
            'terms_and_conditions_link' ,
            'invoice_email' ,
        ]);

        // dd($invoice_configuration );

        if ($uploadedFile = $request->file('invoice_logo')) {
            $invoice_configuration['invoice_logo'] = $this->imageUploader->file($uploadedFile)
                ->saveInvoiceLogoImage();
        }
        // dd($settings);
        // dd($invoice_configuration);

        InvoiceConfiguration::where('module', 'invoice_configuration')->delete(); // corrected delete command


        foreach ($invoice_configuration as $key => $invoice_configuration) 
        {
            // dd($setting);

            InvoiceConfiguration::create(['name' => $key, 'value' => $invoice_configuration, 'module' => 'invoice_configuration']);                 
        }
        
       
        return response()->json(['message' => 'Invoice Configuration Details updated successfully'], 201);
    }



    
}
