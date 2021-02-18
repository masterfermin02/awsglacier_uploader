<?php


namespace Loader;


use Aws\Glacier\GlacierClient;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Archive
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
                $result = $this->client->uploadArchive([
                    'vaultName' => $this->vault,
                    'body' => fopen($file->getPathname(), 'r+')
                ]);
                $archiveId = $result->get('archiveId');
                echo "archiveId : " . $archiveId . "\n \n";
            }
        }
    }
}
