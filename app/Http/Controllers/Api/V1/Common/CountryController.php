<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Models\Country;
use App\Http\Controllers\ApiController;
use App\Transformers\CountryTransformer;
use App\Transformers\CountryNewTransformer;
use App\Models\Admin\Onboarding;

use App\Transformers\OnboardingTransformer;

/**
 * @group Countries
 *
 * Get countries
 */
class CountryController extends ApiController
{

    /**
     * Get all the countries.
     *
     * @return \Illuminate\Http\JsonResponse
     * @response 
     * {
     *  "success": true,
     *  "data": [
     *      {
     *          "id": 1,
     *          "dial_code": "+93",
     *          "name": "Afghanistan",
     *          "code": "AF",
     *          "flag": "http://localhost/new_vue_tagxi/public/image/country/flags/AF.png",
     *          "dial_min_length": 7,
     *          "dial_max_length": 14,
     *          "active": true,
     *          "default": false
     *      },
     *  ]
     * }
     */
    public function index()
    {
        $countriesQuery = Country::active();

        $countries = filter($countriesQuery, new CountryTransformer)->defaultSort('name')->get();

        return $this->respondOk($countries);
    }
    /**
     * Get User OnBoarding screens
     * 
     * @response
     * {
     *    "success": true,
     *    "data": {
     *        "onboarding": {
     *            "data": [
     *                {
     *                    "order": 3,
     *                    "id": 3,
     *                    "screen": "user",
     *                    "title": "Intuitive",
     *                    "onboarding_image": "http://localhost/new_vue_tagxi/public/storage/uploads/onboarding/onboard2.jpg",
     *                    "description": "Seamless journeys,\nJust a tap away,\nExplore hassle-free,\nEvery step of the way.",
     *                    "active": 1
     *                },
     *            ]
     *        }
     *    }
     *}
     */
    public function onboarding()
    {
        $countriesQuery = Country::active();
        $onboardingQuery = Onboarding::where('screen', 'user')->active();
        $onboarding = filter($onboardingQuery, new OnboardingTransformer)->defaultSort('title')->get();
        $response = [
            'onboarding' => $onboarding
        ];

        return $this->respondOk($response);
    }

    /**
     * Get Driver OnBoarding screens
     * 
     * @response
     * {
     *    "success": true,
     *    "data": {
     *        "onboarding": {
     *            "data": [
     *                {
     *                    "order": 2,
     *                    "id": 6,
     *                    "screen": "driver",
     *                    "title": "Clarity",
     *                    "onboarding_image": "http://localhost/new_vue_tagxi/public/storage/uploads/onboarding/onboard4.jpg",
     *                    "description": "Fair pricing, crystal clear,\nYour trust, our promise sincere.\nWith us, youll find no hidden fee,\nTransparency is our guarantee.",
     *                    "active": 1
     *                },
     *            ]
     *        }
     *    }
     * }
     */
    public function onBoardingDriver()
    {
        $countriesQuery = Country::active();
        $onboardingQuery = Onboarding::where('screen', 'driver')->active();
        $onboarding = filter($onboardingQuery, new OnboardingTransformer)->defaultSort('title')->get();
        $response = [
            'onboarding' => $onboarding
        ];

        return $this->respondOk($response);
    }

    /**
     * Get Owner OnBoarding screens
     * 
     * @response
     * {
     *    "success": true,
     *    "data": {
     *        "onboarding": {
     *            "data": [
     *                {
     *                    "order": 4,
     *                    "id": 12,
     *                    "screen": "owner",
     *                    "title": "Support",
     *                    "onboarding_image": "http://localhost/new_vue_tagxi/public/storage/uploads/onboarding/onboard3.jpg",
     *                    "description": "Embark on your journey with confidence, knowing that our commitment to your satisfaction is unwavering",
     *                    "active": 1
     *                },
     *            ]
     *        }
     *    }
     * }
     */
    public function onBoardingOwner()
    {
        $countriesQuery = Country::active();
        $onboardingQuery = Onboarding::where('screen', 'owner')->active();
        $onboarding = filter($onboardingQuery, new OnboardingTransformer)->defaultSort('title')->get();
        $response = [
            'onboarding' => $onboarding
        ];

        return $this->respondOk($response);
    }


}
