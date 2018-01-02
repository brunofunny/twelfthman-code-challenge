<?php

namespace Twelfthman\Console\Commands;

use Illuminate\Console\Command;
use Twelfthman\Support\Helpers;
use ZipArchive;

class ImportImages extends Command
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
     * Path destination of images
     *
     * @var string
     */
    protected $destImgs = './public/imgs/';

    /**
     * Ignore files
     *
     * @var array
     */
    protected $ignoreFiles = [
        '__MACOSX'
    ];

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
        $tmpZipDirPath = './storage/tmp/' . $tmpZipDirLabel;
        $files = [];
        $filesNotCopied = [];
        $countSuccess = 0;

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

                        // Create a files array
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

                        // Move files
                        foreach($files as $filename) {
                            $pathInfo = pathinfo($filename);
                            $filenameUnique = md5_file($filename);
                            $filenameNew = $filenameUnique . '.' . $pathInfo['extension'];
                            
                            if (copy($filename, $this->destImgs.$filenameNew)) {
                                $countSuccess++;
                            } else {
                                $filesNotCopied[] = $pathInfo['basename'];
                            }
                        }
                        
                        $helper = new Helpers;
                        $helper->rmdir($tmpZipDirPath);

                        // Output statistics
                        $this->info($countSuccess . ' file(s) were copied successfully');
                        if (($countFailed = count($filesNotCopied)) > 0) {
                            $this->info($countFailed . ' file(s) failed:');
                            foreach ($filesNotCopied as $filesNotCopiedStr) {
                                $this->info('- ' . $filesNotCopiedStr);
                            }
                        }
                        
                    } else {
                        $this->error('Failed to extract files');
                    }
                } else {
                    $this->info('Zip file empty');
                }
            } else {
                $this->info('Could not open the file');
            }
        } else {
            $this->info('File not found');
        }
    }
}