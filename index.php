<?php

// Include AWS php sdk that you can download here : https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/getting-started_installation.html
require 'vendor/autoload.php';
use Aws\S3\S3Client;

// Initialize your S3 connection.
// To generate your YOUR_IAM_USER_KEY and YOUR_IAM_USER_SECRET create and Aws IAM user with the S3FullAccess Role
// to do so follow : https://docs.aws.amazon.com/IAM/latest/UserGuide/id_users_create.html
$s3 = new Aws\S3\S3Client([
    'region' => 'YOUR_S3_REGION',
    'version' => 'latest',
    'credentials' => [
        'key'    => "YOUR_IAM_USER_KEY",
        'secret' => "YOUR_IAM_USER_SECRET",
    ]
]);

$dir = "/Users/intellisys/awsgracier/uploads";
//for example $dir = "/home/ubuntu/Projets/MY_PROJECT/MY_FOLDER_TO_UPLOAD/*";

function uploadFolder(string $dir, S3Client $s3): void
{
    $dir_iterator = new RecursiveDirectoryIterator($dir);
    $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $file_name = $file->getFilename() ;
            echo "Upload File Key : " . $file_name . "\n";
            echo "Uplad File Path : " . substr($file->getPathname(), 27) . "\n \n";
            $s3->putObject([
                'Bucket' => 'YOUR_S3_BUCKET_NAME',
                'Key'    => 'FOLDER_NAME_INSIDE_S3_BUCKET/'.$file_name,
                'Body' => fopen($file->getPathname(), 'r+')
            ]);

            // Wait for the file to be uploaded and accessible :
            $s3->waitUntil('ObjectExists', array(
                'Bucket' => 'YOUR_S3_BUCKET_NAME',
                'Key'    => 'FOLDER_NAME_INSIDE_S3_BUCKET/'.$file_name
            ));
        }
    }
}

uploadFolder($dir, $s3);
