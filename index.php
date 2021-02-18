<?php

// Include AWS php sdk with composer autoload
require 'vendor/autoload.php';
use Aws\Credentials\CredentialProvider;
use Aws\S3\S3Client;
use Aws\Glacier\GlacierClient;

$profile = 'default';
$path = './credentials.ini';

$provider = CredentialProvider::ini($profile, $path);
$provider = CredentialProvider::memoize($provider);

// Initialize your S3 connection.
// To generate your YOUR_IAM_USER_KEY and YOUR_IAM_USER_SECRET create and Aws IAM user with the S3FullAccess Role
// to do so follow : https://docs.aws.amazon.com/IAM/latest/UserGuide/id_users_create.html
$client = GlacierClient::factory(array(
    'region' => 'us-east-1',
    'version' => 'latest',
    'credentials' => $provider
));

$dir = "/Users/intellisys/awsgracier/uploads";

function uploadArchive(string $dir, GlacierClient $s3): void
{
    $dir_iterator = new RecursiveDirectoryIterator($dir);
    $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
    $vault = 'shakespeare-videos';
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $file_name = $file->getFilename() ;
            echo "Upload File Key : " . $file_name . "\n";
            echo "Uplad File Path : " . substr($file->getPathname(), 27) . "\n \n";
            $result = $s3->uploadArchive([
                'vaultName' => $vault,
                'body' => fopen($file->getPathname(), 'r+')
            ]);
            $archiveId = $result->get('archiveId');
            echo "archiveId : " . $archiveId . "\n \n";
        }
    }
}

uploadArchive($dir, $client);
