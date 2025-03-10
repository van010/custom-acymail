<?php

namespace AcyMailing\Helpers;

use AcyMailing\Libraries\acymObject;

class ImageHelper extends acymObject
{
    var $error;
    var $maxHeight;
    var $maxWidth;
    var $destination;

    public function removePictures($text)
    {
        $return = preg_replace('#< *img((?!content_main_image)[^>])*>#Ui', '', $text);
        $return = preg_replace('#< *div[^>]*class="jce_caption"[^>]*>[^<]*(< *div[^>]*>[^<]*<\/div>)*[^<]*<\/div>#Ui', '', $return);

        return $return;
    }

    public function available()
    {
        if (!function_exists('gd_info')) {
            $this->error = 'The GD library is not installed.';

            return false;
        }
        if (!function_exists('getimagesize')) {
            $this->error = 'Cound not find getimagesize function';

            return false;
        }
        if (!function_exists('imagealphablending')) {
            $this->error = "Please make sure you're using GD 2.0.1 or later version";

            return false;
        }

        return true;
    }

    public function resizePictures($input)
    {
        $this->destination = ACYM_MEDIA.'resized'.DS;
        acym_createDir($this->destination);
        $content = acym_absoluteURL($input);

        preg_match_all('#<img([^>]*)>#Ui', $content, $results);
        if (empty($results[1])) {
            return $input;
        }

        $replace = [];

        foreach ($results[1] as $onepicture) {
            if (strpos($onepicture, 'donotresize') !== false) {
                continue;
            }

            if (!preg_match('#src="([^"]*)"#Ui', $onepicture, $path)) {
                continue;
            }
            $imageUrl = $path[1];

            $imageUrl = preg_replace('/#joomlaImage.*/', '', $imageUrl);

            $imageUrl = acym_internalUrlToPath($imageUrl);

            $newPicture = $this->generateThumbnail($imageUrl);

            $newDimension = 'max-width:'.$this->maxWidth.'px;max-height:'.$this->maxHeight.'px;';

            if (!$newPicture) {
                if (strpos($onepicture, 'style="') !== false) {
                    $replace[$onepicture] = preg_replace('#style="([^"]*)"#Uis', 'style="'.$newDimension.'$1"', $onepicture);
                } else {
                    $replace[$onepicture] = ' style="'.$newDimension.'" '.$onepicture;
                }
                continue;
            }

            $newPicture['file'] = preg_replace('#^'.preg_quote(ACYM_ROOT, '#').'#i', ACYM_LIVE, $newPicture['file']);
            $newPicture['file'] = str_replace(DS, '/', $newPicture['file']);
            $replaceImage = [];
            $replaceImage[$path[1]] = $newPicture['file'];
            if (preg_match_all('#(width|height)(:|=) *"?([0-9]+)#i', $onepicture, $resultsSize)) {
                foreach ($resultsSize[0] as $i => $oneArg) {
                    $newVal = (strtolower($resultsSize[1][$i]) == 'width') ? $newPicture['width'] : $newPicture['height'];
                    if ($newVal > $resultsSize[3][$i]) {
                        continue;
                    }
                    $replaceImage[$oneArg] = str_replace($resultsSize[3][$i], $newVal, $oneArg);
                }
            }

            $replace[$onepicture] = str_replace(array_keys($replaceImage), $replaceImage, $onepicture);

            if (strpos($replace[$onepicture], 'width') === false) {
                if (strpos($onepicture, 'style="') !== false) {
                    $replace[$onepicture] = preg_replace('#style="([^"]*)"#Uis', 'style="'.$newDimension.'$1"', $replace[$onepicture]);
                } else {
                    $replace[$onepicture] = ' style="'.$newDimension.'" '.$replace[$onepicture];
                }
            }
        }

        if (!empty($replace)) {
            $input = str_replace(array_keys($replace), $replace, $content);
        }

        return $input;
    }

    public function generateThumbnail($picturePath)
    {
        $paramsPos = strpos($picturePath, '?');
        if ($paramsPos !== false) $picturePath = substr($picturePath, 0, $paramsPos);

        [$currentwidth, $currentheight] = @getimagesize($picturePath);
        if (empty($currentwidth) || empty($currentheight)) {
            return false;
        }
        $factor = min($this->maxWidth / $currentwidth, $this->maxHeight / $currentheight);
        if ($factor >= 1) {
            return false;
        }
        $newWidth = round($currentwidth * $factor);
        $newHeight = round($currentheight * $factor);

        if (strpos($picturePath, 'http') === 0) {
            $filename = substr($picturePath, strrpos($picturePath, '/') + 1);
        } else {
            $filename = basename($picturePath);
        }

        if (substr($picturePath, 0, 10) == 'data:image') {
            preg_match('#data:image/([^;]{1,5});#', $picturePath, $resultextension);
            if (empty($resultextension[1])) {
                return false;
            }
            $extension = $resultextension[1];
            $name = md5($picturePath);
        } else {
            $extension = strtolower(substr($filename, strrpos($filename, '.') + 1));
            $name = strtolower(substr($filename, 0, strrpos($filename, '.')));
            $name .= substr(@filemtime($picturePath), -4);
        }

        $newImage = md5($picturePath).'-'.$name.'thumb'.$this->maxWidth.'x'.$this->maxHeight.'.'.$extension;
        if (empty($this->destination)) {
            $newFile = dirname($picturePath).DS.$newImage;
        } else {
            $newFile = $this->destination.$newImage;
        }

        if (file_exists($newFile)) {
            return [
                'file' => $newFile,
                'width' => $newWidth,
                'height' => $newHeight,
            ];
        }

        switch ($extension) {
            case 'gif':
                $img = imagecreatefromgif($picturePath);
                break;
            case 'jpg':
            case 'jpeg':
                $img = imagecreatefromjpeg($picturePath);
                break;
            case 'png':
                $img = imagecreatefrompng($picturePath);
                break;
            case 'webp':
                if (function_exists('imagecreatefromwebp')) {
                    $img = imagecreatefromwebp($picturePath);
                }
                break;
            default:
                return false;
        }

        if (empty($img)) {
            return false;
        }

        $thumb = imagecreatetruecolor($newWidth, $newHeight);

        if (in_array($extension, ['gif', 'png'])) {
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
        }

        if (function_exists('imagecopyresampled')) {
            imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newWidth, $newHeight, $currentwidth, $currentheight);
        } else {
            ImageCopyResized($thumb, $img, 0, 0, 0, 0, $newWidth, $newHeight, $currentwidth, $currentheight);
        }

        ob_start();
        switch ($extension) {
            case 'gif':
                $status = imagegif($thumb);
                break;
            case 'jpg':
            case 'jpeg':
                $status = imagejpeg($thumb, null, 100);
                break;
            case 'png':
                $status = imagepng($thumb, null, 0);
                break;
            case 'webp':
                $status = imagewebp($thumb, null, 100);
                break;
        }
        $imageContent = ob_get_clean();

        $status = $status && acym_writeFile($newFile, $imageContent);
        imagedestroy($thumb);
        imagedestroy($img);

        if (!$status) {
            $newFile = $picturePath;
        }

        return [
            'file' => $newFile,
            'width' => $newWidth,
            'height' => $newHeight,
        ];
    }
}
