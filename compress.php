<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 27/6/2017
 * Time: 19:29
 */
function getFiles($path,$child=false){
    $files=array();
    if(!$child){
        if(is_dir($path)){
            $dp = dir($path);
        }else{
            return null;
        }
        while ($file = $dp ->read()){
            if($file !="." && $file !=".." && is_file($path.$file)){
                $files[] = $file;
            }
        }
        $dp->close();
    }else{
        scanfiles($files,$path);
    }
    return $files;
}
/**
 *@param $files 结果
 *@param $path 路径
 *@param $childDir 子目录名称
 */
function scanfiles(&$files,$path,$childDir=false){
    $dp = dir($path);
    while ($file = $dp ->read()){
        if($file !="." && $file !=".."){
            if(is_file($path.$file)){//当前为文件
                if(strrpos($file,".png") > -1){
                  array_push($files,$path.$file);
                }
            }else{//当前为目录
                scanfiles($files,$path.$file.DIRECTORY_SEPARATOR,$file);
            }
        }
    }
    $dp->close();
}

function compress_png($path_to_png_file, $max_quality = 90)
{
    if (!file_exists($path_to_png_file)) {
        throw new Exception("File does not exist: $path_to_png_file");
    }

    // guarantee that quality won't be worse than that.
    $min_quality = 60;

    // '-' makes it use stdout, required to save to $compressed_png_content variable
    // '<' makes it read from the given file path
    // escapeshellarg() makes this safe to use with any path
    $compressed_png_content = shell_exec("./pngquant/pngquant --quality=$min_quality-$max_quality - < ".escapeshellarg(    $path_to_png_file));

    if (!$compressed_png_content) {
        throw new Exception("Conversion to compressed PNG failed. Is pngquant 1.8+ installed on the server?");
    }
    return $compressed_png_content;
}

$files=getFiles($argv [1],true);
foreach($files as $file){
  $compressed_png_content = compress_png($file);
  file_put_contents($file, $compressed_png_content);
}
echo "compress successfully.\n";
