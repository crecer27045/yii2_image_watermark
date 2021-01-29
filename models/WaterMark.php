<?php

namespace app\models;

use Yii;

class WaterMark
{

    public function getDistanceFromColor($a, $b)
    {
        list($r1, $g1, $b1) = $a;
        list($r2, $g2, $b2) = $b;

        return sqrt(pow($r2-$r1, 2)+pow($g2-$g1, 2)+pow($b2-$b1, 2));
    }

    public function findMaxColor($hex): string
    {
        $colorArr = sscanf($hex, "%02x%02x%02x");

        $max = null;
        $maxKey = null;

        foreach ($colorArr as $k => $v)
        {
            if ($v > $max or $max === null)
            {
                $max = $v;
                $maxKey = $k;
            }
        }
        return $maxKey;
    }

    public function getClosestColor($color, array $pallet)
    {
        $distances = array_map(function ($colorFromPallet) use ($color) {
            return $this->getDistanceFromColor($color, $colorFromPallet);
        }, $pallet);

        ksort($distances);
        $keys = array_keys($distances);

        return $pallet[$keys[0]];
    }

    public function getImageWithWaterMark($image_path): string
    {

        $colorsModel = new GetMostCommonColors();

        $colors = $colorsModel->Get_Color($image_path, 3, 1, 1, 24);

        // Unset Black and White
        unset($colors['ffffff']);
        unset($colors['000000']);

        $mainColor = $this->findMaxColor(array_key_first($colors));

        $waterMark = array([
            'image' => null,
            'width' => null,
            'height' => null
        ]);

        switch ($mainColor) {
            case 0:
//                echo "Цвет красный";
                $waterMark['image'] = imagecreatefrompng('watermark_black.png');
                break;
            case 1:
//                echo "Цвет зеленый";
                $waterMark['image'] = imagecreatefrompng('watermark_red.png');
                break;
            case 2:
//                echo "Цвет синий";
                $waterMark['image'] = imagecreatefrompng('watermark_yellow.png');
                break;
        }

        $waterMark['width'] = imagesx($waterMark['image']);
        $waterMark['height'] = imagesy($waterMark['image']);


        $imageType = exif_imagetype($image_path);

        $image = $imageType == 2 ? imagecreatefromjpeg($image_path) : imagecreatefrompng($image_path);

        $size = getimagesize($image_path);

        $dest_x = $size[0] - $waterMark['width'] - 5;
        $dest_y = $size[1] - $waterMark['height'] - 5;

        imagealphablending($image, true);
        imagealphablending($waterMark['image'], true);

        $imageName = Yii::$app->security->generateRandomString(12);

        imagecopy($image, $waterMark['image'], $dest_x, $dest_y, 0, 0, $waterMark['width'], $waterMark['height']);
        imagejpeg($image, 'saved/' . $imageName . '.jpg', 80);


        imagedestroy($image);
        imagedestroy($waterMark['image']);

        return 'saved/' . $imageName . '.jpg';
    }
}
