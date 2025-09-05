<?php

function upload_worker($upload, $path, $type, $timestamp = true, $enable_filetype = [])
{
    $max_prefered_size = 1024;
    if ($type == "bg") {
        $max_prefered_size = 1900;
    } elseif ($type == "logo") {
        $max_prefered_size = 400;
    }

    if ($path == RESOURCES_FOLDER) {
        $path = dirname(dirname(dirname(__FILE__))) . '/resources/';
    }
    $disallowed_filetypes = array(".php", ".phtml", ".php3", ".php4", ".js", ".shtml");

    if (!empty($upload['error']) && $upload['error'] != '8') {
        $upload['error_code'] = $upload['error'];
        switch ($upload['error']) {
            case '1':
                $upload['error'] = 'El archivo cargado supera la directiva TAMAÑO MÁXIMO PERMITIDO (UPLOAD_MAX_FILESIZE) de ' . ini_get("upload_max_filesize");
            break;
            case '2':
                $upload['error'] = 'El archivo cargado supera la directiva TAMAÑO MÁXIMO DE ARCHIVO (MAX_FILE_SIZE) que se especificó en el formulario HTML';
                break;
            case '3':
                $upload['error'] = 'El archivo cargado solo se cargó parcialmente';
                break;
            case '4':
                $upload['error'] = 'Ningun archivo fue subido';
                break;
            case '6':
                $upload['error'] = 'Falta una carpeta temporal';
                break;
            case '7':
                $upload['error'] = 'Error al escribir el archivo en el disco';
                break;
            case '999':
            default:
                $upload['error'] = 'No hay código de error disponible';
        }
        return $upload;
    }

    if ((isset($upload['error']) && $upload['error'] == 0) || (!empty($upload['tmp_name']) && $upload['tmp_name'] != 'none')
    ) {

        if (is_uploaded_file($upload['tmp_name'])) {

            /* Action Move */
            $filename = strtolower($upload['name']);
            $ext = strtolower(substr($filename, strpos($filename, '.'), strlen($filename) - 1));
            $filename = htmlentities($filename);
            $filename = preg_replace('/\&(.)[^;]*;/', '\\1', $filename);
            $name = str_replace($ext, '', ($timestamp ? time() : $filename));

            if (in_array($ext, $disallowed_filetypes)) {
                $upload['error'] = 'File upload stopped by extension';
                return $upload;
            }
            if (!empty($enable_filetype) && !in_array(substr($ext, 1), $enable_filetype)){
              $upload['error'] = 'File upload stopped by extension';
                return $upload;
            }

            $new_filename = $name . $ext;
            if (file_exists($path . $name . $ext)) {
                $i = 1;
                while (file_exists($path . $name . $i . $ext)) {
                    $i++;
                }
                $new_filename = $name . $i . $ext;
            }


            if (move_uploaded_file($upload['tmp_name'], $path . $new_filename)) {
                $size = getimagesize($path . $new_filename);
                if ($size) {
                    if ($size['0'] > $max_prefered_size || $size['1'] > $max_prefered_size) {
                        $config['image_library'] = 'gd2';
                        $config['source_image'] = $path . $new_filename;
                        $config['maintain_ratio'] = TRUE;
                        $config['width'] = $max_prefered_size;
                        $config['height'] = $max_prefered_size;
                        // get_instance()->load->library('image_lib', $config);
                        get_instance()->load->library('image_lib');
                        get_instance()->image_lib->initialize($config);
                        get_instance()->image_lib->clear();
                        if (!get_instance()->image_lib->resize()) {
                            $upload['error'] = get_instance()->image_lib->display_errors();
                        }
                    }
                }

                $upload['filename'] = $new_filename;
            } else {
                $upload['error'] = 'Error';
            }
        }
    }

    return $upload;
}
