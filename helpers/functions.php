<?php
function genCustomerGroup($data, $parent = 0, $text = '' ){
  $customer = '';
  foreach($data as $item){
    if($item->parent_id == $parent){
      $customer = '';
      $customer .= '<tr class="group-item" bgcolor="white" valign="middle" >';
      if ($parent == 0 || ($parent != 0 && $item->is_default == ACTIVE)) {
        $customer .= '<td></td>';
      }else{
        $customer .=  '<td><input name="selected_ids['.$item->id.'][]" type="checkbox" value="'. $text .'"  id=""></td>';
      }
      $customer .=  '<td nowrap="" align="left">';
      if ($parent != 0) {
        $customer .=  $text;
      }
      if($parent == 0 ){
        $customer .= '<img src="/svg/spacer.gif" width="8">';
        $customer .= '<img src="/svg/node.gif">';
        $customer .= '<span class="page_indent">&nbsp;&nbsp;</span>';
      }else{
        $customer .=    '<img src="/svg/tree_next.gif"> <span class="page_indent">&nbsp;</span>';
      }
      $customer .=     $item->name;
      $customer .=  '</td>';
      if ($parent == 0 || $item->is_default == ACTIVE) {
        $customer .=  '<td width="24px" align="center">';
        $customer .=    'x';
        $customer .=  '</td>';
        $customer .=  '<td width="24px" align="center">';
        $customer .=   'x';
        $customer .=  '</td>';
      }else{
        $customer .= '<td width="24px" align="center">';
        $customer .= '  <a href="#"><span class="fa fa-chevron-up" aria-hidden="true"></span></a>';
        $customer .= '</td>';
        $customer .= '<td width="24px" align="center">';
        $customer .= '<a href="#"><span class="fa fa-chevron-down" aria-hidden="true"></span></a>';
        $customer .= '</td>';
      }
      $customer .='</tr>';
      echo $customer;
      genCustomerGroup($data, $item->id, $text.' --' );
    }
  }
  // return $customer;
}
function genCustomerGroupDelete($data){
  $customer = '';
  foreach($data as $key => $item){
    $customer .='<tr class="group-item" bgcolor="white" valign="middle"  style="cursor: pointer;" id="CrmCustomerGroup_tr_442">';
    $customer .='  <td>';
    $customer .='    <input name="selected_ids[]" checked type="checkbox" value="'. $item['id'] .'"  id="">';
    $customer .='  </td>';
    $customer .='  <td nowrap="" align="left">';
    $customer .='    '. $item['sub'] .' <img src="/svg/tree_last.gif"> <span class="page_indent">&nbsp;</span>';
    $customer .='    <a href="#">'. $item['name'] .'</a>';
    $customer .='  </td>';
    $customer .='  <td width="24px" align="center">';
    $customer .='    <a href="#"><span class="fa fa-chevron-up" aria-hidden="true"></span></a>';
    $customer .='  </td>';
    $customer .='  <td width="24px" align="center">';
    $customer .='    <a href="#"><span class="fa fa-chevron-down" aria-hidden="true"></span></a> ';
    $customer .=' </td>';
    $customer .='</tr>';
  }
  echo $customer;
}

function getCurrentAdmin() {
  return Auth::guard('superadmin')->user();
}

function getCurrentUser() {
  return Auth::guard('users')->user();
}

function convertPriceToInt($price) {
  return !empty($price) ? intval(str_replace(',', '', $price)) : 0;
}

function redirectIfNotHasPermission() {
  return response()->view('admin.no_permission');
}

function redirectIfNotHasPermission2() {
  return response()->view('admin.no_permission2');
}

function queryCommonOrderReport($query, $conditions)
{
  if(isset($conditions['source_id']) && $conditions['source_id'] != null){
    $query = $query->where('source_id',$conditions['source_id']);
  }

  if(isset($conditions['upsale_from_user_id']) && $conditions['upsale_from_user_id'] != null){
    $query = $query->where('upsale_from_user_id',$conditions['upsale_from_user_id']);
  }

  if(isset($conditions['type']) && $conditions['type'] != null){
    $query = $query->where('type',$conditions['type']);
  }
  return $query;
}

function String2Stars($string='',$first=0,$last=0,$rep='*'){
  $begin  = substr($string,0,$first);
  $middle = str_repeat($rep,strlen(substr($string,$first,$last)));
  $end    = substr($string,$last);
  $stars  = $begin.$middle.$end;
  return $stars;
}
