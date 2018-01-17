<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use App\Support\Helpers;
use Faker\Factory as Faker;
use App\Image;
use ZipArchive;

class import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:images {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import images from zip file';

    /**
     * Ignore files
     *
     * @var array
     */
    protected $ignoreFiles = [
        '__MACOSX'
    ];

    /**
     * Files copied
     *
     * @var array
     */
    protected $filesCopied = [];

    /**
     * Files not copied
     *
     * @var array
     */
    protected $filesNotCopied = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $file = $this->argument('file');
        $tmpZipDirLabel = uniqid();
        $tmpZipDirPath = config('custom.images.temporaryPath') . $tmpZipDirLabel;
        $files = [];

        if (file_exists($file)) {
            $zip = new ZipArchive;
            if ($zip->open($file) === TRUE) {
                if ($zip->numFiles !== 0) {
                    if ($zip->extractTo($tmpZipDirPath)) {
                        $zip->close();

                        // Iterating and search
                        $tmpDir = new \RecursiveDirectoryIterator($tmpZipDirPath);
                        $iterator = new \RecursiveIteratorIterator($tmpDir);
                        $filter = new \RegexIterator($iterator, '/^.+(.jpe?g|.png)$/i', \RecursiveRegexIterator::GET_MATCH);

                        // Creating files array
                        foreach($filter as $filename => $filter) {
                            $files[] = $filename;
                        }

                        // Filter files
                        $files = array_filter($files, function($i) {
                            foreach ($this->ignoreFiles as $ignoreStr) {
                                if (strpos($i, $ignoreStr) !== FALSE) {
                                    return false;
                                }
                            }
                            return true;
                        });

                        // Move files to media folder
                        foreach($files as $filename) {

                            $pathInfo = pathinfo($filename);

                            // Check if file isn't bigger than maximum allowed
                            if (filesize($filename) < config("custom.images.maxAllowedSize")) {

                                $newFilename = md5($filename . time());
                                if (Storage::disk('images')->put('./', new File($filename))) {
                                    $image = new Image;
                                    $image->file_original_name = $pathInfo['basename'];
                                    $image->file_system_name = $newFilename;
                                    $image->file_extension = $pathInfo['extension'];
                                    $image->caption = (Faker::create())->catchPhrase;
                                    $image->deleted = false;
                                    $image->save();

                                    $this->fileCopyLog(true, $pathInfo['basename'], filesize($filename));
                                } else {
                                    $this->fileCopyLog(false, $pathInfo['basename'], filesize($filename), 'Could not copy file');
                                }
                            } else {
                                $this->fileCopyLog(false, $pathInfo['basename'], filesize($filename), 'File size is bigger than allowed');
                            }
                        }

                        // Remove temp dir
                        $helper = new Helpers;
                        $helper->rmdir($tmpZipDirPath);

                        // Output statistics
                        $this->table(['Filename', 'Size', 'Message'], array_merge($this->filesNotCopied, $this->filesCopied));

                    } else {
                        $this->error('Failed to extract files');
                    }
                } else {
                    $this->warn('Zip file empty');
                }
            } else {
                $this->error('Could not open the file');
            }
        } else {
            $this->error('File not found');
        }
    }

    /**
     * Log files not copied
     *
     * @return void
     */
    public function fileCopyLog($success, $filename, $filesize, $error = "OK")
    {
        $support = new Helpers;

        if ($success) {
            $this->filesCopied[] = [
                'name' => $filename,
                'filesize' => $support->formatBytes($filesize),
                'msg' => 'OK'
            ];
        } else {
            $this->filesNotCopied[] = [
                'name' => $filename,
                'filesize' => $support->formatBytes($filesize),
                'msg' => $error
            ];
        }

    }

}