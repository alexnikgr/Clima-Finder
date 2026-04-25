<?php

require_once __DIR__ . '/constants.php';

function getP($k,$d=null){return $_POST[$k]??$d;}
function isS($k,$v,$df=false){if(!isset($_POST[$k])&&$df)return"selected";return(isset($_POST[$k])&&$_POST[$k]==$v)?"selected":"";}

$mode=getP('mode','cooling');
$area=floatval(getP('area',25));
$height=floatval(getP('height',2.8));
$volume=$area*$height;

$people=intval(getP('people',2));
$equipment=intval(getP('equipment',1));
$zone=getP('zone','b');

if($mode==='cooling'){
    $tout=getP('custom_tout',$DESIGN_CONDITIONS[$zone]['cooling']['tdb']);
    $tin=getP('custom_tin',$T_IN_DEFAULT['cooling']);
}else{
    $tout=$DESIGN_CONDITIONS[$zone]['heating']['tdb'];
    $tin=$T_IN_DEFAULT['heating'];
}

$dt=$tout-$tin;

$rh_out=floatval(getP('rh_out',50));
$rh_in=floatval(getP('rh_in',50));

function W($T,$RH,$p){
    $pws=610.78*exp(($T*17.27)/($T+237.3));
    $pw=$RH/100*$pws;
    return 0.622*($pw/($p-$pw));
}

$W_out=W($tout,$rh_out,$PSYCHRO['p_atm']);
$W_in=W($tin,$rh_in,$PSYCHRO['p_atm']);

$m_sf_cool=floatval(getP('m_sf_cool',1.10));
$m_sf_heat=floatval(getP('m_sf_heat',1.20));
$m_latent=floatval(getP('m_latent',1.30));

$m_people_w=floatval(getP('m_people_w',100));
$m_equip_w=floatval(getP('m_equip_w',150));

$roof_type=getP('roof_type','terrace');
$roof_color=getP('roof_color','dark');
$roof_shade=getP('roof_shade','none');
$floor_type=getP('floor_type','ground');

$tc_w=0;$tc_surf=0;$ts_w=0;$inf=0;$internal=0;$latent=0;

$etos=getP('etos','legacy');

function Uwall($etos,$lambda_id,$dcm,$U_WALL,$LAMBDA){
    $u0=$U_WALL[$etos];
    if($lambda_id==='none'||$dcm<=0)return$u0;
    $λ=$LAMBDA[$lambda_id]['lambda'];
    $d=$dcm/100;
    $R0=1/$u0;
    $Rins=$d/$λ;
    return 1/($R0+$Rins);
}

$SOLAR=['north'=>0.10,'south'=>1.00,'east'=>0.75,'west'=>0.85];
$DIR=['north','south','east','west'];

foreach($DIR as $id){

    $type=getP("w_type_$id","external");
    $len=floatval(getP("w_len_$id",0));
    $Aw=$len*$height;

    if($type==='internal'){
        continue;
    }

    $monosi_id=getP("monosi_$id","none");
    $depth_cm=floatval(getP("ins_depth_$id",0));
    $u_wall=Uwall($etos,$monosi_id,$depth_cm,$U_WALL,$LAMBDA);

    $tc_w+=$Aw*$u_wall*$dt;

    $std=intval(getP("win_std_$id",0));
    $pat=intval(getP("win_patio_$id",0));

    if($std+$pat>0){

        $glass=getP("glass_$id","double");
        $frame=getP("frame_$id","alum");

        $ug=$TZAMI[$glass]['u'];
        $uf=$KOUFOMATA[$frame]['u'];

        $u_win=$ug*0.8+$uf*0.2;

        $Awin=$std*1.2+$pat*2.4;

        $tc_w+=$Awin*$u_win*$dt;

        $g=$TZAMI[$glass]['g'];
        $shade=floatval(getP("shade_$id",1.0));
        $sf=$SOLAR[$id]*$shade;

        $ts_w+=$Awin*$g*$sf*180;
    }
}

$roof_ins=getP('roof_ins','none');
$roof_ins_depth=floatval(getP('roof_ins_depth',0));

switch($roof_type){
    case'terrace':$u0r=1.20;break;
    case'pitched':$u0r=0.80;break;
    default:$u0r=0.40;
}

$u_r=Uwall('legacy',$roof_ins,$roof_ins_depth,['legacy'=>$u0r],$LAMBDA);

$tc_surf+=$area*$u_r*$dt;

if($mode==='cooling'&&$roof_type!=='heated_above'){
    $α0=$ROOF_SOLAR[$roof_type][$roof_color]['alpha'];
    if($roof_shade==='strong')$α=0.15;
    elseif($roof_shade==='partial')$α=($α0+0.15)/2;
    else $α=$α0;
    $ts_w+=$area*650*$α;
}

switch($floor_type){
    case'ground':$u_f=0.60;break;
    case'pilotis':$u_f=1.20;break;
    default:$u_f=0.40;
}

$tc_surf+=$area*$u_f*$dt;

$ach=0.8;$ρ=1.2;$cp=1005;

$inf_s=$ach*$volume*$ρ*$cp*$dt/3600;
$m_dot=$ach*$volume*$ρ/3600;
$latent_inf=$m_dot*($W_out-$W_in)*$PSYCHRO['h_fg'];

$inf=$inf_s;

$internal=$people*$m_people_w+$equipment*$m_equip_w;

$latent=($people*55+$latent_inf)*$m_latent;

$S=$tc_w+$tc_surf+$ts_w+$inf+$internal;
$T=$S+$latent;

$shr=$S/max($T,1);

if($mode==='cooling')$T*=$m_sf_cool;
else $T*=$m_sf_heat;

$btu=$T*3.412;
$kw=$T/1000;

$result=[
    'btu'=>$btu,
    'kw'=>$kw,
    'tout'=>$tout,
    'tin'=>$tin,
    'tc_w'=>$tc_w,
    'tc_surf'=>$tc_surf,
    'ts_w'=>$ts_w,
    'inf'=>$inf,
    'internal'=>$internal,
    'latent_total'=>$latent,
    'shr'=>$shr
];

?>