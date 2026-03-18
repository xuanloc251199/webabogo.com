<?php

namespace Database\Seeders;

use Botble\Ecommerce\Models\GlobalOption;
use Botble\Ecommerce\Models\GlobalOptionValue;
use Botble\Ecommerce\Option\OptionType\Dropdown;
use Botble\Ecommerce\Option\OptionType\RadioButton;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductOptionSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            [
                'name' => 'Dịch vụ đi kèm',
                'option_type' => RadioButton::class,
                'required' => true,
                'values' => [
                    [
                        'option_value' => 'BBQ',
                        'affect_price' => 15,
                        'affect_type' => 0,
                    ],
                    [
                        'option_value' => 'Ăn sáng',
                        'affect_price' => 10,
                        'affect_type' => 0,
                    ],
                    [
                        'option_value' => 'Bảo hiểm',
                        'affect_price' => 20,
                        'affect_type' => 0,
                    ],
                ],
            ],
        ];

        DB::table('ec_global_options')->truncate();
        DB::table('ec_global_option_value')->truncate();
        DB::table('ec_options')->truncate();
        DB::table('ec_option_value')->truncate();

        $this->saveGlobalOption($options);
    }

    protected function saveGlobalOption(array $options): void
    {
        foreach ($options as $option) {
            $globalOption = new GlobalOption();
            $globalOption->name = $option['name'];
            $globalOption->option_type = $option['option_type'];
            $globalOption->required = $option['required'];
            $globalOption->save();
            $optionValue = $this->formatGlobalOptionValue($option['values']);
            $globalOption->values()->saveMany($optionValue);
        }
    }

    protected function formatGlobalOptionValue(array $data): array
    {
        $values = [];
        foreach ($data as $item) {
            $globalOptionValue = new GlobalOptionValue();
            $item['affect_price'] = ! empty($item['affect_price']) ? $item['affect_price'] : 0;
            $globalOptionValue->fill($item);
            $values[] = $globalOptionValue;
        }

        return $values;
    }
}
