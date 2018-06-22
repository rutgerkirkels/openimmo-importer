<?php

class Importer
{
    /**
     * @var string
     */
    protected $uploadDir;

    /**
     * @var string
     */
    protected $dataDir;

    public function __construct($uploadDir = '', $dataDir = '/tmp/eifelimportdata')
    {
        if ($uploadDir !== '') {
            $this->uploadDir = $uploadDir;
        }
        $this->dataDir = $dataDir;
//        $this->deleteDataDir();
    }

    public function getUploadedFiles($extension = 'zip')
    {
        $files = [];
        foreach (scandir($this->uploadDir) as $item) {
            if (stristr($item, '.' . $extension)) {
                $files[] = $item;
            }
        }

        return $files;
    }

    public function unpackUploadedFile($filename)
    {
        $zip = new ZipArchive();
        $zip->open($this->uploadDir . DIRECTORY_SEPARATOR . $filename);
        $zip->extractTo($this->dataDir);
    }

    protected function createDataDir()
    {
        mkdir($this->dataDir);
    }

    protected function deleteDataDir()
    {
        $it = new RecursiveDirectoryIterator($this->dataDir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
            RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($this->dataDir);
    }

    public function processUploadedFiles()
    {
        foreach ($this->getUploadedFiles() as $uploadedFile) {
            $this->processUploadedFile($uploadedFile);
        }
    }

    protected function processUploadedFile($filename)
    {
        $this->createDataDir();
        $this->unpackUploadedFile($filename);
        $this->readXml($filename);
//        $this->deleteDataDir();
    }

    protected function readXml()
    {
        foreach (glob($this->dataDir . DIRECTORY_SEPARATOR . '*.xml') as $file) {
            $this->processXmlFile($file);
        }


    }

    protected function processXmlFile($file)
    {
        $xml = simplexml_load_file($file,null, LIBXML_NOCDATA);
        $data = $xml->children('imo', true);
        var_dump($data->anbieter->immobilie);
    }
}