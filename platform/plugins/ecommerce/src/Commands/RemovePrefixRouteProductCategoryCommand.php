<?php

namespace Botble\Ecommerce\Commands;

use Botble\Ecommerce\Models\ProductCategory;
use Botble\Slug\Models\Slug;
use Illuminate\Console\Command;

class RemovePrefixRouteProductCategoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-prefix-route-product-category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa tiền tố của route product category';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $slugs = Slug::query()->where("prefix","product-categories")
            ->where("reference_type",ProductCategory::class)
            ->update(["prefix"=>""]);
        $this->info("Done");
    }
}
