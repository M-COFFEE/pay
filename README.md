```        //富有app支付
        $pay = new PayMemberFactory();
        $payWay=new FuYouAppPay();
        //设置密钥和配置文件 可以是配置文件路径 或 数组
        //        [
        //            "mchnt_cd" => "",//商户代码
        //            "back_notify_url" => "",//回调地址
        //            "pay_pc_url" => "https://aggpcpay.fuioupay.com/aggpos/order.fuiou",//pc扫码支付地址
        //            "pay_app_url" => "https://aggapp.fuioupay.com/token.fuiou",//app支付地址
        //            "rsa_private_key" => "",//商户私钥
        //            "rsa_public_key" => "",//富友公钥
        //        ];
        $payWay->setConfig(public_path().'fuYou.php');
        //生成订单号
        $orderNumber = create_order_number(102);
        $orderNumber='mhy'.$orderNumber;

        $payData= [
            'order_amt' => 1,//支付金额
            'order_id'=>$orderNumber,
        ];
        //设置支付类
        $pay->setPaymentStrategy($payWay);
        //返回参数app使用官方sdk调用支付。支付方式由app决定。后端只负责生成token加密数据
        $pay->processPayment($payData);


        //富有pc扫码支付
        $pay = new PayMemberFactory();
        $payWay=new FuYouPcPay();
        $payWay->setConfig(public_path().'fuYou.php');
        $orderNumber = create_order_number(102);
        $orderNumber='mhy'.$orderNumber;
        $payData= [
            'order_amt' => 1,
            'order_id'=> $orderNumber,
            'type'=>'WECHAT',//微信 WECHAT 支付宝 ALIPAY
        ];
        $pay->setPaymentStrategy($payWay);
        dd($pay->processPayment($payData));
        
        //获取回调
        $call=new FuYouCallback();
        //设置配置文件
        $call->setConfig(public_path().'fuYou.php');
        $pay->setPaymentCallback($call);
        $res=$pay->processPaymentCallback();
        dd($res);```
