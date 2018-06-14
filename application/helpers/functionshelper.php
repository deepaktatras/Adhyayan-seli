<?php
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
if (!function_exists('array_column')) {

    //array_column function in php 5.4 does not exist, so use this instead
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if (!isset($value[$columnKey])) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            } else {
                if (!isset($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if (!is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }

}

function resizeImage($filename, $newwidth, $newheight) {
    list($width, $height, $type) = getimagesize($filename);
    if ($newheight < $height || $newwidth < $width) {
        $newwidthX = $newwidth;
        $newheightX = ($height / $width) * $newwidthX;
        if ($newheightX > $newheight) {
            $newheight = $newheight;
            $newwidth = ($newwidthX / $newheightX) * $newheight;
        } else {
            $newheight = $newheightX;
            $newwidth = $newwidthX;
        }
        $thumb = imagecreatetruecolor($newwidth, $newheight);
        if ($type == IMAGETYPE_JPEG) {  //if image is jpeg type
            $source = imagecreatefromjpeg($filename);
        } elseif ($type == IMAGETYPE_GIF) {  // if image is gif type
            $source = imagecreatefromgif($filename);
        } elseif ($type == IMAGETYPE_PNG) {  //if image is png type
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
            $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
            imagefilledrectangle($thumb, 0, 0, $newwidth, $newheight, $transparent);
            $source = imagecreatefrompng($filename);
        }
        //$source = imagecreatefromjpeg($filename);
        imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        if ($type == IMAGETYPE_JPEG) {
            return imagejpeg($thumb, $filename, 100);
        } elseif ($type == IMAGETYPE_GIF) {  // if image is gif type
            return imagegif($thumb, $filename);
        } elseif ($type == IMAGETYPE_PNG) {  //if image is png type
            return imagepng($thumb, $filename,0);
        }
    }
}

/**
     * Write documents
     *
     * @param \PhpOffice\PhpWord\PhpWord $phpWord
     * @param string $filename
     * @param array $writers
     *
     * @return string
     */
    function write($phpWord, $filename, $writers) {
        //$result = '';

        // Write documents
        foreach ($writers as $format => $extension) {
            //$result .= date('H:i:s') . " Write to {$format} format";
            if (null !== $extension) {
                $targetFile = ROOT . "".DOWNLOAD_DIAGNOSTIC."{$filename}.{$extension}";
                $phpWord->save($targetFile, $format);
            } else {
                //$result .= ' ... NOT DONE!';
            }
            //$result .= EOL;
        }

        //$result .= getEndingNotes($writers);

        //return $result;
    }

    /**
     * Get ending notes
     *
     * @param array $writers
     *
     * @return string
     */
    function getEndingNotes($writers) {
        $result = '';

        // Do not show execution time for index
        if (!IS_INDEX) {
            $result .= date('H:i:s') . " Done writing file(s)" . EOL;
            $result .= date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB" . EOL;
        }

        // Return
        if (CLI) {
            $result .= 'The results are stored in the "results" subdirectory.' . EOL;
        } else {
            if (!IS_INDEX) {
                $types = array_values($writers);
                $result .= '<p>&nbsp;</p>';
                $result .= '<p>Results: ';
                foreach ($types as $type) {
                    if (!is_null($type)) {
                        $resultFile = 'results/' . SCRIPT_FILENAME . '.' . $type;
                        if (file_exists($resultFile)) {
                            $result .= "<a href='{$resultFile}' class='btn btn-primary'>{$type}</a> ";
                        }
                    }
                }
                $result .= '</p>';
            }
        }

        return $result;
    }
   
    
   function getMimeType( $filename ) {
        $realpath = realpath( $filename );
        if ( $realpath
                && function_exists( 'finfo_file' )
                && function_exists( 'finfo_open' )
                && defined( 'FILEINFO_MIME_TYPE' )
        ) {
                // Use the Fileinfo PECL extension (PHP 5.3+)
                return finfo_file( finfo_open( FILEINFO_MIME_TYPE ), $realpath );
        }
        
        if ( function_exists( 'mime_content_type' ) ) {
                // Deprecated in PHP 5.3
                return mime_content_type( $realpath );
        }
        
        return false;
}
    
    
     function getMimeType2( $filename ) {
    
       
        $path_parts = @pathinfo($filename);
        $myfileextension = isset($path_parts["extension"])?$path_parts["extension"]:'';

        switch($myfileextension)
        {
          
            ///Image Mime Types
            case 'jpg':
            $mimetype = "image/jpg";
            break;
            case 'jpeg':
            $mimetype = "image/jpeg";
            break;
            case 'gif':
            $mimetype = "image/gif";
            break;
            case 'png':
            $mimetype = "image/png";
            break;
            case 'bm':
            $mimetype = "image/bmp";
            break;
            case 'bmp':
            $mimetype = "image/bmp";
            break;
            case 'art':
            $mimetype = "image/x-jg";
            break;
            case 'dwg':
            $mimetype = "image/x-dwg";
            break;
            case 'dxf':
            $mimetype = "image/x-dwg";
            break;
            case 'flo':
            $mimetype = "image/florian";
            break;
            case 'fpx':
            $mimetype = "image/vnd.fpx";
            break;
            case 'g3':
            $mimetype = "image/g3fax";
            break;
            case 'ief':
            $mimetype = "image/ief";
            break;
            case 'jfif':
            $mimetype = "image/pjpeg";
            break;
            case 'jfif-tbnl':
            $mimetype = "image/jpeg";
            break;
            case 'jpe':
            $mimetype = "image/pjpeg";
            break;
            case 'jps':
            $mimetype = "image/x-jps";
            break;
            case 'jut':
            $mimetype = "image/jutvision";
            break;
            case 'mcf':
            $mimetype = "image/vasa";
            break;
            case 'nap':
            $mimetype = "image/naplps";
            break;
            case 'naplps':
            $mimetype = "image/naplps";
            break;
            case 'nif':
            $mimetype = "image/x-niff";
            break;
            case 'niff':
            $mimetype = "image/x-niff";
            break;
            case 'cod':
            $mimetype = "image/cis-cod";
            break;
            case 'ief':
            $mimetype = "image/ief";
            break;
            case 'svg':
            $mimetype = "image/svg+xml";
            break;
            case 'tif':
            $mimetype = "image/tiff";
            break;
            case 'tiff':
            $mimetype = "image/tiff";
            break;
            case 'ras':
            $mimetype = "image/x-cmu-raster";
            break;
            case 'cmx':
            $mimetype = "image/x-cmx";
            break;
            case 'ico':
            $mimetype = "image/x-icon";
            break;
            case 'pnm':
            $mimetype = "image/x-portable-anymap";
            break;
            case 'pbm':
            $mimetype = "image/x-portable-bitmap";
            break;
            case 'pgm':
            $mimetype = "image/x-portable-graymap";
            break;
            case 'ppm':
            $mimetype = "image/x-portable-pixmap";
            break;
            case 'rgb':
            $mimetype = "image/x-rgb";
            break;
            case 'xbm':
            $mimetype = "image/x-xbitmap";
            break;
            case 'xpm':
            $mimetype = "image/x-xpixmap";
            break;
            case 'xwd':
            $mimetype = "image/x-xwindowdump";
            break;
            case 'rgb':
            $mimetype = "image/x-rgb";
            break;
            case 'xbm':
            $mimetype = "image/x-xbitmap";
            break;
            case "wbmp":
            $mimetype = "image/vnd.wap.wbmp";
            break;
          
            //Files MIME Types
            
            case 'css':
            $mimetype = "text/css";
            break;
            case 'htm':
            $mimetype = "text/html";
            break;
            case 'html':
            $mimetype = "text/html";
            break;
            case 'stm':
            $mimetype = "text/html";
            break;
            case 'c':
            $mimetype = "text/plain";
            break;
            case 'h':
            $mimetype = "text/plain";
            break;
            case 'txt':
            $mimetype = "text/plain";
            break;
            case 'rtx':
            $mimetype = "text/richtext";
            break;
            case 'htc':
            $mimetype = "text/x-component";
            break;
            case 'vcf':
            $mimetype = "text/x-vcard";
            break;
           
           
            //Applications MIME Types
            
            case 'doc':
            $mimetype = "application/msword";
            break;
            case 'xls':
            $mimetype = "application/vnd.ms-excel";
            break;
            case 'ppt':
            $mimetype = "application/vnd.ms-powerpoint";
            break;
            case 'pps':
            $mimetype = "application/vnd.ms-powerpoint";
            break;
            case 'pot':
            $mimetype = "application/vnd.ms-powerpoint";
            break;
          
            case "ogg":
            $mimetype = "application/ogg";
            break;
            case "pls":
            $mimetype = "application/pls+xml";
            break;
            case "asf":
            $mimetype = "application/vnd.ms-asf";
            break;
            case "wmlc":
            $mimetype = "application/vnd.wap.wmlc";
            break;
            case 'dot':
            $mimetype = "application/msword";
            break;
            case 'class':
            $mimetype = "application/octet-stream";
            break;
            case 'exe':
            $mimetype = "application/octet-stream";
            break;
            case 'pdf':
            $mimetype = "application/pdf";
            break;
            case 'rtf':
            $mimetype = "application/rtf";
            break;
            case 'xla':
            $mimetype = "application/vnd.ms-excel";
            break;
            case 'xlc':
            $mimetype = "application/vnd.ms-excel";
            break;
            case 'xlm':
            $mimetype = "application/vnd.ms-excel";
            break;
           
            case 'msg':
            $mimetype = "application/vnd.ms-outlook";
            break;
            case 'mpp':
            $mimetype = "application/vnd.ms-project";
            break;
            case 'cdf':
            $mimetype = "application/x-cdf";
            break;
            case 'tgz':
            $mimetype = "application/x-compressed";
            break;
            case 'dir':
            $mimetype = "application/x-director";
            break;
            case 'dvi':
            $mimetype = "application/x-dvi";
            break;
            case 'gz':
            $mimetype = "application/x-gzip";
            break;
            case 'js':
            $mimetype = "application/x-javascript";
            break;
            case 'mdb':
            $mimetype = "application/x-msaccess";
            break;
            case 'dll':
            $mimetype = "application/x-msdownload";
            break;
            case 'wri':
            $mimetype = "application/x-mswrite";
            break;
            case 'cdf':
            $mimetype = "application/x-netcdf";
            break;
            case 'swf':
            $mimetype = "application/x-shockwave-flash";
            break;
            case 'tar':
            $mimetype = "application/x-tar";
            break;
            case 'man':
            $mimetype = "application/x-troff-man";
            break;
            case 'zip':
            $mimetype = "application/zip";
            break;
            case 'xlsx':
            $mimetype = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
            break;
            case 'pptx':
            $mimetype = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
            break;
            case 'docx':
            $mimetype = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
            break;
            case 'xltx':
            $mimetype = "application/vnd.openxmlformats-officedocument.spreadsheetml.template";
            break;
            case 'potx':
            $mimetype = "application/vnd.openxmlformats-officedocument.presentationml.template";
            break;
            case 'ppsx':
            $mimetype = "application/vnd.openxmlformats-officedocument.presentationml.slideshow";
            break;
            case 'sldx':
            $mimetype = "application/vnd.openxmlformats-officedocument.presentationml.slide";
            break;
          
            ///Audio and Video Files
            
            case 'mp3':
            $mimetype = "audio/mpeg";
            break;
            case 'wav':
            $mimetype = "audio/x-wav";
            break;
            case 'au':
            $mimetype = "audio/basic";
            break;
            case 'snd':
            $mimetype = "audio/basic";
            break;
            case 'm3u':
            $mimetype = "audio/x-mpegurl";
            break;
            case 'ra':
            $mimetype = "audio/x-pn-realaudio";
            break;
            case 'mp2':
            $mimetype = "video/mpeg";
            break;
            case 'mov':
            $mimetype = "video/quicktime";
            break;
            case 'qt':
            $mimetype = "video/quicktime";
            break;
            case 'mp4':
            $mimetype = "video/mp4";
            break;
            case 'm4a':
            $mimetype = "audio/mp4";
            break;
            case 'mp4a':
            $mimetype = "audio/mp4";
            break;
            case 'm4p':
            $mimetype = "audio/mp4";
            break;
            case 'm3a':
            $mimetype = "audio/mpeg";
            break;
            case 'm2a':
            $mimetype = "audio/mpeg";
            break;
            case 'mp2a':
            $mimetype = "audio/mpeg";
            break;
            case 'mp2':
            $mimetype = "audio/mpeg";
            break;
            case 'mpga':
            $mimetype = "audio/mpeg";
            break;
            case '3gp':
            $mimetype = "video/3gpp";
            break;
            case '3g2':
            $mimetype = "video/3gpp2";
            break;
            case 'mp4v':
            $mimetype = "video/mp4";
            break;
            case 'mpg4':
            $mimetype = "video/mp4";
            break;
            case 'm2v':
            $mimetype = "video/mpeg";
            break;
            case 'm1v':
            $mimetype = "video/mpeg";
            break;
            case 'mpe':
            $mimetype = "video/mpeg";
            break;
            case 'avi':
            $mimetype = "video/x-msvideo";
            break;
            case 'midi':
            $mimetype = "audio/midi";
            break;
            case 'mid':
            $mimetype = "audio/mid";
            break;
            case 'amr':
            $mimetype = "audio/amr";
            break;
            
            
            default:
            $mimetype = "";
        
            
        }
        
        return $mimetype;
}

function getMimeType3( $filename ) {
    
    $myfileextension=strtolower(array_pop(explode('.',$fname)));
            
        switch($myfileextension)
        {
          
            ///Image Mime Types
            case 'jpg':
            $mimetype = "image/jpg";
            break;
            case 'jpeg':
            $mimetype = "image/jpeg";
            break;
            case 'gif':
            $mimetype = "image/gif";
            break;
            case 'png':
            $mimetype = "image/png";
            break;
            case 'bm':
            $mimetype = "image/bmp";
            break;
            case 'bmp':
            $mimetype = "image/bmp";
            break;
            case 'art':
            $mimetype = "image/x-jg";
            break;
            case 'dwg':
            $mimetype = "image/x-dwg";
            break;
            case 'dxf':
            $mimetype = "image/x-dwg";
            break;
            case 'flo':
            $mimetype = "image/florian";
            break;
            case 'fpx':
            $mimetype = "image/vnd.fpx";
            break;
            case 'g3':
            $mimetype = "image/g3fax";
            break;
            case 'ief':
            $mimetype = "image/ief";
            break;
            case 'jfif':
            $mimetype = "image/pjpeg";
            break;
            case 'jfif-tbnl':
            $mimetype = "image/jpeg";
            break;
            case 'jpe':
            $mimetype = "image/pjpeg";
            break;
            case 'jps':
            $mimetype = "image/x-jps";
            break;
            case 'jut':
            $mimetype = "image/jutvision";
            break;
            case 'mcf':
            $mimetype = "image/vasa";
            break;
            case 'nap':
            $mimetype = "image/naplps";
            break;
            case 'naplps':
            $mimetype = "image/naplps";
            break;
            case 'nif':
            $mimetype = "image/x-niff";
            break;
            case 'niff':
            $mimetype = "image/x-niff";
            break;
            case 'cod':
            $mimetype = "image/cis-cod";
            break;
            case 'ief':
            $mimetype = "image/ief";
            break;
            case 'svg':
            $mimetype = "image/svg+xml";
            break;
            case 'tif':
            $mimetype = "image/tiff";
            break;
            case 'tiff':
            $mimetype = "image/tiff";
            break;
            case 'ras':
            $mimetype = "image/x-cmu-raster";
            break;
            case 'cmx':
            $mimetype = "image/x-cmx";
            break;
            case 'ico':
            $mimetype = "image/x-icon";
            break;
            case 'pnm':
            $mimetype = "image/x-portable-anymap";
            break;
            case 'pbm':
            $mimetype = "image/x-portable-bitmap";
            break;
            case 'pgm':
            $mimetype = "image/x-portable-graymap";
            break;
            case 'ppm':
            $mimetype = "image/x-portable-pixmap";
            break;
            case 'rgb':
            $mimetype = "image/x-rgb";
            break;
            case 'xbm':
            $mimetype = "image/x-xbitmap";
            break;
            case 'xpm':
            $mimetype = "image/x-xpixmap";
            break;
            case 'xwd':
            $mimetype = "image/x-xwindowdump";
            break;
            case 'rgb':
            $mimetype = "image/x-rgb";
            break;
            case 'xbm':
            $mimetype = "image/x-xbitmap";
            break;
            case "wbmp":
            $mimetype = "image/vnd.wap.wbmp";
            break;
          
            //Files MIME Types
            
            case 'css':
            $mimetype = "text/css";
            break;
            case 'htm':
            $mimetype = "text/html";
            break;
            case 'html':
            $mimetype = "text/html";
            break;
            case 'stm':
            $mimetype = "text/html";
            break;
            case 'c':
            $mimetype = "text/plain";
            break;
            case 'h':
            $mimetype = "text/plain";
            break;
            case 'txt':
            $mimetype = "text/plain";
            break;
            case 'rtx':
            $mimetype = "text/richtext";
            break;
            case 'htc':
            $mimetype = "text/x-component";
            break;
            case 'vcf':
            $mimetype = "text/x-vcard";
            break;
           
           
            //Applications MIME Types
            
            case 'doc':
            $mimetype = "application/msword";
            break;
            case 'xls':
            $mimetype = "application/vnd.ms-excel";
            break;
            case 'ppt':
            $mimetype = "application/vnd.ms-powerpoint";
            break;
            case 'pps':
            $mimetype = "application/vnd.ms-powerpoint";
            break;
            case 'pot':
            $mimetype = "application/vnd.ms-powerpoint";
            break;
          
            case "ogg":
            $mimetype = "application/ogg";
            break;
            case "pls":
            $mimetype = "application/pls+xml";
            break;
            case "asf":
            $mimetype = "application/vnd.ms-asf";
            break;
            case "wmlc":
            $mimetype = "application/vnd.wap.wmlc";
            break;
            case 'dot':
            $mimetype = "application/msword";
            break;
            case 'class':
            $mimetype = "application/octet-stream";
            break;
            case 'exe':
            $mimetype = "application/octet-stream";
            break;
            case 'pdf':
            $mimetype = "application/pdf";
            break;
            case 'rtf':
            $mimetype = "application/rtf";
            break;
            case 'xla':
            $mimetype = "application/vnd.ms-excel";
            break;
            case 'xlc':
            $mimetype = "application/vnd.ms-excel";
            break;
            case 'xlm':
            $mimetype = "application/vnd.ms-excel";
            break;
           
            case 'msg':
            $mimetype = "application/vnd.ms-outlook";
            break;
            case 'mpp':
            $mimetype = "application/vnd.ms-project";
            break;
            case 'cdf':
            $mimetype = "application/x-cdf";
            break;
            case 'tgz':
            $mimetype = "application/x-compressed";
            break;
            case 'dir':
            $mimetype = "application/x-director";
            break;
            case 'dvi':
            $mimetype = "application/x-dvi";
            break;
            case 'gz':
            $mimetype = "application/x-gzip";
            break;
            case 'js':
            $mimetype = "application/x-javascript";
            break;
            case 'mdb':
            $mimetype = "application/x-msaccess";
            break;
            case 'dll':
            $mimetype = "application/x-msdownload";
            break;
            case 'wri':
            $mimetype = "application/x-mswrite";
            break;
            case 'cdf':
            $mimetype = "application/x-netcdf";
            break;
            case 'swf':
            $mimetype = "application/x-shockwave-flash";
            break;
            case 'tar':
            $mimetype = "application/x-tar";
            break;
            case 'man':
            $mimetype = "application/x-troff-man";
            break;
            case 'zip':
            $mimetype = "application/zip";
            break;
            case 'xlsx':
            $mimetype = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
            break;
            case 'pptx':
            $mimetype = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
            break;
            case 'docx':
            $mimetype = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
            break;
            case 'xltx':
            $mimetype = "application/vnd.openxmlformats-officedocument.spreadsheetml.template";
            break;
            case 'potx':
            $mimetype = "application/vnd.openxmlformats-officedocument.presentationml.template";
            break;
            case 'ppsx':
            $mimetype = "application/vnd.openxmlformats-officedocument.presentationml.slideshow";
            break;
            case 'sldx':
            $mimetype = "application/vnd.openxmlformats-officedocument.presentationml.slide";
            break;
          
            ///Audio and Video Files
            
            case 'mp3':
            $mimetype = "audio/mpeg";
            break;
            case 'wav':
            $mimetype = "audio/x-wav";
            break;
            case 'au':
            $mimetype = "audio/basic";
            break;
            case 'snd':
            $mimetype = "audio/basic";
            break;
            case 'm3u':
            $mimetype = "audio/x-mpegurl";
            break;
            case 'ra':
            $mimetype = "audio/x-pn-realaudio";
            break;
            case 'mp2':
            $mimetype = "video/mpeg";
            break;
            case 'mov':
            $mimetype = "video/quicktime";
            break;
            case 'qt':
            $mimetype = "video/quicktime";
            break;
            case 'mp4':
            $mimetype = "video/mp4";
            break;
            case 'm4a':
            $mimetype = "audio/mp4";
            break;
            case 'mp4a':
            $mimetype = "audio/mp4";
            break;
            case 'm4p':
            $mimetype = "audio/mp4";
            break;
            case 'm3a':
            $mimetype = "audio/mpeg";
            break;
            case 'm2a':
            $mimetype = "audio/mpeg";
            break;
            case 'mp2a':
            $mimetype = "audio/mpeg";
            break;
            case 'mp2':
            $mimetype = "audio/mpeg";
            break;
            case 'mpga':
            $mimetype = "audio/mpeg";
            break;
            case '3gp':
            $mimetype = "video/3gpp";
            break;
            case '3g2':
            $mimetype = "video/3gpp2";
            break;
            case 'mp4v':
            $mimetype = "video/mp4";
            break;
            case 'mpg4':
            $mimetype = "video/mp4";
            break;
            case 'm2v':
            $mimetype = "video/mpeg";
            break;
            case 'm1v':
            $mimetype = "video/mpeg";
            break;
            case 'mpe':
            $mimetype = "video/mpeg";
            break;
            case 'avi':
            $mimetype = "video/x-msvideo";
            break;
            case 'midi':
            $mimetype = "audio/midi";
            break;
            case 'mid':
            $mimetype = "audio/mid";
            break;
            case 'amr':
            $mimetype = "audio/amr";
            break;
            
            
            default:
            $mimetype = "";
        
            
        }
        
        return $mimetype;
}
    
    function upload_file($pathtoupload, $file_path = "",$resize=false,$newwidth=0,$newheight=0) {
        
    if (empty($file_path)) {
        $file_path = $_FILES ['file'] ['tmp_name'];
    }
    
    if($resize){
    resizeImage($file_path,$newwidth,$newheight);
    }
    /* if (@copy ( $file_path, $pathtoupload )) {
      return true;
      }else{
      return false;
      } */
    $mime=getMimeType($file_path);
    
    if(!$mime){
     //$mime="";
     $mime=getMimeType2($file_path);  
    }
    
    if(empty($mime)){
      $mime=getMimeType2($pathtoupload);   
    }
    
    $s3 = S3Client::factory(array(
                'version' => 'latest',
                'region' => 'ap-south-1',
                'credentials' => array(
                    'key' => AWS_KEY,
                    'secret' => AWS_SECRET
                )
    ));

    try {

// Upload a file.
        if(!empty($mime)){
        $result = $s3->putObject(array(
            'Bucket' => AWS_BUCKET,
            'Key' => $pathtoupload,
            'SourceFile' => $file_path,
            'ContentType' => $mime,
            'ACL'        => 'public-read'
        ));
        
        }else{
            
         $result = $s3->putObject(array(
            'Bucket' => AWS_BUCKET,
            'Key' => $pathtoupload,
            'SourceFile' => $file_path,
            'ACL'        => 'public-read'
        ));   
            
        }
        //echo $result['ObjectURL'];
        return true;
    } catch (S3Exception $e) {
        //echo $e->getMessage() . "\n";
        return false;
    }
}


function deleteFile($file){
    
    $s3 = S3Client::factory(array(
                'version' => 'latest',
                'region' => 'ap-south-1',
                'credentials' => array(
                    'key' => AWS_KEY,
                    'secret' => AWS_SECRET
                )
    ));

    try {

// Delete a file.
        $result = $s3->deleteObject(array(
            'Bucket' => AWS_BUCKET,
            'Key' => $file
        ));
        //echo"dada";    
        //print_r($result);
        return true;
    } catch (S3Exception $e) {
        //echo $e->getMessage() . "\n";
        return false;
    }
    
}

function detailsFile($file){
    
    $s3 = S3Client::factory(array(
                'version' => 'latest',
                'region' => 'ap-south-1',
                'credentials' => array(
                    'key' => AWS_KEY,
                    'secret' => AWS_SECRET
                )
    ));

    try {

// Detail of file.
        $result = $s3->doesObjectExist(AWS_BUCKET,$file);
          
        //echo"dada";    
       return $result;
        //return true;
        
    } catch (S3Exception $e) {
        //echo $e->getMessage() . "\n";
        return false;
    }
    
}

function sendEmail($fromEmail,$fromName,$toEmail,$toName,$ccEmail,$ccName,$subject,$body,$attachmentPath=array(),$inlineImage=array(),$file_path=0){
        require_once ROOT . 'library' . DS . 'phpmailer' . DS . "PHPMailerAutoload" . '.php';
			
			// Create a new PHPMailer instance
			
			$mail = new PHPMailer ();
			
			// Tell PHPMailer to use SMTP
			
			$mail->isSMTP ();
			
			// Enable SMTP debugging
			
			// 0 = off (for production use)
			
			// 1 = client messages
			
			// 2 = client and server messages
			
			$mail->SMTPDebug = 0;
			
			// Ask for HTML-friendly debug output
			
			$mail->Debugoutput = 'html';
                        
                        $smtpType="adhyayan";
                        $smtpDetails=array(
                            "gmail"=>array(
                                "host"=>"smtp.gmail.com",
                                "Port"=>"465",
                                "SMTPAuth"=>true,
                                "SMTPSecure"=>"ssl",
                                "Username"=>"developer@tatrasdata.com",
                                "Password"=>"tatrasdata"
                            ),
                            
                            "adhyayan"=>array(
                                "host"=>"gator3172.hostgator.com",
                                "Port"=>"465",
                                "SMTPAuth"=>true,
                                "SMTPSecure"=>"ssl",
                                "Username"=>"info@adhyayan.asia",
                                "Password"=>"!qaz@wsx1234"
                            )
                        );
                        
			$authDetails=$smtpDetails[$smtpType];
			// Set the hostname of the mail server
			$mail->Host = $authDetails['host'];
			// Set the SMTP port number - likely to be 25, 465 or 587
			$mail->Port = $authDetails['Port'];
			// Whether to use SMTP authentication
			$mail->SMTPAuth = $authDetails['SMTPAuth'];
			// Username to use for SMTP authentication
			$mail->SMTPSecure = $authDetails['SMTPSecure'];
			$mail->Username = $authDetails['Username'];
			// Password to use for SMTP authentication
			$mail->Password = $authDetails['Password'];
                        // $mail->setFrom ( $fromEmail, $fromName );
                        $mail->From = $fromEmail;
                        $mail->FromName = $fromName;
			
			// Set an alternative reply-to address
			//echo STAGING_ENVIRONMENT;
                        if(STAGING_ENVIRONMENT==true){
                            
                            $ccEmail = 'deepakchauhan89@gmail.com,pratibha@tatrasdata.com';
                           // $ccEmail = 'deepakchauhan89@gmail.com,pooja.s@tatrasdata.com,vikas@tatrasdata.com,upma@tatrasdata.com';
                            $toEmail = 'deepak.t@tatrasdata.com';
                        }
                        $allCC = explode(",", $ccEmail);
                       // print_r($allCC);die;
                        foreach($allCC as $key=>$val) {
                            $mail->AddCC($val,$val);
                        }
                       // echo $ccEmail;die;
                           //$toEmail = 'deepak.t@tatrasdata.com';
                           // $ccEmail = 'deepakchauhan89@gmail.com,umraovikas@gmail.com';
                       
			$mail->addReplyTo ( $fromEmail, $fromName );			
			// Set who the message is to be sent to			
			$mail->addAddress ( $toEmail, $toName );
                        //$mail->addAddress ( 'nisha@tatrasdata.com', $toName );

			//$ccEmail!=''?$mail->AddCC($ccEmail,$fromName):'';
                        
                        if(DEVELOPMENT_ENVIRONMENT==true){
			//$ccEmail!=''?$mail->AddCC($ccEmail,$fromName):'';
                        //$mail->AddBCC ( "upma@tatrasdata.com", $toName );    
			//$mail->AddBCC ( "vikas@tatrasdata.com", $toName );
                       // $mail->AddBCC ( "pooja.s@tatrasdata.com", $toName );
                        }
                        
			// Set the subject line			
			$mail->Subject = $subject;
			
			// Read an HTML message body from an external file, convert referenced images to embedded,
			
			// convert HTML into a basic plain-text alternative body
			
			//$mail->AltBody = $message;
			
			$mail->msgHTML ( $body );
			
			// Attach an image file
			if(!empty($attachmentPath)) {
                            
                            foreach($attachmentPath as $key=>$val){
                               // readfile(ROOT . 'cron' . DS . $val);
                                if($file_path == 1){
                                    $mail->addAttachment($val);
                                    unset($val);
                                }else{
                                $mail->addAttachment(ROOT . 'cron' . DS . $val);
                                }
                                //echo $val;
                            }
                        }
                        
                        if(!empty($inlineImage)) {
                        foreach($inlineImage as $key=>$val){
                        $mail->AddEmbeddedImage(ROOT . 'cron' . DS . $val, $key);
                        }
                        }
			//die;
			// send the message, check for errors
			//print $mail->ErrorInfo;
                        //! $mail->send ()
			if (! $mail->send ())                             
				return 0;				
			else 
                            return 1;
    }
    
    function ChangeFormat($date,$format="d-m-Y",$return="-"){
        if(!empty($date) && $date!='0000-00-00'){
        $date = new DateTime($date);
        return $date->format($format);
        }else{
        return $return;    
        }
        
    }
    
    
    function RemoveSpaces($url){
     
	  $url = preg_replace('/\s+/', '-', trim($url));
	  $url = str_replace("         ","-",$url);
	  $url = str_replace("        ","-",$url);
	  $url = str_replace("       ","-",$url);
	  $url = str_replace("      ","-",$url);
	  $url = str_replace("     ","-",$url);
	  $url = str_replace("    ","-",$url);
	  $url = str_replace("   ","-",$url);
	  $url = str_replace("  ","-",$url);
	  $url = str_replace(" ","-",$url);
	
     return $url;
     
}

function RemoveUrlSpaces($url){

        $url = preg_replace('/\s+/', '%20', trim($url));  
        $url = str_replace("         ","%20",$url);
        $url = str_replace("        ","%20",$url);
        $url = str_replace("       ","%20",$url);
        $url = str_replace("      ","%20",$url);
        $url = str_replace("     ","%20",$url);
        $url = str_replace("    ","%20",$url);
	    $url = str_replace("   ","%20",$url);
	    $url = str_replace("  ","%20",$url);
	    $url = str_replace(" ","%20",$url);
		
        return $url;
     
}
function DownloadAnything($file, $newfilename = '', $mimetype='', $isremotefile = false){
         
        $formattedhpath = "";
        $filesize = "";

        if(empty($file)){
           die('Please enter file url to download...!');
           exit;
        }
     
         //Removing spaces and replacing with %20 ascii code
         $file = RemoveUrlSpaces($file);
          
          
        if(preg_match("#http://#", $file) || preg_match("#https://#", $file)){
          $formattedhpath = "url";
        }else{
          $formattedhpath = "filepath";
        }
        
        if($formattedhpath == "url"){

          $file_headers = @get_headers($file);
  
          if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
           die('File is not readable or not found...!');
           exit;
          }
          
        }elseif($formattedhpath == "filepath"){

          if(@is_readable($file)) {
               die('File is not readable or not found...!');
               exit;
          }
        }
        
        
       //Fetching File Size Located in Remote Server
       if($isremotefile && $formattedhpath == "url"){
          
          
          $data = @get_headers($file, true);
          
          if(!empty($data['Content-Length'])){
          $filesize = (int)$data["Content-Length"];
          
          }else{
               
               ///If get_headers fails then try to fetch filesize with curl
               $ch = @curl_init();

               if(!@curl_setopt($ch, CURLOPT_URL, $file)) {
                 @curl_close($ch);
                 @exit;
               }
               
               @curl_setopt($ch, CURLOPT_NOBODY, true);
               @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
               @curl_setopt($ch, CURLOPT_HEADER, true);
               @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
               @curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
               @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
               @curl_exec($ch);
               
               if(!@curl_errno($ch))
               {
                    
                    $http_status = (int)@curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    if($http_status >= 200  && $http_status <= 300)
                    $filesize = (int)@curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
               
               }
               @curl_close($ch);
               
          }
          
       }elseif($isremotefile && $formattedhpath == "filepath"){
         
	   die('Error : Need complete URL of remote file...!');
           exit;
		   
       }else{
         
		   if($formattedhpath == "url"){
		   
			   $data = @get_headers($file, true);
			   $filesize = (int)$data["Content-Length"];
			   
		   }elseif($formattedhpath == "filepath"){
		   
		       $filesize = (int)@filesize($file);
			   
		   }
		   
       }
       
       if(empty($newfilename)){
          $newfilename =  @basename($file);
       }else{
          //Replacing any spaces with (-) hypen
          $newfilename = RemoveSpaces($newfilename);
       }
       
       if(empty($mimetype)){
          
       ///Get the extension of the file
       $path_parts = @pathinfo($file);
       $myfileextension = $path_parts["extension"];

        switch($myfileextension)
        {
          
            ///Image Mime Types
            case 'jpg':
            $mimetype = "image/jpg";
            break;
            case 'jpeg':
            $mimetype = "image/jpeg";
            break;
            case 'gif':
            $mimetype = "image/gif";
            break;
            case 'png':
            $mimetype = "image/png";
            break;
            case 'bm':
            $mimetype = "image/bmp";
            break;
            case 'bmp':
            $mimetype = "image/bmp";
            break;
            case 'art':
            $mimetype = "image/x-jg";
            break;
            case 'dwg':
            $mimetype = "image/x-dwg";
            break;
            case 'dxf':
            $mimetype = "image/x-dwg";
            break;
            case 'flo':
            $mimetype = "image/florian";
            break;
            case 'fpx':
            $mimetype = "image/vnd.fpx";
            break;
            case 'g3':
            $mimetype = "image/g3fax";
            break;
            case 'ief':
            $mimetype = "image/ief";
            break;
            case 'jfif':
            $mimetype = "image/pjpeg";
            break;
            case 'jfif-tbnl':
            $mimetype = "image/jpeg";
            break;
            case 'jpe':
            $mimetype = "image/pjpeg";
            break;
            case 'jps':
            $mimetype = "image/x-jps";
            break;
            case 'jut':
            $mimetype = "image/jutvision";
            break;
            case 'mcf':
            $mimetype = "image/vasa";
            break;
            case 'nap':
            $mimetype = "image/naplps";
            break;
            case 'naplps':
            $mimetype = "image/naplps";
            break;
            case 'nif':
            $mimetype = "image/x-niff";
            break;
            case 'niff':
            $mimetype = "image/x-niff";
            break;
            case 'cod':
            $mimetype = "image/cis-cod";
            break;
            case 'ief':
            $mimetype = "image/ief";
            break;
            case 'svg':
            $mimetype = "image/svg+xml";
            break;
            case 'tif':
            $mimetype = "image/tiff";
            break;
            case 'tiff':
            $mimetype = "image/tiff";
            break;
            case 'ras':
            $mimetype = "image/x-cmu-raster";
            break;
            case 'cmx':
            $mimetype = "image/x-cmx";
            break;
            case 'ico':
            $mimetype = "image/x-icon";
            break;
            case 'pnm':
            $mimetype = "image/x-portable-anymap";
            break;
            case 'pbm':
            $mimetype = "image/x-portable-bitmap";
            break;
            case 'pgm':
            $mimetype = "image/x-portable-graymap";
            break;
            case 'ppm':
            $mimetype = "image/x-portable-pixmap";
            break;
            case 'rgb':
            $mimetype = "image/x-rgb";
            break;
            case 'xbm':
            $mimetype = "image/x-xbitmap";
            break;
            case 'xpm':
            $mimetype = "image/x-xpixmap";
            break;
            case 'xwd':
            $mimetype = "image/x-xwindowdump";
            break;
            case 'rgb':
            $mimetype = "image/x-rgb";
            break;
            case 'xbm':
            $mimetype = "image/x-xbitmap";
            break;
            case "wbmp":
            $mimetype = "image/vnd.wap.wbmp";
            break;
          
            //Files MIME Types
            
            case 'css':
            $mimetype = "text/css";
            break;
            case 'htm':
            $mimetype = "text/html";
            break;
            case 'html':
            $mimetype = "text/html";
            break;
            case 'stm':
            $mimetype = "text/html";
            break;
            case 'c':
            $mimetype = "text/plain";
            break;
            case 'h':
            $mimetype = "text/plain";
            break;
            case 'txt':
            $mimetype = "text/plain";
            break;
            case 'rtx':
            $mimetype = "text/richtext";
            break;
            case 'htc':
            $mimetype = "text/x-component";
            break;
            case 'vcf':
            $mimetype = "text/x-vcard";
            break;
           
           
            //Applications MIME Types
            
            case 'doc':
            $mimetype = "application/msword";
            break;
            case 'xls':
            $mimetype = "application/vnd.ms-excel";
            break;
            case 'ppt':
            $mimetype = "application/vnd.ms-powerpoint";
            break;
            case 'pps':
            $mimetype = "application/vnd.ms-powerpoint";
            break;
            case 'pot':
            $mimetype = "application/vnd.ms-powerpoint";
            break;
          
            case "ogg":
            $mimetype = "application/ogg";
            break;
            case "pls":
            $mimetype = "application/pls+xml";
            break;
            case "asf":
            $mimetype = "application/vnd.ms-asf";
            break;
            case "wmlc":
            $mimetype = "application/vnd.wap.wmlc";
            break;
            case 'dot':
            $mimetype = "application/msword";
            break;
            case 'class':
            $mimetype = "application/octet-stream";
            break;
            case 'exe':
            $mimetype = "application/octet-stream";
            break;
            case 'pdf':
            $mimetype = "application/pdf";
            break;
            case 'rtf':
            $mimetype = "application/rtf";
            break;
            case 'xla':
            $mimetype = "application/vnd.ms-excel";
            break;
            case 'xlc':
            $mimetype = "application/vnd.ms-excel";
            break;
            case 'xlm':
            $mimetype = "application/vnd.ms-excel";
            break;
           
            case 'msg':
            $mimetype = "application/vnd.ms-outlook";
            break;
            case 'mpp':
            $mimetype = "application/vnd.ms-project";
            break;
            case 'cdf':
            $mimetype = "application/x-cdf";
            break;
            case 'tgz':
            $mimetype = "application/x-compressed";
            break;
            case 'dir':
            $mimetype = "application/x-director";
            break;
            case 'dvi':
            $mimetype = "application/x-dvi";
            break;
            case 'gz':
            $mimetype = "application/x-gzip";
            break;
            case 'js':
            $mimetype = "application/x-javascript";
            break;
            case 'mdb':
            $mimetype = "application/x-msaccess";
            break;
            case 'dll':
            $mimetype = "application/x-msdownload";
            break;
            case 'wri':
            $mimetype = "application/x-mswrite";
            break;
            case 'cdf':
            $mimetype = "application/x-netcdf";
            break;
            case 'swf':
            $mimetype = "application/x-shockwave-flash";
            break;
            case 'tar':
            $mimetype = "application/x-tar";
            break;
            case 'man':
            $mimetype = "application/x-troff-man";
            break;
            case 'zip':
            $mimetype = "application/zip";
            break;
            case 'xlsx':
            $mimetype = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
            break;
            case 'pptx':
            $mimetype = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
            break;
            case 'docx':
            $mimetype = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
            break;
            case 'xltx':
            $mimetype = "application/vnd.openxmlformats-officedocument.spreadsheetml.template";
            break;
            case 'potx':
            $mimetype = "application/vnd.openxmlformats-officedocument.presentationml.template";
            break;
            case 'ppsx':
            $mimetype = "application/vnd.openxmlformats-officedocument.presentationml.slideshow";
            break;
            case 'sldx':
            $mimetype = "application/vnd.openxmlformats-officedocument.presentationml.slide";
            break;
          
            ///Audio and Video Files
            
            case 'mp3':
            $mimetype = "audio/mpeg";
            break;
            case 'wav':
            $mimetype = "audio/x-wav";
            break;
            case 'au':
            $mimetype = "audio/basic";
            break;
            case 'snd':
            $mimetype = "audio/basic";
            break;
            case 'm3u':
            $mimetype = "audio/x-mpegurl";
            break;
            case 'ra':
            $mimetype = "audio/x-pn-realaudio";
            break;
            case 'mp2':
            $mimetype = "video/mpeg";
            break;
            case 'mov':
            $mimetype = "video/quicktime";
            break;
            case 'qt':
            $mimetype = "video/quicktime";
            break;
            case 'mp4':
            $mimetype = "video/mp4";
            break;
            case 'm4a':
            $mimetype = "audio/mp4";
            break;
            case 'mp4a':
            $mimetype = "audio/mp4";
            break;
            case 'm4p':
            $mimetype = "audio/mp4";
            break;
            case 'm3a':
            $mimetype = "audio/mpeg";
            break;
            case 'm2a':
            $mimetype = "audio/mpeg";
            break;
            case 'mp2a':
            $mimetype = "audio/mpeg";
            break;
            case 'mp2':
            $mimetype = "audio/mpeg";
            break;
            case 'mpga':
            $mimetype = "audio/mpeg";
            break;
            case '3gp':
            $mimetype = "video/3gpp";
            break;
            case '3g2':
            $mimetype = "video/3gpp2";
            break;
            case 'mp4v':
            $mimetype = "video/mp4";
            break;
            case 'mpg4':
            $mimetype = "video/mp4";
            break;
            case 'm2v':
            $mimetype = "video/mpeg";
            break;
            case 'm1v':
            $mimetype = "video/mpeg";
            break;
            case 'mpe':
            $mimetype = "video/mpeg";
            break;
            case 'avi':
            $mimetype = "video/x-msvideo";
            break;
            case 'midi':
            $mimetype = "audio/midi";
            break;
            case 'mid':
            $mimetype = "audio/mid";
            break;
            case 'amr':
            $mimetype = "audio/amr";
            break;
            
            
            default:
            $mimetype = "application/octet-stream";
        
            
        }
        
        
       }
        
        
          //off output buffering to decrease Server usage
          @ob_end_clean();
        
          if(ini_get('zlib.output_compression')){
            ini_set('zlib.output_compression', 'Off');
          }
        
          header('Content-Description: File Transfer');
          header('Content-Type: '.$mimetype);
          header('Content-Disposition: attachment; filename='.$newfilename.'');
          header('Content-Transfer-Encoding: binary');
          header("Expires: Wed, 07 May 2013 09:09:09 GMT");
	    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	    header('Cache-Control: post-check=0, pre-check=0', false);
	    header('Cache-Control: no-store, no-cache, must-revalidate');
	    header('Pragma: no-cache');
          header('Content-Length: '.$filesize);
        
        
          ///Will Download 1 MB in chunkwise
          $chunk = 1 * (1024 * 1024);
          $nfile = @fopen($file,"rb");
          while(!feof($nfile))
          {
                 
              print(@fread($nfile, $chunk));
              @ob_flush();
              @flush();
          }
          @fclose($filen);
               


}



function add_update_user_moodle($user_new,$check_user){
        //print_r($user_new);
        //print_r($check_user);
        //die();
        //echo $url;
        $ch_check = curl_init(''.MOODLE_URL.'/webservice/rest/server.php?wstoken='.MOODLE_API_KEY.'&wsfunction=core_user_get_users&moodlewsrestformat=json');
        curl_setopt($ch_check, CURLOPT_POST, 1);
        curl_setopt($ch_check,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch_check,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch_check, CURLOPT_POSTFIELDS, http_build_query($check_user));
        $result_check = json_decode(curl_exec($ch_check));
        //print_r($result_check);
        if(curl_errno($ch_check)){
            //throw new exception(curl_error($ch_check));
            return false;
        }
        
        if(@property_exists($result_check,'exception')){
            //throw new exception($result_check->debuginfo);
            return false;
        }
        
        if(isset($result_check->users[0]->id) && $student_id = $result_check->users[0]->id){
            
        $url_update="".MOODLE_URL."/webservice/rest/server.php?wstoken=".MOODLE_API_KEY."&wsfunction=core_user_update_users&moodlewsrestformat=json";
        $user_new['users'][0]['id']=$student_id;
        //print_r($user_new);
        $student_data = $user_new;
        $ch = curl_init($url_update);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($student_data));
        $result = json_decode(curl_exec($ch));   
        //print_r($result);
        
        if(curl_errno($ch)){
            //throw new exception(curl_error($ch));
            return false;
        }
        
        if(@property_exists($result,'exception')){
            //throw new exception($result->debuginfo);
            return false;
        }
        
        return true;
        
        }else{
        
        $url_new="".MOODLE_URL."/webservice/rest/server.php?wstoken=".MOODLE_API_KEY."&wsfunction=core_user_create_users&moodlewsrestformat=json";
        $student_data = $user_new;
        $ch = curl_init($url_new);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($student_data));
        $result = json_decode(curl_exec($ch));
        //print_r($result);
        if(curl_errno($ch)){
            //throw new exception(curl_error($ch));
            return false;
        }
        
        if(@property_exists($result,'exception')){
            //throw new exception($result->debuginfo);
            return false;
        }
        
        if(!$student_id = $result[0]->id){
          //throw new exception($result->debuginfo);
            return false;
        }
        
        return true;
        
        }
}

function sanitazifileName($file){
    
    $special_symbols = array("#","[","]","/","\\",",","(",")","&","$","*","{","}"," ");
    
    return str_replace($special_symbols, "", $file);
    
}

function getdata($url){
    
    /**
* Initialize the cURL session
*/
$ch = curl_init();
/**
* Set the URL of the page or file to download.
*/
curl_setopt($ch, CURLOPT_URL,$url);
/**
* Ask cURL to return the contents in a variable
* instead of simply echoing them to the browser.
*/
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
/**
* Execute the cURL session
*/
 $contents = curl_exec ($ch);
/**
* Close cURL session
*/
curl_close ($ch);
return $contents;
}
			
