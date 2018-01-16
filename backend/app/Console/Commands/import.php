<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Support\Helpers;
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
     * Max allowed size
     *
     * @var integer
     */
    protected $maxAllowedSize = 5242880;

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

                        // Move files to their final destination
                        foreach($files as $filename) {
                            $pathInfo = pathinfo($filename);
                            $filenameUnique = md5(md5_file($filename) . $pathInfo['basename']);
                            $filenameNew = $filenameUnique . '.' . $pathInfo['extension'];

                            if (filesize($filename) < $this->maxAllowedSize) {
                                // Check if the file is a duplicate (prevent from run multiple times the same file)
                                $imageExists = Image::where(['file_system_name' => $filenameNew])->first();
                                if (!$imageExists) {
                                    if (copy($filename, $this->destImgs.$filenameNew)) {
                                        $image = new Image;
                                        $image->file_original_name = $pathInfo['basename'];
                                        $image->file_system_name = $filenameNew;
                                        $image->file_extension = $pathInfo['extension'];
                                        $image->caption = $this->captionGenerate();
                                        $image->deleted = false;
                                        $image->save();
    
                                        $countSuccess++;
                                    } else {
                                        $this->filesNotCopied($pathInfo['basename'], filesize($filename), 'Could not copy file');
                                    }
                                } else {
                                    $this->filesNotCopied($pathInfo['basename'], filesize($filename), 'Duplicated file');
                                }
                            } else {
                                $this->filesNotCopied($pathInfo['basename'], filesize($filename), 'File size is bigger than allowed');
                            }
                        }

                        // Remove temp dir
                        $helper = new Helpers;
                        $helper->rmdir($tmpZipDirPath);

                        // Output statistics
                        $this->info($countSuccess . ' file(s) were copied successfully');
                        if (($countFailed = count($this->filesNotCopied)) > 0) {
                            $this->warn($countFailed . ' file(s) failed:');
                            foreach ($this->filesNotCopied as $files) {
                                $this->error('Name: ' . $files['name'] . ' | Filesize: ' . $files['filesize'] . ' | Msg: ' . $files['msg'] );
                            }
                        }

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
    public function filesNotCopied($filename, $filesize, $error)
    {
        $this->filesNotCopied[] = [
            'name' => $filename,
            'filesize' => $filesize,
            'msg' => $error
        ];
    }

    /**
     * Generate a random name for caption
     *
     * @return string
     */
    public function captionGenerate()
    {
        $captions = [
            'Partnership Rationale',
            'Who we are',
            'Our clubs',
            'The opportunity',
            'Last Crusade Till Tomorrow',
            'Fighting for more resources',
            'Once Upon a Time',
        ];

        return $captions[rand(0, count($captions) - 1)];
    }

}