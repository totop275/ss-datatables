<?php

namespace Kitablog\Traits;
use Illuminate\Http\Request;

trait Datatables{
	public $column,$model,$actionBtn,$shouldSelect;

	public function list(Request $request){
		if(!is_array($this->shouldSelect)){
			$this->shouldSelect=[];
		}
		$data=[
			'draw'=>$request->input('draw',0),
			'start'=>$request->input('start',0),
			'length'=>$request->input('length',10),
			'search'=>$request->input('search',['value'=>'','regex'=>'']),
			'order'=>$request->input('order',[]),
			'columns'=>$request->input('columns',[]),
			'rule'=>$request->input('rule','and'),
			'filter'=>$request->input('filter',[]),
		];

		$column=$this->column;
		$model=$this->model;

		$query=(new $model)->newQuery();
		$column['available2']=array_map(function($var) use ($column){
			return $column['alias'][$var]??$var;
		}, $column['available']);
		$data['order']=array_filter($data['order'],function($var) use ($column,$data){
		   return in_array($data['columns'][$var['column']]['data'],$column['available']);
		});
		foreach ($data['order'] as $order) {
			$colm=$data['columns'][$order['column']]['data'];
			$query->orderByRaw($colm.' '.($order['dir']??'ASC'));
		}
		$data['columns']=array_filter($data['columns'],function($var) use ($column){
			return in_array($var['data'],$column['available']);
		});
		$data['columns']=array_map(function($var) use ($column){
			if($var['search']['value']??false){
				if(!in_array($var['data'],$column['searchable'])){
					$var['search']['value']='';
				}
			}
			return $var;
		},$data['columns']);
		foreach ($column['table'] as $table => $relation) {
			$query->leftJoin($table,...$relation);
		}
		if($data['search']['value']??false){
			if(($data['search']['regex']??'')=='true'){
				$query->where(function($query) use ($data,$column){
					foreach ($column['searchable'] as $col) {
						$col=$column['alias'][$col]??$col;
						$query->orWhereRaw($col.' REGEXP ?',$data['search']['value']);
					}
				});
			}else{
				$query->where(function($query) use ($data,$column){
					foreach ($column['searchable'] as $col) {
						$col=$column['alias'][$col]??$col;
						$query->orWhereRaw($col.' like ?','%'.$data['search']['value'].'%');
					}
				});
			}
		}
		$where=$data['rule']=='and'?'where':'orWhere';
		$select=[];
		foreach ($data['columns'] as $key => $col) {
			$columnName=$column['alias'][$col['data']]??$col['data'];
			if(($col['search']['value']??false)&&$col['search']['value']!='*'){
				$columnOperator=$col['search']['operator']??'like';
				$columnParameter=$col['search']['value'];
				if(strtolower($columnOperator)=='like'){
					$columnParameter='%'.$columnParameter.'%';
				}
				$query->{$where}.'Raw'($columnName.' '.$columnOperator.' ?',$columnParameter);
			}
			$select[]=\DB::raw($columnName.' AS '.$col['data']);
		}
		if($select){
			$query->select(array_merge($this->shouldSelect,$select));
		}else{
			$query->select($this->shouldSelect);
		}
		$data['recordsTotal']=(new $model)->newQuery()->count();
		$data['recordsFiltered']=$query->count();
		$query->skip($data['start']);
		if($data['length']>=0){
			$query->take($data['length']);
		}

		$data['data']=$query->get()->toArray();
		if(!empty($request->input('draw'))){
			if(is_array($this->actionBtn)&&$this->actionBtn){
				$btn=$this->actionBtn;
				$data['data']=array_map(function($var) use ($btn){
					$html='';
					foreach ($btn as $value) {
						array_walk($value, function(&$item,$key) use ($var){
							if($key){
								$item=$var[$item];
							}
						});
						$html.=sprintf(...$value);
					}
					$var['action']=$html;
					return $var;
				},$data['data']);
			}
		}else{
			$data=$data['data'];
		}
		return $data;
	}
}