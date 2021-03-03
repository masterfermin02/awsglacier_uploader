<?php


namespace Loader;


use Aws\Glacier\GlacierClient;
use Aws\Glacier\MultipartUploader;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Exception;

class MultiPartUpload
{
    private $dir;
    private $vault;
    private $client;

    public function __construct(string $dir, string $vault, GlacierClient $client)
    {
        $this->dir = $dir;
        $this->vault = $vault;
        $this->client = $client;
    }

    public function upload(): void
    {
        $dir_iterator = new RecursiveDirectoryIterator($this->dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);


        foreach ($iterator as $file) {
            if ($file->isFile() && substr( $file->getFilename(), 0, 1) !== '.') {
                $file_name = $file->getFilename() ;
                echo "Upload File Key : " . $file_name . "\n";
                echo "Uplad File Path : " . substr($file->getPathname(), 27) . "\n \n";
                try {
                    $archiveData = fopen($file->getPathname(), 'r');
                    $multi = new MultipartUploader($this->client, $archiveData, [
                        'vault_name' => $this->vault
                    ]);

                    $result = $multi->upload();
                    /*while (!$result->getState()->isCompleted())
                    {
                        echo print_r($this->getState()->getUploadedParts());
                        echo PHP_EOL;
                        echo "Process ..." . PHP_EOL;
                        sleep(5);
                    }*/
                    $archiveId = $result->get('archiveId');


                    fclose($archiveData);
                } catch (Exception $e) {
                    $handle = fopen('errorLog.txt', 'a+');
                    $log = "Message error: " . $e->getMessage() . ", file path: " . $file->getPathname() . PHP_EOL;
                    fwrite ( $handle , $log);
                    fclose($handle);

                }

                echo "archiveId : " . $archiveId . "\n \n";
            }
        }
    }
}
