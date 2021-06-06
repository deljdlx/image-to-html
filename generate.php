<?php

function imageToMatrix($file, $cssPrefix)
{
    $sizes = getimagesize($file);
    if(strpos($file, '.png')) {
        $image = imagecreatefrompng($file);
    }
    else if (strpos($file, '.jpg')) {
        $image = imagecreatefromjpeg($file);
    }

    $matrix = [];
    $css = [];
    $cssByCharacter = [];


    for($y = 0 ; $y < $sizes[1] ; $y ++) {

        $line = [];
        $lineBuffer = '';

        for($x = 0 ; $x < $sizes[0] ; $x++) {
            $rgb = imagecolorat($image, $x, $y);


            $colors = imagecolorsforindex($image, $rgb);

            //$className = $colors['red'] . ',' . $colors['green'] . ', ' . $colors['blue'];
            $className = 'p-' . $cssPrefix . '-' . $rgb;



            if(!isset($matrix[$className])) {
                $opacity = round((127 - $colors['alpha']) / 127, 2);
                $css[$className] = [
                    'background-color' => 'rgba(' . $colors['red'] . ',' . $colors['green'] . ', ' . $colors['blue'] . ',' . $opacity . ')'
                ];
            }


            $cssIndexes = array_keys($css);

            $cssIndex = array_search($className, $cssIndexes);

            $character = base_convert($cssIndex, 10, 36);

            //echo $character . "\n";

            $line[] = $character;
            $lineBuffer .= $character;

            $cssByCharacter[$character] = $css[$className];
        }

        //$matrix[] = $line;
        $matrix[] = $lineBuffer;

    }

    $descriptor = [
        'matrix' => $matrix,
        'css' => $cssByCharacter
    ];

    return $descriptor;

}

$spritePath = __DIR__ . '/sprite';

$dir = opendir($spritePath);


$index = 0;

while($file = readdir($dir)) {
    if($file != '.' && $file != '..') {
        $sprite = $spritePath . '/' . $file;
        echo $sprite . "\n";


        $data = imageToMatrix($sprite, $index);

        $buffer = '';
        foreach($data['css'] as $rule => $attributes) {
            $buffer .=  '.' . 'pixel-' . $rule . '{';
            foreach($attributes as $attributeName => $value) {
                $buffer .= $attributeName . ' : ' . $value . ';';
            }
            $buffer .=  '}' . "\n";
        }


        file_put_contents(__DIR__ . '/output/sprite-' . $index . '.css', $buffer);
        file_put_contents(__DIR__ . '/output/sprite-' . $index . '.js', 'sprites[' . $index . '] = ' . json_encode($data['matrix'], JSON_PRETTY_PRINT));

        $index++;
    }
}


