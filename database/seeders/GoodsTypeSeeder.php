<?php

namespace Database\Seeders;

use App\Models\Admin\GoodsType;
use App\Models\Admin\GoodsTypeTranslation;
use App\Models\Admin\Promo;
use App\Models\Admin\PromoCodeUser;
use Illuminate\Database\Seeder;

class GoodsTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $goods_type = [
        ['goods_type_name' => 'Timber/Plywood/Laminate',
            'goods_types_for' => 'both',
            'active' => 1,
        ],
        ['goods_type_name' => 'Electrical/Electronics/Home Appliances',
            'goods_types_for' => 'both',
            'active' => 1,
        ],
        ['goods_type_name' => 'Building/Construction',
            'goods_types_for' => 'both',
            'active' => 1,
        ],
        ['goods_type_name' => 'Catering/Restaurant/Event Management',
            'goods_types_for' => 'both',
            'active' => 1,
        ],
        ['goods_type_name' => 'Machines/Equipments/Spare Parts/Metals',
            'goods_types_for' => 'both',
            'active' => 1,
        ],
        ['goods_type_name' => 'Textile/Garments/Fashion Accessories',
            'goods_types_for' => 'both',
            'active' => 1,
        ],
        ['goods_type_name' => 'Furniture/Home Furnishing',
            'goods_types_for' => 'both',
            'active' => 1,
        ],
        ['goods_type_name' => 'House Shifting',
            'goods_types_for' => 'both',
            'active' => 1,
        ],
        ['goods_type_name' => 'Ceramics/Sanitaryware/HardWare',
            'goods_types_for' => 'both',
            'active' => 1,
        ],
        ['goods_type_name' => 'Paper/Packaging/Printed Material',
            'goods_types_for' => 'both',
            'active' => 1,
        ],
        ['goods_type_name' => 'Chemicals/Paints',
            'goods_types_for' => 'both',
            'active' => 1,
        ],
        ['goods_type_name' => 'Logistics service provider/Packers and Movers',
            'goods_types_for' => 'both',
            'active' => 1,
        ],
        ['goods_type_name' => 'Perishable Food Items',
            'goods_types_for' => 'both',
            'active' => 1,
        ],
        ['goods_type_name' => 'Pharmacy/Medical?Healthcare/Fitness Equipment',
            'goods_types_for' => 'both',
            'active' => 1,
        ],
        ['goods_type_name' => 'FMCG/Food Products',
            'goods_types_for' => 'both',
            'active' => 1,
        ],
        ['goods_type_name' => 'Jewelry/Watches',
            'goods_types_for' => 'both',
            'active' => 1,
        ],
        ['goods_type_name' => 'Plastic/Rubber',
            'goods_types_for' => 'both',
            'active' => 1,
        ],
        ['goods_type_name' => 'Books/Stationery/Toys/Gifts',
            'goods_types_for' => 'both',
            'active' => 1,
        ]
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $created_params = $this->goods_type;

        $value = GoodsType::first();
        if(!$value){
            foreach ($created_params as $goods) 
            {
                $value = GoodsType::create($goods);
                $translationData = ['name' => $goods['goods_type_name'], 'locale' => 'en', 'goods_type_id' => $value->id];
                $translations_data['en'] = new \stdClass();
                $translations_data['en']->locale = 'en';
                $translations_data['en']->name = $goods['goods_type_name'];
                $value->goodsTypeTranslationWords()->insert($translationData);
                $value->translation_dataset = json_encode($translations_data);
                $value->save();
            }
        }

    }
}
