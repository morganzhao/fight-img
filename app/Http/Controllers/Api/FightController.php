<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Model\Resource;
use App\Model\ResourceTemplate;
use App\Model\User;

class FightController extends Controller
{
    protected $nullClass;
    /*
     *construct
     */
    public function __construct(){
        $this->nullClass = new class{};
    }

    /*
     *home page
     */
    public function query(Request $request){
        $validator = Validator::make($request->all(),[
            'token'=>'required'
        ]);
        if($validator->fails()){
            showMsg(2,$validator->errors());
        }
        $resourceModel = new Resource();

        $limit = isset($request->limit)?$request->limit:10;

        $list = $resourceModel->where([])->paginate()->toArray();

        showMsg(1,$list);
    }

    /*
     *templates
     */
    public function chooseTemplateList(Request $request){
        $validator = Validator::make($request->all(),[
            'token'=>'required'
        ]);
        if($validator->fails()){
            showMsg(2,$validator->errors());
        }
 
        $resourceTemplateModel = new ResourceTemplate();

        $list = $resourceTemplateModel->where([])->get()->toArray();

        $list = generateTree($list);

        showMsg(1,$list);
    }

    /*
     *sync templates
     */
    public function syncTemplates(Request $request){
        $validator = Validator::make($request->all(),[
            'token'=>'required'
        ]);
        if($validator->fails()){
            showMsg(2,$validator->errors());
        }
        $file_path = public_path().'/storage/fight-img-resource';

        $file_list = scanFile($file_path);

        if($file_list){
            unset($file_list['fight-img-resource']);
        }
        $resourceTemplateModel = new ResourceTemplate();
        //syncTemplates
        $url = \Request::server()['HTTP_HOST'];
        $file_dir_path  = public_path().'/storage/fight-img-resource/';
        foreach($file_list as $k=>$v){
            $parent_data = [
                'name'=>$k,
                'file_path'=>$file_dir_path.$k,
                'download_url'=>'',
                'description'=>$k,
                'created_at'=>date('Y-m-d H:i:s')
            ];
            $res = $resourceTemplateModel->insertGetId($parent_data);
            foreach($v as $key=>$vv){
                if($vv==$k){
                    continue;
                }
                $child_data[$key] = [
                    'name'=>$vv,
                    'pid'=>$res,
                    'file_path'=>$file_dir_path.$k.'/'.$vv,
                    'download_url'=>$url.'/storage/fight-img-resource/'.$k.'/'.$vv,
                    'description'=>pathinfo($vv)['filename'],
                    'created_at'=>date('Y-m-d H:i:s')
                ];
            }
            $resourceTemplateModel->insert($child_data);
        }
        showMsg(1,$this->nullClass);
    }

    /*
     *evil templates
     */
    public function evilTemplateList(Request $request){
        print_r('22');die;
        $validator = Validator::make($request->all(),[
            'token'=>'required'
        ]);
        if($validator->fails()){
            showMsg(2,$validator->errors());
        }
        $resourceTemplateModel = new ResourceTemplate();
        $where = [
            'name'=>'魔鬼剪辑'
        ];
        $parent_data = $resourceTemplateModel->where($where)->first();
        if(!$parent_data){
            showMsg(1,[]);
        }
        $map = [
            'pid'=>$parent_data->id
        ];
        $list  = $resourceTemplateModel->where($map)->get()->toArray();

        showMsg(1,$list);
    }

    /*
     *hot search list
     */
    public function hotSearchList(Request $request){
        $validator = Validator::make($request->all(),[
            'token'=>'required'
        ]);

        if($validator->fails()){
            showMsg(2,$validator->errors());
        }

        $resourceModel = new Resource();

        $map = [
            ['search_num','>',0]
        ];

        $limit = isset($request->limit)?$request->limit:6;

        $list = $resourceModel->where($map)->orderBy('search_num','desc')->paginate($limit,['label as name'])->toArray();

        showMsg(1,$list);

    }

    /*
     *hot emoji
     */
    public function hotEmojiList(Request $request){
        $validator = Validator::make($request->all(),[
            'token'=>'required'
        ]);

        if($validator->fails()){
            showMsg(2,$validator->errors());
        }
        $resourceModel = new Resource();

        $limit = isset($request->limit)?$request->limit:9;
        
        $map = [
            ['search_num','>',0]
        ];

        $list = $resourceModel->where($map)->paginate($limit)->toArray();

        showMsg(1,$list);

    }

    /*
     *get openid by wx_code
     */
    public function wxInfo(Request $request){
        $validator = Validator::make($request->all(),[
            //'token'=>'required',
            'code'=>'required'
        ]);

        if($validator->fails()){
            showMsg(2,$validator->errors());
        }

        $appID = "wx0c768ba1da11dd9d";

        $appsecret = "933666431c2f907f5c2c51c8241219a7";

        $code = $request->code;

        $get_openid_url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appID&secret=$appsecret&js_code=$code&grant_type=authorization_code";

        $res = sCurl($get_openid_url);

        $json_arr = json_decode($res,true);

        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');

        $token = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));


        $userModel = new User();
        
        if(isset($json_arr['errcode'])){
            $json_arr['openid'] = 'HB23398464619030';
        }
        //has_user
        $user_info = $userModel->where(['openid'=>$json_arr['openid']])->first();

        if($user_info){
            $user_info = $user_info->toArray();
        }


        $data = [
            'openid'=>$json_arr['openid'],
            'token'=>$token,
            'created_at'=>date('Y-m-d H:i:s')
        ];

        if(!$user_info){
            $result = $userModel->insert($data);
        }else{
            $result = 1;
        }
        

        if($result){
            showMsg(1,$data);
        }else{
            showMsg(2,$this->nullClass);
        }
    }

    /**
     *@param token
     *@param file
     */
    public function upload(Request $request){
        if ($request->isMethod('POST')){
            $file = $request->file('file');
            //判断文件是否上传成功
            if ($file){
                //原文件名
                $originalName = $file->getClientOriginalName();
                //扩展名
                $ext = $file->getClientOriginalExtension();
                //MimeType
                $type = $file->getClientMimeType();
                //临时绝对路径
                $realPath = $file->getRealPath();
                $filename = uniqid().'.'.$ext;
                $bool = $request->file('file')->move(storage_path().'/app/public/', $filename);
                //$bool = Storage::disk('public')->put($filename,file_get_contents($realPath));
                //判断是否上传成功
                $filename = 'http://'.$_SERVER['HTTP_HOST'].'/storage/'.$filename;
                if($bool){
                    showMsg(1,['file'=>$filename],'上传成功！');
                }else{
                    showMsg(1,[],'上传成功！');
                }
            }
        }
    }

    /**
     *save img or gif
     *@param type:1图片， 2:gif
     */
    public function save(Request $request){
        $validator = Validator::make($request->all(),[
            'token'=>'required',
            'name'=>'required',
            'file'=>'required'
        ]);
        if($validator->fails()){
            showMsg(2,$validator->errors());
        }

        $resourceModel = new Resource();

        $userModel = new User();

        $user_info = $userModel->where(['token'=>$token])->first();

        if(!$user_info){
            showMsg(2,$this->nullClass);
        }

        $data = [
            'img'=>$request->file,
            'label'=>$request->name,
            'save_num'=>1,
            'userid'=>$user_info->id
        ];

        $res  = $resourceModel->insert($data);

        if($res){
            showMsg(1,$data);
        }else{
            showMsg(2,$this->nullClass);
        }
    }

    /**
     *@param sync img
     */
    public function syncImg(Request $request){
        $img_path = public_path().'/storage/img';
        if(!file_exists($img_path)){
            if(mkdir($img_path,0777)===false){
                showMsg(2,$this->nullClass());
            }
        }

        $resourceModel = new Resource();

        $list = $resourceModel->where([])->get();

        foreach($list as $v){
            $res = sCurl($v['img']);

            $filename = pathinfo($v['img'],PATHINFO_BASENAME);

            $resource = fopen($img_path.'/'.$filename,'a');

            fwrite($resource,$res);

            fclose($resource);
        }

        showMsg(1,$this->nullClass);
    }

    /**
     *@param token
     *@param name
     */
    public function makeGif(Request $request){
        $validator = Validator::make($request->all(),[
            'token'=>'required',
            'template_id'=>'required',
            // 'words'=>'required'
        ]);

        if($validator->fails()){
            showMsg(2,$validator->errors());
        }

        $resourceTemplateModel = new ResourceTemplate();

        $template = $resourceTemplateModel->where(['id'=>$request->template_id])->first();

        //$user_info = getUserInfo($request->token);

        //处理模版生成gif图片
        if($template){
            $request_time = time(true);

            $temp_root = public_path().'/storage/fight-img-resource/魔鬼剪辑/';

            $temp_ass = public_path().'/storage/ass/'.'template.ass';

            $cache_ass_path = public_path().'/storage/cache/'.'evil'.'_'.$request_time.'.ass';

            $cache_path = public_path().'/storage/cache';

            $temp_video = $temp_root.$template->description.'.mp4';

            if(!file_exists($cache_path)){
                if(mkdir($cache_path,0777)===false){
                    showMsg(2,$this->nullClass,'cache文件夹创建失败！');
                }
            }

            if(file_exists($temp_root)){
                $ass_file = file_get_contents($temp_ass);

                $tmp_arr = [
                    '这个菜',
                    '真的不是很好吃',
                    '不过我可以',
                    '让他变得很好吃,',
                    '不行你等会我做给你看看！'
                ];
                $data = isset($request->words)?$request->words:$tmp_arr;//todo

                for($i=0;$i<count($data);$i++){
                    $str_source[$i] = '<?=['.$i.']=?>';
                }
                $change_ass = str_replace($str_source,$data,$ass_file);
                $create_temporary_ass = fopen($cache_ass_path, "w") or die('{"code":501,"msg":"临时字幕文件创建失败，请网站管理员检查 `cache` 目录是否具有读写权限或用户组设否设置正确！"}');
                fwrite($create_temporary_ass, $change_ass) or die('{"code":502,"msg":"临时字幕文件已创建，但写入失败，请网站管理员检查 `cache` 目录是否具有读写权限或用户组设否设置正确！"}');
                fclose($create_temporary_ass);

                $out_put_file= $cache_path.'/'.$request_time.'.gif';
                $command = 'ffmpeg -y -i '.$temp_video.' -vf "ass='.$cache_ass_path.'" '.$out_put_file;
                system($command);

                unlink($cache_ass_path);//删除临时生成的字幕文件
                
                showMsg(1,['file'=>\Request()->server('HTTP_HOST').'/storage/cache/'.$request_time.'.gif']);
            }

        }else{
            showMsg(2,$this->nullClass);
        }

    }

    /*
     *
     */
    public function makeImgByVideo(Request $request){
        $validator = Validator::make($request->all(),[
            'token'=>'required',
            // 'words'=>'required'
        ]);

        if($validator->fails()){
            showMsg(2,$validator->errors());
        }

        $resourceTemplateModel = new ResourceTemplate();

        $list = $resourceTemplateModel->where(['pid'=>314])->get()->toArray();

        foreach($list as $v){
            //ffmpeg
            $video_path = $v['file_path'];
            $img_path = public_path().'/storage/template_imgs/'.$v['description'].'.jpg';
            $command = "ffmpeg -i {$video_path} -y -f mjpeg -ss 1 -t 1 {$img_path} ";
            exec($command);

            $img_url = \Request::server('HTTP_HOST').'/storage/template_imgs/'.$v['description'].'.jpg';
           
            $resourceTemplateModel->where(['id'=>$v['id']])->update(['img_url'=>$img_url]);
        }

        showMsg(1,$this->nullClass);

    }

    /**
     *
     *get resource detail
     */
    public function resourceTemplateInfo(Request $request){
        $validator = Validator::make($request->all(),[
            'token'=>'required',
            'id'=>'required'
            // 'words'=>'required'
        ]);

        if($validator->fails()){
            showMsg(2,$validator->errors());
        }

        $resourceTemplateModel = new ResourceTemplate();

        $template_info = $resourceTemplateModel->where(['id'=>$request->id])->first();

        showMsg(1,$template_info);
    }

    /*
     *update resourceTemplate
     */
    public function updatreResourceTemplate(Request $request){
        $validator = Validator::make($request->all(),[
            'token'=>'required',
            'id'=>'required'
            // 'words'=>'required'
        ]);

        if($validator->fails()){
            showMsg(2,$validator->errors());
        }

        $resourceTemplateModel = new ResourceTemplate();

        $list = $resourceTemplateModel->where([])->get();

        foreach($list as $v){
            //            $data['']
        }
        showMsg(1,$this->nullClass);
    }

}
