<?php

namespace Botble\Ecommerce\Commands;

use Botble\Media\Http\Resources\FolderResource;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class PublishAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:publish:assets:ecommerce';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all file js css in ecommerce/resources to ecommerce/public';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $filesystem)
    {
        $folder_assets = plugin_path('ecommerce/resources/assets/js');
        $path_plugin_public = plugin_path('ecommerce/public/js');
        $files = $filesystem->allFiles($folder_assets);
        if (!$filesystem->exists($path_plugin_public)) {
            $filesystem->makeDirectory($path_plugin_public);
        }
        foreach ($files as $file) {
            if ($file->getExtension() === 'js') {
                $fileMin = $file->getFilenameWithoutExtension() . ".min." . $file->getExtension();
                $filesystem->copy($file->getRealPath(), $path_plugin_public . "/" . $fileMin);
            }
        }
        $folder_assets = plugin_path('ecommerce/resources/assets/sass');
        $path_plugin_public = plugin_path('ecommerce/public/css');
        $files = $filesystem->allFiles($folder_assets);
        if (!$filesystem->exists($path_plugin_public)) {
            $filesystem->makeDirectory($path_plugin_public);
        }
        foreach ($files as $file) {
            if ($file->getExtension() === 'scss') {
                $fileMin = $file->getFilenameWithoutExtension() . ".css";
                $filesystem->copy($file->getRealPath(), $path_plugin_public . "/" . $fileMin);
            }
        }
        $this->info('Assets published successfully!');
    }
}
