<?php

// Include AWS php sdk with composer autoload
require 'vendor/autoload.php';

use Aws\Credentials\CredentialProvider;
use Aws\Glacier\GlacierClient;
use Loader\MultipartUpload;

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
$vault = 'shakespeare-videos';

$archive = new MultipartUpload($dir, $vault, $client);
$archive->upload();
