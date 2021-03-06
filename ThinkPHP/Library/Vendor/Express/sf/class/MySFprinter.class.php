<?php

class SFprinter {

    private $_SETTING = array(
        "dir" => "/SF/",
        "font" => "simhei.ttf",
        "numbfont" => "arial.ttf",
        "boldnumbfont" => "arialbd.ttf",
        "model" => "threemodel2.jpg"
    );
    private $_PIC;
    private $_MODEL;
    private $_FONT;
    private $_NUMBFONT;
    private $_BOLDNUMBFONT;
    var $defult = array(
        "express_type" => "", //快件业务类型
        "mailno" => "", //运单号
        "orderid" => "", //商家订单号
        "j_company" => "", //寄件方公司
        "j_contact" => "", //寄件方姓名
        "j_tel" => "", //寄件方电话
        "j_province" => "",//寄件方省
        "j_city" => "",//寄件方市
        "j_qu" => "",//寄件方区
        "j_address" => "", //寄件方地址
        "j_number" => "",

        "d_company" => "", //收件方公司
        "d_contact" => "", //收件方姓名
        "d_tel" => "", //收件方电话
        "d_province" => "", //省
        "d_city" => "", //市
        "d_qu" => "", //区县
        "d_address" => "", //详细地址（街道）
        "d_number" => "", //收件方地方编号

        "pay_method" => "", //收费方式
        "custid" => "", //月结帐号
        "daishou" => 0, //代收款项
        "remark" => "", //备注
        "things" => ""//寄托物
    );

    const BLACK = 0x000000;
    const RED = 0xFF0000;

    function __construct() {
        $this->SFconfig();
        $this->SFmodel($this->_MODEL);
    }

    function SFconfig() {
        $local = str_replace("\\", '/', dirname(__FILE__));
        $this->_MODEL = $local . $this->_SETTING["dir"] . $this->_SETTING["model"];
        $this->_FONT = $local . $this->_SETTING["dir"] . $this->_SETTING["font"];
        $this->_NUMBFONT = $local . $this->_SETTING["dir"] . $this->_SETTING["numbfont"];
        $this->_BOLDNUMBFONT = $local . $this->_SETTING["dir"] . $this->_SETTING["boldnumbfont"];
    }

    function SFmodel($model) {
        $imgtype = getimagesize($model);
        if ($imgtype[2] == 1) {
            $this->_PIC = imagecreatefromgif($model);
        } else if ($imgtype[2] == 2) {
            $this->_PIC = imagecreatefromjpeg($model);
        } else {
            $this->_PIC = imagecreatefrompng($model);
        }
        return $this;
    }

    function SFdata($pushdata) {
            
        $data = array_merge($this->defult, $pushdata);
      
       
        if ($data["daishou"] != "" && $data["daishou"] != 0) {
            imagefttext($this->_PIC, $this->dpi300(24), 0, 320, 136, self::BLACK, $this->_NUMBFONT, "COD");
        }

        if($data["express_type"] == "电商特惠" || $data["express_type"] == "顺丰隔日"){
            imagefttext($this->_PIC, $this->dpi300(24), 0, 600, 120, self::BLACK, $this->_FONT, "E");
        }

        //生成大条形码
        $COR1 = array(
            "text" => $data["mailno"],
            "thickness" => 40,
            "scale" => 5
        );
        $CO1 = new Code128($COR1);
        $BC1 = $CO1->creatCode()->IM();
        $BC1_W = imagesx($BC1);
        imagecopyresized($this->_PIC, $BC1, 130, 210, 0, 0, $BC1_W, 120, $BC1_W, 120);
        imagefttext($this->_PIC, $this->dpi300(10), 0, 180, 380, self::BLACK, $this->_NUMBFONT, $this->SFmailno($data["mailno"]));
   
        //生成小条形码
        $COR2 = array(
            "text" => $data["mailno"],
            "thickness" => 30,
            "scale" => 5
        );
        $CO2 = new Code128($COR2);
        $BC2 = $CO2->creatCode()->IM();
        $BC2_W = imagesx($BC2);
        imagecopyresized($this->_PIC, $BC2, 450, 1085, 0, 0, $BC2_W, 100, $BC2_W, 100);
        imagefttext($this->_PIC, $this->dpi300(10), 0, 510, 1235, self::BLACK, $this->_NUMBFONT, $this->SFmailno($data["mailno"]));
        //第三联
        imagecopyresized($this->_PIC, $BC2, 450, 1795, 0, 0, $BC2_W, 100, $BC2_W, 100);
        imagefttext($this->_PIC, $this->dpi300(10), 0, 510, 1940, self::BLACK, $this->_NUMBFONT, $this->SFmailno($data["mailno"]));

 
        //传1 打印顺丰次日
        //传2 打印顺丰隔日
        //传5 打印顺丰次晨
        //传6 打印顺丰即日
        imagefttext($this->_PIC, $this->dpi300(10), 0, 800, 205, self::BLACK, $this->_FONT, $data["express_type"]);//

        imagefttext($this->_PIC, $this->dpi300(12), 0, 800, 275, self::BLACK, $this->_FONT, '代收货款');
  
        if ($data["pay_method"] == "收方付款" && $data["custid"] != "") {
             imagefttext($this->_PIC, $this->dpi300(6), 0, 800, 320, self::BLACK, $this->_FONT, "卡号:".$data["custid"]);
        }
        
       
        if ($data["daishou"] != "" && $data["daishou"] != 0) {
            imagefttext($this->_PIC, $this->dpi300(6), 0, 800, 360, self::BLACK, $this->_FONT, "￥" . $data["daishou"] . "元");
        }

        imagefttext($this->_PIC, $this->dpi300(6), 0, 40, 455, self::BLACK, $this->_FONT, "目");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 40, 485, self::BLACK, $this->_FONT, "的");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 40, 515, self::BLACK, $this->_FONT, "地");
        imagefttext($this->_PIC, $this->dpi300(24), 0, 120, 500, self::BLACK, $this->_BOLDNUMBFONT, str_pad($data["d_number"], 3, "0", STR_PAD_LEFT));//$data["d_number"]

        //收件方
        imagefttext($this->_PIC, $this->dpi300(6), 0, 40, 610, self::BLACK, $this->_FONT, "收");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 40, 645, self::BLACK, $this->_FONT, "件");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 40, 675, self::BLACK, $this->_FONT, "人");
//        imagefttext($this->_PIC, $this->dpi300(8), 0, 0, 610, self::BLACK, $this->_FONT, "（自取）");

        $SSQ = "";
        if ($data["d_province"] != "") {
            $SSQ .= $data["d_province"] . "，";
        }
        if ($data["d_city"] != "") {
            $SSQ .= $data["d_city"] . "，";
        }
        if ($data["d_qu"] != "") {
            $SSQ .= $data["d_qu"] . "，";
        }

        $JSSQ = "";
        if ($data["d_province"] != "") {
            $JSSQ .= $data["j_province"] . "，";
        }
        if ($data["d_city"] != "") {
            $JSSQ .= $data["j_city"] . "，";
        }
        if ($data["d_qu"] != "") {
            $JSSQ .= $data["j_qu"] . "，";
        }

        imagefttext($this->_PIC, $this->dpi300(14), 0, 120, 480, self::BLACK, $this->_FONT, $SSQ);
        imagefttext($this->_PIC, $this->dpi300(8), 0, 120, 620, self::BLACK, $this->_FONT, $data["d_address"]);
        imagefttext($this->_PIC, $this->dpi300(8), 0, 120, 670, self::BLACK, $this->_FONT, $data["d_company"]);
        imagefttext($this->_PIC, $this->dpi300(8), 0, 120, 670, self::BLACK, $this->_FONT, $data["d_contact"] . "（收）  " . $data["d_tel"]);


        imagefttext($this->_PIC, $this->dpi300(6), 0, 30, 840, self::BLACK, $this->_FONT, "付款方式：" . $data["pay_method"]);
       // if ( $data["pay_method"] == "寄付月结") {
            imagefttext($this->_PIC, $this->dpi300(6), 0, 30, 880, self::BLACK, $this->_FONT, "月结帐号：".$data["custid"] );
       // }
        
        imagefttext($this->_PIC, $this->dpi300(6), 0, 340, 840, self::BLACK, $this->_FONT, "实际重量：");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 340, 880, self::BLACK, $this->_FONT, "计费重量：");
        
        
        imagefttext($this->_PIC, $this->dpi300(9), 0, 900, 740, self::BLACK, $this->_FONT, "定时派送");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 900, 780, self::BLACK, $this->_FONT, "自寄");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 1000, 780, self::BLACK, $this->_FONT, "自取");
    
        imagefttext($this->_PIC, $this->dpi300(6), 0, 580, 985, self::BLACK, $this->_FONT, "收件员：");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 580, 1020, self::BLACK, $this->_FONT, "寄件日期：");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 580, 1055, self::BLACK, $this->_FONT, "派件员：");

        imagefttext($this->_PIC, $this->dpi300(7), 0, 900, 845, self::BLACK, $this->_FONT, "签名：");
        imagefttext($this->_PIC, $this->dpi300(7), 0, 920, 1025, self::BLACK, $this->_FONT, "  月   日");
        
        //寄件方
        imagefttext($this->_PIC, $this->dpi300(6), 0, 40, 740, self::BLACK, $this->_FONT, "寄");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 40, 775, self::BLACK, $this->_FONT, "件");
        //imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 720, self::BLACK, $this->_FONT, $JSSQ);
        imagefttext($this->_PIC, $this->dpi300(6), 0, 120, 740, self::BLACK, $this->_FONT, $data["j_province"] . $data["j_city"] . $data["j_qu"].$data["j_address"]);
        //imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 755, self::BLACK, $this->_FONT, $data["j_address"]);
       // imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 790, self::BLACK, $this->_FONT, $data["j_company"]);
        imagefttext($this->_PIC, $this->dpi300(6), 0, 120, 780, self::BLACK, $this->_FONT, $data["j_contact"] . "   " . $data["j_tel"]);
        imagefttext($this->_PIC, $this->dpi300(6), 0, 430, 780, self::BLACK, $this->_FONT, "原寄地：" . $data["j_number"]);
        /*
        //寄件方
        imagefttext($this->_PIC, $this->dpi300(10), 0, 20, 930, self::BLACK, $this->_FONT, "寄");
        imagefttext($this->_PIC, $this->dpi300(10), 0, 20, 975, self::BLACK, $this->_FONT, "件");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 920, self::BLACK, $this->_FONT, $JSSQ);
        imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 955, self::BLACK, $this->_FONT, $data["j_address"]);
        imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 990, self::BLACK, $this->_FONT, $data["j_company"]);
        imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 1026, self::BLACK, $this->_FONT, $data["j_contact"] . "   " . $data["j_tel"]);
        imagefttext($this->_PIC, $this->dpi300(6), 0, 430, 1026, self::BLACK, $this->_FONT, "原寄地：" . $data["j_number"]);
        */
        //-----------------------------------------------------------------------
        //寄件方
        imagefttext($this->_PIC, $this->dpi300(6), 0, 40, 1290, self::BLACK, $this->_FONT, "寄");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 40, 1325, self::BLACK, $this->_FONT, "件");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 1325, self::BLACK, $this->_FONT, $data["j_province"] . $data["j_city"] . $data["j_qu"].$data["j_address"]);
       // imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 1370, self::BLACK, $this->_FONT, $data["j_address"]);
       // imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 1385, self::BLACK, $this->_FONT, $data["j_company"]);
        imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 1290, self::BLACK, $this->_FONT, $data["j_contact"] . "  " . $data["j_tel"]);

        //收件方
        imagefttext($this->_PIC, $this->dpi300(6), 0, 40, 1410, self::BLACK, $this->_FONT, "收");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 40, 1445, self::BLACK, $this->_FONT, "件");
        //imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 1440, self::BLACK, $this->_FONT, $SSQ);
        imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 1445, self::BLACK, $this->_FONT,  $data["d_province"] . $data["d_city"] . $data["d_qu"].$data["d_address"]);
        imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 1410, self::BLACK, $this->_FONT, $data["d_contact"] . "（收） " . $data["d_tel"]);

        imagefttext($this->_PIC, $this->dpi300(6), 0, 900, 1510, self::BLACK, $this->_FONT, "订单号：");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 900, 1550, self::BLACK, $this->_FONT, $data["orderid"]);

       // imagefttext($this->_PIC, $this->dpi300(6), 0, 20, 1550, self::BLACK, $this->_FONT, "托寄物");
        //imagefttext($this->_PIC, $this->dpi300(6), 0, 20, 1585, self::BLACK, $this->_FONT, $data["things"]);
        //imagefttext($this->_PIC, $this->dpi300(6), 0, 20, 1695, self::BLACK, $this->_FONT, "备注");
        //imagefttext($this->_PIC, $this->dpi300(6), 0, 20, 1730, self::BLACK, $this->_FONT, $data["remark"]);
        //imagefttext($this->_PIC, $this->dpi300(6), 0, 965, 1695, self::BLACK, $this->_FONT, "费用合计：");


        //-----------------------------------------------------------------------

        //寄件方
        imagefttext($this->_PIC, $this->dpi300(6), 0, 40, 2000, self::BLACK, $this->_FONT, "寄");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 40, 2035, self::BLACK, $this->_FONT, "件");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 2035, self::BLACK, $this->_FONT, $data["j_province"] . $data["j_city"] . $data["j_qu"].$data["j_address"]);
       // imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 1370, self::BLACK, $this->_FONT, $data["j_address"]);
       // imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 1385, self::BLACK, $this->_FONT, $data["j_company"]);
        imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 2000, self::BLACK, $this->_FONT, $data["j_contact"] . "  " . $data["j_tel"]);

        //收件方
        imagefttext($this->_PIC, $this->dpi300(6), 0, 40, 2120, self::BLACK, $this->_FONT, "收");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 40, 2155, self::BLACK, $this->_FONT, "件");
        //imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 1440, self::BLACK, $this->_FONT, $SSQ);
        imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 2155, self::BLACK, $this->_FONT,  $data["d_province"] . $data["d_city"] . $data["d_qu"].$data["d_address"]);
        imagefttext($this->_PIC, $this->dpi300(6), 0, 80, 2120, self::BLACK, $this->_FONT, $data["d_contact"] . "（收） " . $data["d_tel"]);

        imagefttext($this->_PIC, $this->dpi300(6), 0, 900, 2220, self::BLACK, $this->_FONT, "订单号：");
        imagefttext($this->_PIC, $this->dpi300(6), 0, 900, 2260, self::BLACK, $this->_FONT, $data["orderid"]);


        //imagefttext($this->_PIC, $this->dpi300(6), 0, 20, 2280, self::BLACK, $this->_FONT, "托寄物");
        //imagefttext($this->_PIC, $this->dpi300(6), 0, 20, 2285, self::BLACK, $this->_FONT, $data["things"]);
        //imagefttext($this->_PIC, $this->dpi300(6), 0, 20, 2430, self::BLACK, $this->_FONT, "备注");
        //imagefttext($this->_PIC, $this->dpi300(6), 0, 20, 2430, self::BLACK, $this->_FONT, $data["remark"]);
        //imagefttext($this->_PIC, $this->dpi300(6), 0, 965, 2430, self::BLACK, $this->_FONT, "费用合计：");
        
        return $this;
    }

    function SFmailno($mailno) {
        $len = strlen($mailno);
        $mailno2 = "";
        for ($i = 0; $i < $len; $i++) {
            if ($i > 0 && $i % 3 == 0) {
                $mailno2 .= "  ";
            }
            $mailno2 .= $mailno[$i];
        }
        return $mailno2;
    }

    function SFview() {
        header("Content-Type: image/png");
        Imagejpeg($this->_PIC);
    }

    function dpi300($num) {
        return floor($num * 48 / 14);
    }

    function SFprint($filename="") {
        if ($filename == "") {
            exit();
        }
        $filestore = explode(".", $filename);
        $last = count($filestore) - 1;
        switch ($filestore[$last]) {
            case "png":
                imagepng($this->_PIC, $filename);
                break;
            case "jpeg":
            case "jpg":
                imagejpeg($this->_PIC, $filename, 95);
                break;
            case "gif":
                imagejpeg($this->_PIC, $filename, 95);
                break;
            default:
                break;
        }
    }

}


require_once("Code128/BCGDrawing.php");
include_once("Code128/BCGcode128.barcode.php");

class Code128 {

    var $SETTING = array(
        "filetype" => "PNG",
        "scale" => 4,
        "text" => "",
        "thickness" => 10
    );
    var $CD128;
    var $DW;
    var $filetypes = array('PNG' => BCGDrawing::IMG_FORMAT_PNG, 'JPEG' => BCGDrawing::IMG_FORMAT_JPEG, 'GIF' => BCGDrawing::IMG_FORMAT_GIF);

    function __construct($data) {
        $this->SETTING = array_merge($this->SETTING, $data);
    }

    function creatCode() {
        $color_black = new BCGColor(0, 0, 0);
        $color_white = new BCGColor(255, 255, 255);

        $this->CD128 = new BCGcode128();
        $this->CD128->setFont(0);
        $this->CD128->setThickness($this->SETTING["thickness"]);

        $this->CD128->setScale($this->SETTING['scale']);
        $this->CD128->setBackgroundColor($color_white);
        $this->CD128->setForegroundColor($color_black);

        if ($this->SETTING['text'] !== '') {
            $this->CD128->parse($this->SETTING['text']);
        }

        $this->DW = new BCGDrawing('', $color_white);

        $this->DW->setBarcode($this->CD128);
        $this->DW->setRotationAngle($this->SETTING['rotation']);
        $this->DW->setDPI($this->SETTING['dpi'] === 'NULL' ? null : max(72, min(300, intval($this->SETTING['dpi']))));
        $this->DW->draw();
        return $this;
    }

    function IM(){
        return $this->DW->get_im();
    }

    function saveImg($filename="") {
        if ($filename == "") {
            exit();
        }
        $filestore = explode(".", $filename);
        $last = count($filestore) - 1;
        switch ($filestore[$last]) {
            case "png":
                imagepng($this->DW->get_im(), $filename);
                break;
            case "jpeg":
            case "jpg":
                imagejpeg($this->DW->get_im(), $filename, 95);
                break;
            case "gif":
                imagejpeg($this->DW->get_im(), $filename, 95);
                break;
            default:
                break;
        }
    }

    function showView() {
        switch ($this->SETTING['filetype']) {
            case 'PNG':
                header('Content-Type: image/png');
                break;
            case 'JPEG':
                header('Content-Type: image/jpeg');
                break;
            case 'GIF':
                header('Content-Type: image/gif');
                break;
        }
        $this->DW->finish($this->filetypes[$this->SETTING['filetype']]);
    }

}

?>
