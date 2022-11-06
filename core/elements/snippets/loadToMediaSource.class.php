<?php
class loadToSelectel{
    /* @var modX $modx */
    public $modx;

    function __construct(modX &$modx)
    {
        $this->modx =& $modx;

    }

    /**
     * @param string $fieldname
     * @param int $filesource
     * @param string $internalPath
     * @return array
     */
    public function uploadFiles($fieldname, $filesource, $internalPath = '/{year}/{month}/{day}/', $ctx = 'web') {
        $files = $this->getRequestFiles($fieldname);
        if(count($files) == 0) {
            return false;
        }

        if (!$this->getMediaSource($filesource, $ctx)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[loadToSelectel] Failed to get media source object while uploading files');
            return false;
        }
        if (!$this->mediaSource->checkPolicy('create')) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[loadToSelectel] No permission to upload files to a specified source.');
            return false;
        }

        // build path
        $internalPath = $this->preparePath($internalPath);
        $path = $this->mediaSource->getBasePath() . $internalPath;

        // Create directory if doesn't exists
        if (!$this->mediaSource->createContainer($internalPath, '')) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[loadToSelectel] Can`t create directory: '.$path);
            return false;
        }

        // save files
        $filesInfo = array();
        $count = 0;
        $size = 0;
        foreach($files as $file) {
            $originalFilename = $file['name'];
            $ext = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));

            $filename = $this->modx->filterPathSegment($this->generateRandomName()).".".$ext;

            $uploadResult = $this->mediaSource->uploadObjectsToContainer(
                $internalPath,
                array(
                    array_merge($file, array('name' => $filename))
                )
            );

            if($uploadResult) {
                $filesInfo[] = array(
                    'name' => $filename,
                    'name_original' => $originalFilename,
                    'path' => $internalPath.$filename,
                    'url' => $this->mediaSource->getObjectUrl($internalPath.$filename),
                    'size' => $file['size'],
                    'extension' => $ext
                );
                $count++;
                $size += $file['size'];
            }
            else {
                $errorMessage = '';
                $errors = $this->mediaSource->getErrors();
                foreach($errors as $k => $msg) {
                    $errorMessage .= $k.': '.$msg.'. ';
                }
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[loadToSelectel] Can`t upload user file: '.$errorMessage);
            }
        }
        return $filesInfo;
    }

    /**
     * @param $context
     * @param $filesource
     * @return modMediaSource|boolean
     */
    private function getMediaSource($filesource, $context) {
        if (empty($this->mediaSource)) {
            $this->modx->loadClass('sources.modMediaSource');

            $this->mediaSource = modMediaSource::getDefaultSource($this->modx, $filesource);

            $this->mediaSource->set('ctx', $context);
            $this->mediaSource->initialize();
        }

        return $this->mediaSource;
    }

    /**
     * Генерирует случайное имя для файла
     * @param int $length
     * @return string
     */
    private function generateRandomName($length = 16) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz_';
        $charactersLength = strlen($characters);

        $result = '';

        for ($i = 0; $i < $length; $i++)
            $result .= $characters[rand(0, $charactersLength - 1)];

        return $result;
    }

    private function preparePath($path) {
        $search = array('{year}','{month}','{day}');
        $replace = array(date('Y'),date('m'),date('d'));

        return str_replace($search, $replace, $path);
    }

    private function getRequestFiles($var) {
        $result = array();
        if(isset($_FILES[$var]) && isset($_FILES[$var]['name']))
        {
            if(is_array($_FILES[$var]['name'])) {

                $count = count($_FILES[$var]['name']);
                $keys = array_keys($_FILES[$var]);

                for ($i = 0; $i < $count; $i++) {
                    foreach ($keys as $key) {
                        if($_FILES[$var]['error'][$i] == UPLOAD_ERR_OK){
                            $result[$i][$key] = $_FILES[$var][$key][$i];
                        }
                    }
                }
            }
            else {
                if($_FILES[$var]['error'] == UPLOAD_ERR_OK){
                    return $_FILES['files'];
                }
            }
        }
        return $result;
    }
}