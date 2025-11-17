<?php

namespace app\api\controller;

use app\backend\server\VipOperationService;
use app\events\Pay;
use app\events\VodSign;
use app\model\Activity;
use app\model\ActivityRecord;
use app\model\ClubsFastadmin;
//use app\model\CopyRight\CopyrightPayInfoBaseModel;
//use app\model\CopyRight\PlaceBigDataModel;
use app\model\wechat\AdFreeModel;
use app\model\wechat\AdLogModel;
use app\model\wechat\GiftModel;
use app\model\wechat\OfficeConfigModel;
use app\model\wechat\PartyModel;
use app\model\wechat\SuperScreenModel;
use app\model\wechat\TimeAlbumModel;
use app\model\wechat\VipOperationModel;
use app\server\PayServer;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use support\Db;
use support\Redis;
use support\Request;
use yzh52521\EasyHttp\Http;

class TestController
{

    public function dealRecord($param,$status = STATUS_YES)
    {
        $arr = [
            'uid'=>$param['uid'],
            'cate'=>$param['cate'],
            'target_id'=>$param['target_id'],
            'flag'=>$status,
            'create_time'=>time()
        ];
        return AdLogModel::query()->insert($arr);
    }


    /**
     * 重庆调用
     * @return
     */

    public function demo()
    {
//        set_time_limit(0);
//
//        $redis = Redis::connection('test');
////        $redis = \redis();
////        $yesterday = date('Y-m-d', strtotime('-1 day'));
//        $time = $redis->get('time');
//
////        $yesterday = "2025-06-02";
//        $yesterday = $time;
//        $limit_time = $redis->get('limit_time');
//
//
//        if($yesterday == $limit_time){
//            return  success([],'time_is_'.$limit_time);
////            dd('时间为2025-08-13');
//        }
//
//        $datatime = [
//            'start_time' => $yesterday . ' 00:00:00',
//            'end_time' => $yesterday . ' 23:59:59',
//        ];
////        dump($datatime);
////        dd(5);
////        处理存入大屏数据统计
//        $a = $this->datacount($datatime);
////        return  success($a,'ok');
//        // 使用 Carbon 处理日期
//        $carbonDate = \Carbon\Carbon::createFromFormat('Y-m-d', $time);
//        $carbonDate->addDay();
//        $newTime = $carbonDate->format('Y-m-d');
////
//        $redis->set('time', $newTime);
//        return  success([],'ok');
    }

//    public function datacount($param)
//    {
//
//        return Db::transaction(function () use ($param) {
//            $datatime = [$param['start_time'], $param['end_time']];
////        dd($datatime);
//
//            // 本月的第一天
//            $firstDayOfMonth = date('Y-m-01 00:00:00', strtotime($param['start_time']));
//            //当年第一天
//            $firstDayOfYear = date('Y-01-01 00:00:00', strtotime($param['start_time']));
//            //年-月模型
//            // dd(date('Y-m', strtotime($param['end_time'])));
//            $copyrightPayInfoModel = new CopyrightPayInfoBaseModel();
//            // $copyrightPayInfoModel->setPartitionTable(date('Y-m'))->newQuery();
//            $copyrightPayInfoModel->setPartitionTable(date('Y-m', strtotime($param['end_time'])))->newQuery();
//            $placeBigDataModel = PlaceBigDataModel::query();
//
//            //昨日数据查询
//            $yesterday_copyright = (clone $copyrightPayInfoModel)
//                ->whereBetween('payment_time', $datatime)
//                ->select('shop_id',
//                    DB::raw('count(*) as day_order_count'),
//                    DB::raw('sum(total) as day_total'),
//                    DB::raw('sum(practical) as day_practical'),
//                    DB::raw('sum(pay_facilitator_money) as day_pay_facilitator_money'),
//                    DB::raw('sum(after_amount) as day_after_amount')
//                )
//                ->groupBy('shop_id')
//                ->get()->toArray();
//            //    dd($yesterday_copyright[0]);
//
//            //本月数据查询
//            $month_copyright = (clone $copyrightPayInfoModel)
//                ->whereBetween('payment_time', [$firstDayOfMonth, $param['end_time']])
//                ->groupBy('shop_id')
//                ->select('shop_id',
//                    DB::raw('count(*) as month_order_count'),
//                    DB::raw('sum(total) as month_total'),
//                    DB::raw('sum(practical) as month_practical'),
//                    DB::raw('sum(pay_facilitator_money) as month_pay_facilitator_money'),
//                    DB::raw('sum(after_amount) as month_after_amount')
//                )
//                ->get()->toArray();
//
//
//            //本年数据查询
//            //  查数据 按照年查最新的一条就是最新的数据统计
//            $year_copyright = (clone $placeBigDataModel)
//                ->whereBetween('created_at', [$firstDayOfYear, $param['end_time']])
//                ->select(['total_amount', 'total_service_fee', 'total_practical', 'total_after_amount', 'total_order_num', 'shop_id', 'created_at'])
//                ->whereIn('id', function($query) {
//                    $query->selectRaw('MAX(id)')
//                        ->from('place_big_data')
//                        ->groupBy('shop_id');
//                })
//                ->lockForUpdate() //共享锁
//                ->get()
//                ->toArray();
//
//            //    dd($yesterday_copyright,$month_copyright,$year_copyright);
//            // 将年数据按 shop_id 索引，方便查找
//            $year_copyright_map = [];
//            foreach ($year_copyright as $year_item) {
//                $year_copyright_map[$year_item['shop_id']] = $year_item;
//            }
//            //新增的兼容新 v2.0 begin
//            // 动态获取所有涉及店铺ID（昨日+本月）
//            $all_shop_ids = array_unique(array_merge(
//                array_column($yesterday_copyright, 'shop_id'),
//                array_column($month_copyright, 'shop_id')
//            ));
//
//            // 处理缺失店铺的年度数据
//            foreach ($all_shop_ids as $shop_id) {
//                if (!isset($year_copyright_map[$shop_id])) {
//                    $year_copyright_map[$shop_id] = [
//                        'shop_id'           => $shop_id,
//                        'total_amount'       => 0,
//                        'total_service_fee'  => 0,
//                        'total_practical'    => 0,
//                        'total_after_amount' => 0,
//                        'total_order_num'    => 0,
//                        'created_at'         => $param['end_time']
//                    ];
//                }
//            }
//            //新增的兼容新 v2.0 end
//
//
//
//            foreach ($yesterday_copyright as $yesterday_item) {
//                $shop_id = $yesterday_item['shop_id'];
//                // 如果年数据中有该 shop_id，则进行合并
//                if (isset($year_copyright_map[$shop_id])) {
//                    $year_copyright_map[$shop_id]['total_amount'] += $yesterday_item['day_total']; //2
//                    $year_copyright_map[$shop_id]['total_service_fee'] += $yesterday_item['day_pay_facilitator_money'];//4
//                    $year_copyright_map[$shop_id]['total_practical'] += $yesterday_item['day_practical'];//2
//                    $year_copyright_map[$shop_id]['total_after_amount'] += $yesterday_item['day_after_amount'];//4
//                    $year_copyright_map[$shop_id]['total_order_num'] += $yesterday_item['day_order_count'];
//                }
//            }
//            $updated_year_copyright = array_values($year_copyright_map);
//
//// 打印更新后的年度数据
//            $year_copyright = $updated_year_copyright;
//            $copyrightdata = collect(array_merge($yesterday_copyright, $month_copyright, $year_copyright))->groupBy('shop_id')->toArray();
//            //         dd($copyrightdata);
//            $res = [];
//            foreach ($copyrightdata as $key => $val) {
//                // dd($val);
//                $total = 0;
//                $practical = 0;
//                $pay_facilitator_money = 0;
//                $day_after_amount = 0;
//                $month_total = 0;
//                $month_practical = 0;
//                $month_facilitator_money = 0;
//                $month_after_amount = 0;
//                $total_amount = 0;
//                $total_practical = 0;
//                $total_service_fee = 0;
//                $total_after_amount = 0;
//
//                $day_order_num = (clone $copyrightPayInfoModel)
//                    ->whereBetween('payment_time', $datatime)
//                    ->where('shop_id', $key)
//                    // ->where('total', '>', 0)
//                    ->count();
////                echo $key . "昨日订单数" . $day_order_num . "\r\n";
//                // dd($day_order_num);
//
//
//                $month_order_num = (clone $copyrightPayInfoModel)
//                    ->whereBetween('payment_time', [$firstDayOfMonth, $param['end_time']])
//                    ->where('shop_id', $key)
//                    // ->where('total', '>', 0)
//                    ->count();
////                echo $key . "本月订单数" . $month_order_num . "\r\n";
//
////            $total_order_num = (clone $copyrightPayInfoModel)
////                ->whereBetween('payment_time', [$firstDayOfYear, $param['end_time']])
////                ->where('shop_id', $key)
////                // ->where('total', '>', 0)
////                ->count();
//
//                $total_order_num = (clone $placeBigDataModel)
//                    ->where('shop_id', $key)
//                    ->orderBy('id','desc')
//                    // ->where('total', '>', 0)
//                    ->value('total_order_num');
//                $total_order_num = $total_order_num + $day_order_num;
//
////                echo $key . "本年订单数" . $total_order_num . "\r\n";
//
//                foreach ($val as $ll => $vll) {
//                    if (Arr::get($vll, 'day_order_count')) {
//                        $total = $vll['day_total'];
//                        $practical = $vll['day_practical'];
//                        $pay_facilitator_money = $vll['day_pay_facilitator_money'];
//                        $day_after_amount = $vll['day_after_amount'];
//                        // $day_order_num         = $vll['day_order_count'];
//                    }
//                    if (Arr::get($vll, 'month_order_count')) {
//                        $month_total = $vll['month_total'];
//                        $month_practical = $vll['month_practical'];
//                        $month_facilitator_money = $vll['month_pay_facilitator_money'];
//                        $month_after_amount = $vll['month_after_amount'];
//                        // $month_order_num         = $vll['month_order_count'];
//                    }
//                    if (Arr::get($vll, 'total_order_num')) {
//                        $total_amount = $vll['total_amount'];
//                        $total_practical = $vll['total_practical'];
//                        $total_service_fee = $vll['total_service_fee'];
//                        $total_after_amount = $vll['total_after_amount'];
//                        // $total_order_num         = $vll['total_order_num'];
//                    }
//                }
//
////            $placebigdatalist = (clone $placeBigDataModel)
////                ->where('shop_id', $key)
////                ->orderBy('id', 'desc')->value('created_at')->toArray()['formatted'];
//
//                $placebigdatalist = (clone $placeBigDataModel)->where('shop_id', $key)
//                    ->select('created_at')
//                    ->orderBy('created_at', 'desc')
//                    ->lockForUpdate()//排他锁
//                    ->first();
////            dd($placebigdatalist,$param['end_time']);
//                if ($placebigdatalist['created_at']< $param['end_time']) {
//
//                    //按照年的计算  客单价 =  总金额 / 总订单数
//                    if ($total_amount > 0 && $total_order_num > 0) {
//                        $customer_unit_price = bcdiv($total_amount, $total_order_num, 2);
//                    } else {
//                        $customer_unit_price = 0;
//                    }
////                if ($day_order_num > 0) {
////                    //客单价 =  当天总金额 / 当天总订单数
////                    // $customer_unit_price = bcdiv($total, $day_order_num, 2);
////                    //按照年的计算
////                   $customer_unit_price = bcdiv($total_amount, $total_order_num, 2);
////                } else {
////                    $customer_unit_price = 0;
////                }
//                    $clubInfo = ClubsFastadmin::query()->where('code',$key)->first();
//
////                    $yjx_nums = $clubInfo->cavca_room_number??0;//音集协包房数量
////                    $nums = $day_order_num;//日均开台数 ???哪里来的
////                    //        day_order_num 订单数 /对应的code的 cavca_room_number为 音集协包房数量
////                    if($nums == 0 || $yjx_nums == 0){
////                        $day_rate = 0;
////                    }else{
////                        $day_rate = bcdiv($nums, $yjx_nums, 2);
////                    }
////
////                    if($total == 0 || $day_order_num== 0){
////                        $average_scanning_amount = 0;
////                    }else{
////                        $average_scanning_amount = bcdiv($total, $day_order_num, 2);
////                    }
//
//                    $res[] = [
//                        'shop_id' => $key,
//                        'total' => $total,
//                        'practical' => $practical,
//                        'pay_facilitator_money' => $pay_facilitator_money,
//                        'after_amount' => $day_after_amount,
//                        'day_order_num' => $day_order_num,
//                        'month_total' => $month_total,
//                        'month_practical' => $month_practical,
//                        'month_facilitator_money' => $month_facilitator_money,
//                        'month_after_amount' => $month_after_amount,
//                        'month_order_num' => $month_order_num,
//                        'total_amount' => $total_amount,
//                        'total_service_fee' => $total_service_fee,
//                        'total_practical' => $total_practical,
//                        'total_after_amount' => $total_after_amount,
//                        'total_order_num' => $total_order_num,
//                        'customer_unit_price' => $customer_unit_price,
//                        /***新增字段start***/
//                        'facilitator_id'=>$clubInfo->facilitator_id,//服务商id
//                        'province'=>$clubInfo->province,//省
//                        'city'=>$clubInfo->city,//市
//                        'county'=>$clubInfo->county,//区
//                        'box_nums'=>$clubInfo->max_client_count,//包厢数
//                        'cavca_room_number'=> $clubInfo->cavca_room_number,//包厢数
////                        'day_rate'=>$day_rate,//日均开台率
////                        'average_scanning_amount'=>$average_scanning_amount,//平均扫码金额
//                        /***新增字段end****/
//                        'created_at' => $param['end_time'],
//                    ];
//                }
//            }
////            return $res;
//            if($res){
//                try {
//                    (clone $placeBigDataModel)->insert($res);
//                } catch (\Illuminate\Database\QueryException $e) {
//                    // 更精确地捕获唯一约束冲突错误
//                    if ($e->getCode() == 23000) { // MySQL 唯一约束违反错误码
//                        Log::info('重复数据插入已忽略', ['end_time' => $param['end_time']]);
//                    } else {
//                        // 重新抛出其他异常
////                        throw $e;
//                        Log::error('插入数据失败：' . $e->getMessage());
//                    }
//                }
//
//            }else{
//                echo "没有数据插入";
//            }
//
//        }); // 结束 DB::transaction
//
//    }


    public function test(Request $request)
    {

        $reportData = [
            'stats' => [
                [
                    'word' => '二狗日报',
                    'count' => 15,
                    'titles' => [
                        [
                            'title' => '昨夜二狗在王者十连跪是道德的沦丧吗',
                            'source_name' => '科技新闻',
                            'ranks' => [1, 3],
                            'rank_threshold' => 10,
                            'url' => 'http://example.com',
                            'mobile_url' => '',
                            'time_display' => '09:00',
                            'count' => 2,
                            'is_new' => true
                        ]
                    ]
                ]
            ],
            'new_titles' => [],
            'failed_ids' => []
        ];

        $messageData = self::buildMessageData($reportData, '当日汇总');
//        dump($messageData);
//        $format =  json_encode($messageData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $URL='https://oapi.dingtalk.com/robot/send?access_token=8b1cdc6cd509df3040b638f9b12af9bd4185a956449f594e42cc7aacd7aa2228888';
        $res = Http::asJson()->post($URL,$messageData);
        $responseBody = $res->json(); // 获取响应体
        if(isset($responseBody['errcode']) && $responseBody['errcode'] === 0) {
            // 发送完全成功
            echo "消息发送成功";
        } else {
            // 发送失败，可以查看具体的错误信息
            echo "消息发送失败: " . ($responseBody['errmsg'] ?? '未知错误');
        }
        return success(['data' => $res]);

        dump($res);
//        try {
//            $a = OfficeConfigModel::find(2);
//            return success(['data' => $a]);
//        }catch (\Exception $e){
//            echo "异常已记录";
////            return response('', 302, ['Location' => 'https://www.baidu.com']);
//            $qrImg = 'https://ydlunacommon-cdn.nosdn.127.net/d4420720818e84a2cefb493dc0315992.png';//小程序二维码
//            $official_name = '视通K歌小程序';//小程序
//            return view('official/mini_program', [
//                    'qrImg' => $qrImg,
//                    'name' => $official_name
//                ]
//            );
//        }
//        $activities = VipOperationModel::query()
//            ->where('status', '<>', STATE_BACK_MONEY)
//            ->where('is_long_time', STATUS_NO)
//            ->select('id', 'status', 'start_time', 'end_time', 'create_time')
//            ->get();
//        dump($activities);
//        return success(['data' => $activities]);
//        return view('official/qrcode', [
//                'qrCodeUrl' => "https://ww2.sinaimg.cn/mw690/007ut4Uhly1hx4v37mpxcj30u017cgrv.jpg",
//                'name'=>env('OFFICIAL_NAME','视通娱LE')
//            ]
//        );
//
//
//        $qustion = "你好，VOD的施工准备有哪些步骤呢";// 海绵宝宝制造汉堡过程是怎么样的呢
//        $url = "https://api.sizhi.com/chat?appid=a733135abeca4537b05a6b22b04425c7&userid=9527&stream=true&spoken=".$qustion;
////        $url = "https://api.sizhi.com/chat?appid=a733135abeca4537b05a6b22b04425c7&userid=95278&spoken=".$qustion;
//
//        // 设置响应头
//        header('Content-Type: text/plain; charset=utf-8');
//        header('Cache-Control: no-cache');
//        header('X-Accel-Buffering: no');
//        try {
//            // 创建 Guzzle 客户端
//            $client = new Client();
//
//            // 发送流式请求
//            $response = $client->get($url, [
//                'stream' => true, // 启用流式处理
//                'timeout' => 30
//            ]);
//
//            // 获取响应体流
//            $body = $response->getBody();
//
//            // 流式读取并输出
//            while (!$body->eof()) {
//                echo $body->read(1024);
//                if (ob_get_level() > 0) {
//                    ob_flush();
//                }
//                flush();
//            }
//
//        } catch (\Exception $e) {
//            echo "Error: " . $e->getMessage();
//        }

/*

        try {
            $response = \yzh52521\EasyHttp\Http::get($url);
            $result = $response->body(); // 获取原始响应内容

            return success(json_decode($result,true));
        } catch (\Exception $e) {
            return error('请求失败: ' . $e->getMessage());
        }*/


/*
        $url = 'https://api.sizhi.com/chat?appid=a733135abeca4537b05a6b22b04425c7&userid=9527&spoken='.'你好呀，说下海绵宝宝汉堡制造过程';
//        $res = Http::get($url)->json();
        $res = Http::get($url)->json(true); // 传入 true 参数确保返回数组

        return success($res);



        $a= (new WechatController())->getDevice(['account'=>600216139]);

        return success($a);

        $param = $request->all();

        dump($param);
        switch ($param['cate']) {
            case 1:
                $model = new GiftModel();
                break;
            case 2:
                $model = new SuperScreenModel();
                break;
            case 3:
                $model = new TimeAlbumModel();
                break;
            case 4:
            default:
                $model = new PartyModel();
                break;
        }
        $info = $model->where('id', $param['target_id'])->first();
        if(empty($info)){
            return error();
        }
        $info = $info->toArray();
        $num = $info['see_num'];//需要使用得次数

        $this->dealRecord($param);

        $logNum = AdLogModel::query()
            ->where(
                [
                    'uid'=>$param['uid'],
                    'cate'=>$param['cate'],
                    'use'=>STATUS_NO
                ]
            )->count();
        if($logNum >= $num){
            $dealArr = AdLogModel::query()
                ->where(
                    [
                        'uid'=>$param['uid'],
                        'cate'=>$param['cate'],
                        'use'=>STATUS_NO
                    ])
                ->limit($num)->pluck('id')->toArray();
            AdLogModel::query()->whereIn('id',$dealArr)
                ->update(['use'=>STATUS_YES]);
            $adFree = AdFreeModel::query()
                ->where([
                    'uid'=>$param['uid'],
                    'cate'=>$param['cate'],
                    'target_id'=>$param['target_id'],
                ])->first();
            if($adFree){
                $adFree->num = $adFree->num + 1;
                $adFree->update_time = time();
                $adFree ->save();
            }else{
                AdFreeModel::query()->insert([
                    'uid'=>$param['uid'],
                    'cate'=>$param['cate'],
                    'target_id'=>$param['target_id'],
                    'num'=>1,
                    'create_time'=>time(),
                    'update_time'=>time(),
                ]);
            }
        }

        return success();*/



        /*
                $param = $request->all();
                $url = getenv('WECHAT_HOST').'/wechat/ksong_activity.html';
                $setParam = [
                    'query' => 'set',
                    'account' => $param['account'],           //云账号
                    'roomCode' => $param['room_code'],         //房间码
                    'userid' => $param['user_id'],            //用户ID
                    'code' =>  $param['code'],                //歌曲编码(低32位)
                    'codex' =>  $param['code_ex'],            //歌曲编码(高32位)
                    'activityId' => $param['activity_id'],    //活动ID
                    'activityRecordId' => $param['activity_record_id'],    //活动记录ID
                    'timestamp' => time()
                ];
                $serct = (new VodSign())->sign($setParam);
                dump($serct);
                // dd((new VodSign())->sign($setParam));
                $data = Http::asJson()->post($url, $serct)->array();
                dump($data);
                $res = [
                    'params' => (new VodSign())->sign($setParam),
                    'pushRes' => $data,

                ];
                dump($res);*/


//        $param = [
//            'amount'=>0.01,
//            'merOrderId'=>"142R20250331152429SxSTj",
//            'payment_time'=>"2025-03-31 15:24:33",
//        ];
//        $a = (new Pay())->refund($param);
//            dump($a);

//        return success(['data' => 'test123']);
    }


    /**
     * 构建钉钉机器人消息数据
     *
     * @param array $reportData 报告数据
     * @param string $reportType 报告类型
     * @param array|null $updateInfo 更新信息
     * @param string $mode 模式
     * @return array 钉钉消息数据结构
     */
    public static function buildMessageData($reportData, $reportType, $updateInfo = null, $mode = "daily") {
        $content = self::renderDingtalkContent($reportData, $updateInfo, $mode);

        return [
            "msgtype" => "markdown",
            "markdown" => [
                "title" => "TrendRadar 热点分析报告 - " . $reportType,
                "text" => $content
            ]
        ];
    }

    /**
     * 渲染钉钉内容
     *
     * @param array $reportData 报告数据
     * @param array|null $updateInfo 更新信息
     * @param string $mode 模式
     * @return string 格式化后的内容
     */
    private static function renderDingtalkContent($reportData, $updateInfo, $mode) {
        $text_content = "";
        $total_titles = 0;

        if (!empty($reportData['stats'])) {
            foreach ($reportData['stats'] as $stat) {
                if ($stat['count'] > 0) {
                    $total_titles += count($stat['titles']);
                }
            }
        }

        $text_content .= "**总新闻数：** " . $total_titles . "\n\n";
        $text_content .= "**时间：** " . date('Y-m-d H:i:s') . "\n\n";
        $text_content .= "**类型：** 热点分析报告\n\n";
        $text_content .= "---\n\n";

        if (!empty($reportData['stats'])) {
            $text_content .= "📊 **热点词汇统计**\n\n";
            $total_count = count($reportData['stats']);

            foreach ($reportData['stats'] as $i => $stat) {
                $word = $stat['word'];
                $count = $stat['count'];
                $sequence_display = "[" . ($i + 1) . "/" . $total_count . "]";

                if ($count >= 10) {
                    $text_content .= "🔥 " . $sequence_display . " **" . $word . "** : **" . $count . "** 条\n\n";
                } elseif ($count >= 5) {
                    $text_content .= "📈 " . $sequence_display . " **" . $word . "** : **" . $count . "** 条\n\n";
                } else {
                    $text_content .= "📌 " . $sequence_display . " **" . $word . "** : " . $count . " 条\n\n";
                }

                foreach ($stat['titles'] as $j => $title_data) {
                    $formatted_title = self::formatTitleForPlatform("dingtalk", $title_data, true);
                    $text_content .= "  " . ($j + 1) . ". " . $formatted_title . "\n";

                    if ($j < count($stat['titles']) - 1) {
                        $text_content .= "\n";
                    }
                }

                if ($i < count($reportData['stats']) - 1) {
                    $text_content .= "\n---\n\n";
                }
            }
        }

        if (empty($reportData['stats'])) {
            if ($mode == "incremental") {
                $mode_text = "增量模式下暂无新增匹配的热点词汇";
            } elseif ($mode == "current") {
                $mode_text = "当前榜单模式下暂无匹配的热点词汇";
            } else {
                $mode_text = "暂无匹配的热点词汇";
            }
            $text_content .= "📭 " . $mode_text . "\n\n";
        }

        if (!empty($reportData['new_titles'])) {
            if ($text_content && !strpos($text_content, "暂无匹配")) {
                $text_content .= "\n---\n\n";
            }

            $total_new_count = 0;
            foreach ($reportData['new_titles'] as $source_data) {
                $total_new_count += count($source_data['titles']);
            }

            $text_content .= "🆕 **本次新增热点新闻** (共 " . $total_new_count . " 条)\n\n";

            foreach ($reportData['new_titles'] as $source_data) {
                $text_content .= "**" . $source_data['source_name'] . "** (" . count($source_data['titles']) . " 条):\n\n";

                foreach ($source_data['titles'] as $j => $title_data) {
                    $title_data_copy = $title_data;
                    $title_data_copy['is_new'] = false;
                    $formatted_title = self::formatTitleForPlatform("dingtalk", $title_data_copy, false);
                    $text_content .= "  " . ($j + 1) . ". " . $formatted_title . "\n";
                }

                $text_content .= "\n";
            }
        }

        if (!empty($reportData['failed_ids'])) {
            if ($text_content && !strpos($text_content, "暂无匹配")) {
                $text_content .= "\n---\n\n";
            }

            $text_content .= "⚠️ **数据获取失败的平台：**\n\n";
            foreach ($reportData['failed_ids'] as $i => $id_value) {
                $text_content .= "  • **" . $id_value . "**\n";
            }
        }

        $text_content .= "\n\n> 更新时间：" . date('Y-m-d H:i:s');

        if ($updateInfo) {
            $text_content .= "\n> TrendRadar 发现新版本 **" . $updateInfo['remote_version'] . "**，当前 " . $updateInfo['current_version'];
        }

        return $text_content;
    }

    /**
     * 格式化标题用于不同平台
     *
     * @param string $platform 平台名称
     * @param array $titleData 标题数据
     * @param bool $showSource 是否显示来源
     * @return string 格式化后的标题
     */
    private static function formatTitleForPlatform($platform, $titleData, $showSource) {
        $rank_display = self::formatRankDisplay(
            $titleData['ranks'],
            $titleData['rank_threshold'],
            $platform
        );

        $link_url = !empty($titleData['mobile_url']) ? $titleData['mobile_url'] : $titleData['url'];
        $cleaned_title = self::cleanTitle($titleData['title']);

        if ($link_url) {
            $formatted_title = "[" . $cleaned_title . "](" . $link_url . ")";
        } else {
            $formatted_title = $cleaned_title;
        }

        $title_prefix = !empty($titleData['is_new']) ? "🆕 " : "";

        if ($showSource) {
            $result = "[" . $titleData['source_name'] . "] " . $title_prefix . $formatted_title;
        } else {
            $result = $title_prefix . $formatted_title;
        }

        if ($rank_display) {
            $result .= " " . $rank_display;
        }

        if (!empty($titleData['time_display'])) {
            $result .= " - " . $titleData['time_display'];
        }

        if ($titleData['count'] > 1) {
            $result .= " (" . $titleData['count'] . "次)";
        }

        return $result;
    }

    /**
     * 格式化排名显示
     *
     * @param array $ranks 排名数组
     * @param int $rankThreshold 阈值
     * @param string $platform 平台
     * @return string 格式化后的排名
     */
    private static function formatRankDisplay($ranks, $rankThreshold, $platform) {
        if (empty($ranks)) {
            return "";
        }

        $unique_ranks = array_unique($ranks);
        sort($unique_ranks);
        $min_rank = $unique_ranks[0];
        $max_rank = $unique_ranks[count($unique_ranks) - 1];

        if ($platform == "dingtalk") {
            $highlight_start = "**";
            $highlight_end = "**";
        } else {
            $highlight_start = "**";
            $highlight_end = "**";
        }

        if ($min_rank <= $rankThreshold) {
            if ($min_rank == $max_rank) {
                return $highlight_start . "[" . $min_rank . "]" . $highlight_end;
            } else {
                return $highlight_start . "[" . $min_rank . " - " . $max_rank . "]" . $highlight_end;
            }
        } else {
            if ($min_rank == $max_rank) {
                return "[" . $min_rank . "]";
            } else {
                return "[" . $min_rank . " - " . $max_rank . "]";
            }
        }
    }

    /**
     * 清理标题
     *
     * @param string $title 标题
     * @return string 清理后的标题
     */
    private static function cleanTitle($title) {
        if (!is_string($title)) {
            $title = strval($title);
        }

        $cleaned_title = str_replace(["\n", "\r"], " ", $title);
        $cleaned_title = preg_replace('/\s+/', ' ', $cleaned_title);
        return trim($cleaned_title);
    }




}
