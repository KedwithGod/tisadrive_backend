<?php

namespace App\Transformers\Requests;

use App\Transformers\Transformer;
use App\Models\Master\MobileAppSetting;

class MobileAppSettingsTransformer extends Transformer
{
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
    protected array $availableIncludes = [

    ];

    /**
     * A Fractal transformer.
     *
     * @param MobileAppSetting $reason
     * @return array
     */
    public function transform(MobileAppSetting $mobile)
    {
        return [
            'id' => $mobile->id,
            'name' => $mobile->name,
            'service_type' => $mobile->service_type,
            'active' => $mobile->active,
            'menu_icon' => $mobile->mobile_menu_icon,
            'icon_types_for' => $mobile->icon_types_for,
        ];
    }
}
