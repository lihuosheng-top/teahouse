<?php

namespace app\admin\model;
use think\Controller;
use think\Model;

class MakeZip extends Model
{

    /**
     * description:主方法：生成压缩包
     * @author: MY
     * @param $dir_path  想要压缩的目录：如 './demo/'
     * @param $zipName   压缩后的文件名：如 './folder/demo.zip'
     * @return string
     */
    public function zip($dir_path, $zipName,$id)
    { 
        $relationArr = array(
            $dir_path => array(
                'originName' => $dir_path,
                'is_dir' => true,
                'children' => array()
            )
        );

        $key = array_keys($relationArr);
        $val = array_values($relationArr);

        // $this->modifiyFileName($dir_path, $relationArr[$dir_path]['children']);
        $zip = new \ZipArchive();
        //ZIPARCHIVE::CREATE没有即是创建
        $zip->open($zipName, \ZipArchive::CREATE);
        $this->zipDir($key[0], $zipName, $zip, $val[0]['children'],$id);
        $zip->close();
        // $this->restoreFileName($key[0], $val[0]['children']);
        return true;
    }

    public function zipDir($real_path, $zip_path, &$zip, $relationArr,$id)
    {
        $sub_zip_path = empty($zip_path) ? '' : $zip_path . '\\';
        if (is_dir($real_path)) {
            $handler = opendir($real_path); 
             while (($filename = readdir($handler)) !== false){
                 if ($filename != "." && $filename != "..") {  
                    $pathFilename = $real_path.$filename; 
                    $rest = str_replace($real_path,'',$pathFilename);
                    $restuler = $id.'/'.$rest;
                    $zip->addFile($pathFilename,$restuler);
                    }
                }
            }

    }

    public function modifiyFileName($path, &$relationArr)
    {
        if (!is_dir($path) || !is_array($relationArr)) {
            return false;
        }
        if ($dh = opendir($path)) {
            $count = 0;
            while (($file = readdir($dh)) !== false) {
                if(in_array($file,array('.', '..', null))) continue; //无效文件，重来
                if (is_dir($path . '\\' . $file)) {
                    $newName = md5(rand(0, 99999) . rand(0, 99999) . rand(0, 99999) . microtime() . 'dir' . $count);
                    $relationArr[$newName] = array(
                        'originName' => iconv('GBK', 'UTF-8', $file),
                        'is_dir' => true,
                        'children' => array()
                    );
                    rename($path . '\\' . $file, $path . '\\' . $newName);
                    $this->modifiyFileName($path . '\\' . $newName, $relationArr[$newName]['children']);
                    $count++;
                } else {
                    $extension = strchr($file, '.');
                    $newName = md5(rand(0, 99999) . rand(0, 99999) . rand(0, 99999) . microtime() . 'file' . $count);
                    $relationArr[$newName . $extension] = array(
                        'originName' => iconv('GBK', 'UTF-8', $file),
                        'is_dir' => false,
                        'children' => array()
                    );
                    rename($path .$file, $path . $newName . $extension);
                    $count++;
                }
            }
        }
    }

    public function restoreFileName($path, $relationArr)
    {
        foreach ($relationArr as $k => $v) {
            if (!empty($v['children'])) {
                $this->restoreFileName($path . '\\' . $k, $v['children']);
                rename($path . '\\' . $k, iconv('UTF-8', 'GBK', $path . '\\' . $v['originName']));
            } else {
                rename($path . '\\' . $k, iconv('UTF-8', 'GBK', $path . '\\' . $v['originName']));
            }
        }
    }

}