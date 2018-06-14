<?php
class communicationModel extends Model{
    // function for getting communicating users list on 22-07-2016 by Mohit Kumar
    public function getCommunicateUsers($args=array()){
        $sqlArgs = array();
        $args=$this->parse_arg($args,array(
            "max_rows"=>10,
            "page"=>1,
            "order_by"=>"date",
            "order_type"=>"desc",
            'name_like'=>'',
            'client_id' => 0,
            'client_like' => '',
            'fdate_like' => '',
            'edate_like' => ''
        ));
        $order_by = array(
            'name'=>'name',
            'client_name'=>'client_name',
            'date'=>'date'
        );
        $SQL="Select name,client_name,date FROM 
            (
                Select t1.name,t2.client_name,t3.modification_date as date from d_user t1 Left Join d_client t2 ON (t1.client_id=t2.client_id) Left Join
                h_aqs_team_invite_user t3 ON (t1.user_id=t3.aqs_team_user_id) Where t3.send_email_status='1' and t3.user_type_table='d_user'
                group by t3.email
            UNION ALL
                Select t1.name,t4.client_name,t3.modification_date as date from d_AQS_team t1 Left Join d_assessment t2 ON (t1.AQS_data_id=t2.aqsdata_id)
                Left Join d_client t4 ON (t4.client_id=t2.client_id) Left Join h_aqs_team_invite_user t3 ON (t1.id=t3.aqs_team_user_id)
                Where t3.send_email_status='1' and t3.user_type_table='d_aqs_team'
                group by t3.email
            )  z Where 1 ";
        if($args['name_like']!=""){
            $SQL.="and name like '%".$args['name_like']."%' ";
            //$sqlArgs[]="%".$args['name_like']."%";
        }
        if($args['client_like']!=""){
            $SQL.="and client_name like '%".$args['client_like']."%' ";
            //$sqlArgs[]="%".$args['client_like']."%";
        }
        if($args['fdate_like']!='' && $args['edate_like']==''){
            $SQL.="and date like '%".$args['fdate_like']."%' ";
            //$sqlArgs[]="%".$args['fdata_like']."%";
        } else if($args['fdate_like']=='' && $args['edate_like']!=''){
            $SQL.="and date like '%".$args['edate_like']."%' ";
            //$sqlArgs[]="%".$args['edate_like']."%";
        } else if($args['fdate_like']!='' && $args['edate_like']!=''){
            $SQL.="and ( date Between '".$args['fdate_like']." 00:00:00' And '".$args['edate_like']." 00:00:00' ";
            //$sqlArgs[]="".$args['fdata_like']." 00:00:00"." and ".$args['edate_like']." 00:00:00"."";
            $SQL.=" Or date like '%".$args['edate_like']."%' ) ";
            //$sqlArgs[] = "%".$args['fdata_like']."%";
        }
        
        $SQL.=" group by name,client_name ";
        
        $SQL.=" order by ".(isset($order_by[$args["order_by"]])?$order_by[$args["order_by"]]:"date");
        $SQL.=$args["order_type"]=="desc"?" desc ":" asc ";
        $SQL.=$this->limit_query($args['max_rows'],$args['page']);
        $data = $this->db->get_results($SQL);
        $this->setPageCount($args['max_rows']);
        return $data;
    }
}