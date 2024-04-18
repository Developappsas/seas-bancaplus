<?php

require_once "../plugins/vendor/autoload.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\BlobSharedAccessSignatureHelper;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
use MicrosoftAzure\Storage\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
use MicrosoftAzure\Storage\Blob\Models\DeleteBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\GetBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\ContainerACL;
use MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions;
use MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Exceptions\InvalidArgumentTypeException;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\StorageServiceSettings;
use MicrosoftAzure\Storage\Common\Models\Range;
use MicrosoftAzure\Storage\Common\Models\Logging;
use MicrosoftAzure\Storage\Common\Models\Metrics;
use MicrosoftAzure\Storage\Common\Models\RetentionPolicy;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

function cadena_conexion(){
    return 'DefaultEndpointsProtocol=https;AccountName=azstacuse2prdacce;AccountKey=4mx1TFojZOh0uFligYKU3RRrd8YyENHdt4YuJiNm/pmYNyYKArewwtyILh28htGGY4kunaFOGHYz+ASt6dXhbA==;EndpointSuffix=core.windows.net';
}

function upload_file3($file, $containerName, $nameFile, $metadata = []){
    try{
        $connectionString = cadena_conexion();
 
        $settings = StorageServiceSettings::createFromConnectionString($connectionString);
        $accountName = $settings->getName();
        $accountKey = $settings->getKey();
 
        $helper = new BlobSharedAccessSignatureHelper(
            $accountName,
            $accountKey
        );
        $datetime = new DateTime();
        $datetime1 = new DateTime();
        $datetime1->add(new DateInterval('PT3M'));
        // Refer to following link for full candidate values to construct a service level SAS
        // https://docs.microsoft.com/en-us/rest/api/storageservices/constructing-a-service-sas
        $sas = $helper->generateBlobServiceSharedAccessSignatureToken(
            Resources::RESOURCE_TYPE_BLOB,
            "$containerName/$nameFile",
            'cw',                            // Write
            $datetime1, //,       // A valid ISO 8601 format expiry time
            $datetime       // A valid ISO 8601 format expiry time
        );
        $connectionStringWithSAS = Resources::BLOB_ENDPOINT_NAME .
            '=' .
            'https://' .
            $accountName .
            '.' .
            Resources::BLOB_BASE_DNS_NAME .
            ";" .
            Resources::SAS_TOKEN_NAME .
            '=' .
            $sas;
 
        $blobClientWithSAS = BlobRestProxy::createBlobService(
            $connectionStringWithSAS
        );
        $mensaje = $connectionStringWithSAS;
        $options = new CreateBlockBlobOptions();
        $options->setContentType("application/pdf");
 
        $content = base64_decode($file);
 
        $blobClientWithSAS->createBlockBlob($containerName, $nameFile, $content, $options);
        $blobClientWithSAS->setBlobMetadata($containerName, $nameFile, $metadata);
 
        return array("response" => true, "message" => "Documento Cargado Satisfactoriamente");
 
    } catch (ServiceException $exception) {
 
        $message = $exception->getErrorMessage();
        $message = str_replace("\n", ' ', $message);
 
        return array("response" => false, "message" => $message);
    }
}

function upload_file2($file, $containerName, $nameFile, $metadata = []){
    try{
        $connectionString = cadena_conexion();

        $settings = StorageServiceSettings::createFromConnectionString($connectionString);
        $accountName = $settings->getName();
        $accountKey = $settings->getKey();

        $helper = new BlobSharedAccessSignatureHelper(
            $accountName,
            $accountKey
        );
        $datetime = new DateTime();
        $datetime1 = new DateTime();
        $datetime1->add(new DateInterval('PT3M'));
        // Refer to following link for full candidate values to construct a service level SAS
        // https://docs.microsoft.com/en-us/rest/api/storageservices/constructing-a-service-sas
        $sas = $helper->generateBlobServiceSharedAccessSignatureToken(
            Resources::RESOURCE_TYPE_BLOB,
            "$containerName/$nameFile",
            'cw',                            // Write
            $datetime1, //,       // A valid ISO 8601 format expiry time
            $datetime       // A valid ISO 8601 format expiry time
        );
        $connectionStringWithSAS = Resources::BLOB_ENDPOINT_NAME .
            '=' .
            'https://' .
            $accountName .
            '.' .
            Resources::BLOB_BASE_DNS_NAME .
            ";" .
            Resources::SAS_TOKEN_NAME .
            '=' .
            $sas;

        $blobClientWithSAS = BlobRestProxy::createBlobService(
            $connectionStringWithSAS
        );
        $mensaje = $connectionStringWithSAS;
        $options = new CreateBlockBlobOptions();
        $options->setContentType("application/pdf");
        $content = fopen($file, "r");

        $blobClientWithSAS->createBlockBlob($containerName, $nameFile, $content, $options);
        $blobClientWithSAS->setBlobMetadata($containerName, $nameFile, $metadata);

        return array("response" => true, "message" => "Documento Cargado Satisfactoriamente");

    } catch (ServiceException $exception) {

        $message = $exception->getErrorMessage();
        $message = str_replace("\n", ' ', $message);

        return array("response" => false, "message" => $message);
    }
}

function generateBlobDownloadLinkWithSAS($containerName, $blobPath){

    $connectionString = cadena_conexion();

    $settings = StorageServiceSettings::createFromConnectionString($connectionString);
    $accountName = $settings->getName();
    $accountKey = $settings->getKey();

    $helper = new BlobSharedAccessSignatureHelper(
        $accountName,
        $accountKey
    );
    $datetime = new DateTime();
    $datetime1 = new DateTime();
    $datetime1->add(new DateInterval('PT9H'));
    // Refer to following link for full candidate values to construct a service level SAS
    // https://docs.microsoft.com/en-us/rest/api/storageservices/constructing-a-service-sas
    $sas = $helper->generateBlobServiceSharedAccessSignatureToken(
        Resources::RESOURCE_TYPE_BLOB,
        "$containerName/$blobPath",
        'r',                            // Read
        $datetime1, //,       // A valid ISO 8601 format expiry time
        $datetime       // A valid ISO 8601 format expiry time
        //'0.0.0.0-255.255.255.255'
        //'https,http'
    );

    $connectionStringWithSAS = Resources::BLOB_ENDPOINT_NAME .
        '=' .
        'https://' .
        $accountName .
        '.' .
        Resources::BLOB_BASE_DNS_NAME .
        ';' .
        Resources::SAS_TOKEN_NAME .
        '=' .
        $sas;

    $blobClientWithSAS = BlobRestProxy::createBlobService(
        $connectionStringWithSAS
    );

    // Or generate a temporary readonly download URL link
    $blobUrlWithSAS = sprintf(
        '%s%s?%s',
        (string)$blobClientWithSAS->getPsrPrimaryUri(),
        "$containerName/$blobPath",
        $sas
    );

    return $blobUrlWithSAS . $server_output;
}

function upload_file($file, $containerName, $nameFile, $metadata = []){

    try{
        $connectionString = cadena_conexion();

        $settings = StorageServiceSettings::createFromConnectionString($connectionString);
        $accountName = $settings->getName();
        $accountKey = $settings->getKey();

        $helper = new BlobSharedAccessSignatureHelper(
            $accountName,
            $accountKey
        );
        $datetime = new DateTime();
        $datetime1 = new DateTime();
        $datetime1->add(new DateInterval('PT3M'));
        // Refer to following link for full candidate values to construct a service level SAS
        // https://docs.microsoft.com/en-us/rest/api/storageservices/constructing-a-service-sas
        $sas = $helper->generateBlobServiceSharedAccessSignatureToken(
            Resources::RESOURCE_TYPE_BLOB,
            "$containerName/$nameFile",
            'cw',                            // Write
            $datetime1, //,       // A valid ISO 8601 format expiry time
            $datetime       // A valid ISO 8601 format expiry time
        );
        $connectionStringWithSAS = Resources::BLOB_ENDPOINT_NAME .
            '=' .
            'https://' .
            $accountName .
            '.' .
            Resources::BLOB_BASE_DNS_NAME .
            ";" .
            Resources::SAS_TOKEN_NAME .
            '=' .
            $sas;

            $blobClientWithSAS = BlobRestProxy::createBlobService(
            $connectionStringWithSAS
        );
        $mensaje = $connectionStringWithSAS;
        $options = new CreateBlockBlobOptions();
        $options->setContentType(mime_content_type($file["tmp_name"]));
        $content = fopen($file["tmp_name"], "r");

        $blobClientWithSAS->createBlockBlob($containerName, $nameFile, $content, $options);
        $blobClientWithSAS->setBlobMetadata($containerName, $nameFile, $metadata);

        return array("response" => true, "message" => "Documento Cargado Satisfactoriamente");
    } catch (ServiceException $exception) {

        $message = $exception->getErrorMessage();
        $message = str_replace("\n", ' ', $message);

        return array("response" => false, "message" => $message);
    }
}

function delete_file($containerName, $nameFile){
    try{
        $connectionString = cadena_conexion();

        $settings = StorageServiceSettings::createFromConnectionString($connectionString);
        $accountName = $settings->getName();
        $accountKey = $settings->getKey();

        $helper = new BlobSharedAccessSignatureHelper(
            $accountName,
            $accountKey
        );

        $datetime = new DateTime();
        $datetime1 = new DateTime();
        $datetime1->add(new DateInterval('PT3M'));
        // Refer to following link for full candidate values to construct a service level SAS
        // https://docs.microsoft.com/en-us/rest/api/storageservices/constructing-a-service-sas
        $sas = $helper->generateBlobServiceSharedAccessSignatureToken(
            Resources::RESOURCE_TYPE_BLOB,
            "$containerName/$nameFile",
            'd',                            // Write
            $datetime1, //,       // A valid ISO 8601 format expiry time
            $datetime       // A valid ISO 8601 format expiry time
        );

        $connectionStringWithSAS = Resources::BLOB_ENDPOINT_NAME .
            '=' .
            'https://' .
            $accountName .
            '.' .
            Resources::BLOB_BASE_DNS_NAME .
            ";" .
            Resources::SAS_TOKEN_NAME .
            '=' .
            $sas;

        $blobClientWithSAS = BlobRestProxy::createBlobService(
            $connectionStringWithSAS
        );

        $blobClientWithSAS->deleteBlob($containerName, $nameFile);

        return array("response" => true, "message" => "Archivo Eliminado Satisfactoriamente");
    } catch (ServiceException $exception) {

        $message = $exception->getErrorMessage();
        $message = str_replace("\n", ' ', $message);

        return array("response" => false, "message" => $message);
    }
}