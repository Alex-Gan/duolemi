<?php
namespace App\Http\Controllers\Api;

use App\Services\Api\UserTokenService;

class Testcontroller
{
    public function testToekn()
    {
        $token = new UserTokenService("021NvyvE1kn6e80KnZvE1AHAvE1Nvyvz");

        dd($token->getUserToken());
    }


    public function image()
    {

        phpinfo();

        $bg = public_path()."/images/experience_course_template.jpg";
        $file_code = public_path()."/images/code.jpg";
        $img_bg = imagecreatefromjpeg($bg); //背景图
        $img_code = imagecreatefromjpeg($file_code);//二维码

        //获取背景图的宽高
        $width = imagesx($img_bg);
        $height = imagesy($img_bg);

        $im = imagecreatetruecolor($width, $height);  //创建一张与背景图同样大小的真彩色图像
        imagecopy($im, $img_bg, 0, 0, 0, 0, $width, $height);

        $width_code = 258;
        $height_code = 258;

        /*开始合成*/
        imagecopymerge($im, $img_code, $width - $width_code - 500, $height - $height_code - 100, 0, 0, $width_code, $height_code, 100);
        /*保存路径*/
        $relative_path = '/images/user_experience_course/'.mt_rand().time().'.png';
        imagepng($im, public_path().$relative_path);
        dd(11);

        header('Content-Type: image/png');
        Imagepng($im);      //浏览器直接显示
        ImageDestroy($img_code);
        ImageDestroy($im);
        //dd($img_bg);

    }
}