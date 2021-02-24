<?php


namespace Loader;


use Aws\Glacier\GlacierClient;
use Aws\Glacier\MultipartUploader;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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
        $dir_iterator = new RecursiveDirectoryIterator($this->dir);
        $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $file_name = $file->getFilename() ;
                echo "Upload File Key : " . $file_name . "\n";
                echo "Uplad File Path : " . substr($file->getPathname(), 27) . "\n \n";
                $archiveData = fopen($file->getPathname(), 'r');
                $multi = new MultipartUploader($this->client, $archiveData, [
                    'vault_name' => $this->vault
                ]);

                $result = $multi->upload();
                $archiveId = $result->get('archiveId');

                fclose($archiveData);
                echo "archiveId : " . $archiveId . "\n \n";
            }
        }
    }
}
