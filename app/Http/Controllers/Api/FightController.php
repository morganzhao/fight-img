<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Model\Resource;
use App\Model\ResourceTemplate;

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
    public function index(Request $request){
        $validator = Validator::make($request->all(),[
            'token'=>'required'
        ]);
        if($validator->fails()){
            showMsg(2,$validator->errors());
        }
        $resourceModel = new Resource();

        $limit = isset($request->limit)?$request->limit:10;

        $list = $resourceModel->where([])->paginate($limit)->toArray();

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
        $file_dir_path  = public_path().'/fight-img-resource/';
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

        $list = $resourceModel->where($map)->orderBy('search_num','desc')->paginate($limit)->toArray();

        showMsg(1,$list);

    }
}
