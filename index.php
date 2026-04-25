<?php 
require_once __DIR__ . '/includes/constants.php';
require_once __DIR__ . '/includes/engine.php';
?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HVAC Pro v41</title>

<?php $accent=($mode==='cooling')?"#007AFF":"#FF9500"; ?>

<link rel="stylesheet" href="css/style.css">

<style>
:root{
--accent:<?= $accent ?>;
--accent_low:<?= $accent ?>22;
--accent_glow:<?= $accent ?>44;
--card:#1c1c1e;
--bg:#000;
--label:#8e8e93;
}
</style>
</head>
<body>

<form method="POST" class="grid">

<div class="box hero">
<label>Συνολικό φορτίο (BTU/h)</label>
<h1><?= $result?number_format($result['btu'],0):'0' ?></h1>

<div style="display:flex;flex-wrap:wrap;gap:40px;margin-top:20px;border-top:1px solid rgba(255,255,255,0.1);padding-top:15px;">

<div>
<label>Απαιτούμενη ισχύς (kW)</label>
<span style="font-size:1.2rem;font-weight:600;color:<?= $accent ?>;">
<?= number_format($result['kw']??0,2) ?> kW
</span>
</div>

<div>
<label>Θερμοκρασία έξω/μέσα</label>
<span style="font-size:1.2rem;font-weight:600;">
<?= $result['tout']??'--' ?>° / <?= $result['tin']??'--' ?>°
</span>
</div>

<div>
<label>Λανθάνον φορτίο</label>
<span style="font-size:1.2rem;font-weight:600;color:#BF5AF2;">
<?= number_format($result['latent_total']??0,0) ?> W
</span>
</div>

<div>
<label>SHR</label>
<span style="font-size:1.2rem;font-weight:600;color:#FFD60A;">
<?= number_format($result['shr']??0,2) ?>
</span>
</div>

</div>
</div>

<div class="box" style="grid-column:span 3;">
<label>Τύπος λειτουργίας</label>
<select name="mode" onchange="this.form.submit()">
<option value="cooling" <?= isS('mode','cooling',true)?>>❄️ Ψύξη</option>
<option value="heating" <?= isS('mode','heating')?>>🔥 Θέρμανση</option>
</select>

<label>Κλιματική περιοχή</label>
<select name="zone" onchange="this.form.submit()">
<?php foreach($CLIMATE_ZONES as $id=>$v)echo"<option value='$id' ".isS('zone',$id).">{$v['label']}</option>";?>
</select>

<div style="margin-top:15px;padding-top:10px;border-top:1px solid rgba(255,255,255,0.1);">
<label style="display:flex;align-items:center;gap:8px;cursor:pointer;text-transform:none;font-size:0.75rem;">
<input type="checkbox" name="use_override" value="1" <?= isset($_POST['use_override'])?'checked':'' ?> onchange="this.form.submit()" style="width:auto;margin:0;">
Χειροκίνητες ρυθμίσεις
</label>
</div>

<button type="submit" class="btn">ΥΠΟΛΟΓΙΣΜΟΣ</button>
</div>

<?php if(isset($_POST['use_override'])): ?>
<div class="section-title">Χειροκίνητες ρυθμίσεις</div>

<div class="box" style="grid-column:span 12;display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:15px;border:1px solid var(--accent);">

<div><label>Θερμοκρασία έξω</label><input type="number" step="0.5" name="custom_tout" value="<?= getP('custom_tout',$result['tout']??35) ?>"></div>
<div><label>Θερμοκρασία μέσα</label><input type="number" step="0.5" name="custom_tin" value="<?= getP('custom_tin',26) ?>"></div>
<div><label>RH έξω</label><input type="number" name="rh_out" value="<?= getP('rh_out',50) ?>"></div>
<div><label>RH μέσα</label><input type="number" name="rh_in" value="<?= getP('rh_in',50) ?>"></div>
<div><label>m_latent</label><input type="number" step="0.01" name="m_latent" value="<?= getP('m_latent',1.30) ?>"></div>
<div><label>SF Ψύξης</label><input type="number" step="0.01" name="m_sf_cool" value="<?= getP('m_sf_cool',1.10) ?>"></div>
<div><label>SF Θέρμανσης</label><input type="number" step="0.01" name="m_sf_heat" value="<?= getP('m_sf_heat',1.20) ?>"></div>

</div>
<?php endif; ?>

<div class="section-title">Γεωμετρία & χαρακτηριστικά</div>

<div class="box" style="grid-column:span 12;display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:15px;">

<div><label>Τετραγωνικά</label><input type="number" name="area" value="<?= getP('area',25) ?>"></div>
<div><label>Ύψος</label><input type="number" step="0.1" name="height" value="<?= getP('height',2.8) ?>"></div>
<div><label>Άτομα</label><input type="number" name="people" value="<?= getP('people',2) ?>"></div>
<div><label>Συσκευές</label><input type="number" name="equipment" value="<?= getP('equipment',1) ?>"></div>

<div>
<label>Έτος</label>
<select name="etos" onchange="this.form.submit()">
<?php foreach($ETOS_LABELS as $id=>$l)echo"<option value='$id' ".isS('etos',$id).">$l</option>";?>
</select>
</div>

<div>
<label>Μόνωση οροφής</label>
<select name="roof_ins">
<?php foreach($LAMBDA as $id=>$v)echo"<option value='$id' ".isS('roof_ins',$id).">{$v['label']}</option>";?>
</select>
</div>

<div><label>Πάχος οροφής (cm)</label><input type="number" name="roof_ins_depth" value="<?= getP('roof_ins_depth',5) ?>"></div>

<div>
<label>Τύπος οροφής</label>
<select name="roof_type">
<option value="terrace" <?= isS('roof_type','terrace',true)?>>Δώμα</option>
<option value="pitched" <?= isS('roof_type','pitched')?>>Κεραμοσκεπή</option>
<option value="heated_above" <?= isS('roof_type','heated_above')?>>Θερμαινόμενος από πάνω</option>
</select>
</div>

<div>
<label>Χρώμα οροφής</label>
<select name="roof_color">
<option value="dark" <?= isS('roof_color','dark',true)?>>Σκούρα</option>
<option value="white" <?= isS('roof_color','white')?>>Λευκή</option>
</select>
</div>

<div>
<label>Σκίαση οροφής</label>
<select name="roof_shade">
<?php foreach($ROOF_SHADING as $id=>$v)echo"<option value='$id' ".isS('roof_shade',$id).">{$v['label']}</option>";?>
</select>
</div>

<div>
<label>Δάπεδο</label>
<select name="floor_type">
<option value="ground" <?= isS('floor_type','ground',true)?>>Έδαφος</option>
<option value="pilotis" <?= isS('floor_type','pilotis')?>>Πιλοτή</option>
<option value="heated_below" <?= isS('floor_type','heated_below')?>>Θερμαινόμενο από κάτω</option>
</select>
</div>

</div>

<div class="section-title">Προσανατολισμοί</div>

<?php 
$ORDER=['north','south','east','west'];
foreach($ORDER as $id):
$p=$DIRECTIONS[$id];
?>
<div class="box" style="grid-column:span 3;border-top:4px solid <?= $id=='north'?'#5E5CE6':($id=='south'?'#FFD60A':$accent) ?>;">

<label>🧭 <?= $p['label'] ?></label>

<label>Τύπος τοίχου</label>
<select name="w_type_<?= $id ?>" onchange="this.form.submit()">
<option value="external" <?= isS("w_type_$id",'external',true)?>>Εξωτερικός</option>
<option value="internal" <?= isS("w_type_$id",'internal')?>>Εσωτερικός</option>
</select>

<label>Μήκος (m)</label>
<input type="number" step="0.1" name="w_len_<?= $id ?>" value="<?= getP("w_len_$id") ?>">

<label>Μόνωση</label>
<select name="monosi_<?= $id ?>">
<?php foreach($LAMBDA as $lid=>$lv)echo"<option value='$lid' ".isS("monosi_$id",$lid).">{$lv['label']}</option>";?>
</select>

<label>Πάχος (cm)</label>
<input type="number" name="ins_depth_<?= $id ?>" value="<?= getP("ins_depth_$id",5) ?>">

<?php if(getP("w_type_$id",'external')=='external'): ?>

<label>Υαλοπίνακας</label>
<select name="glass_<?= $id ?>">
<?php foreach($TZAMI as $tid=>$tv)echo"<option value='$tid' ".isS("glass_$id",$tid).">{$tv['label']}</option>";?>
</select>

<label>Πλαίσιο</label>
<select name="frame_<?= $id ?>">
<?php foreach($KOUFOMATA as $fid=>$fv)echo"<option value='$fid' ".isS("frame_$id",$fid).">{$fv['label']}</option>";?>
</select>

<div style="display:flex;gap:5px;">
<div style="flex:1;">
<label>Παράθυρα</label>
<input type="number" name="win_std_<?= $id ?>" value="<?= getP("win_std_$id") ?>">
</div>
<div style="flex:1;">
<label>Μπαλκονόπορτες</label>
<input type="number" name="win_patio_<?= $id ?>" value="<?= getP("win_patio_$id") ?>">
</div>
</div>

<label>Σκίαση</label>
<select name="shade_<?= $id ?>">
<option value="1.0" <?= isS("shade_$id","1.0",true)?>>Καμία</option>
<option value="0.7" <?= isS("shade_$id","0.7")?>>Μερική</option>
<option value="0.4" <?= isS("shade_$id","0.4")?>>Τέντα</option>
<option value="0.2" <?= isS("shade_$id","0.2")?>>Ισχυρή</option>
</select>

<?php endif; ?>

</div>
<?php endforeach; ?>

<div class="box" style="grid-column:span 12;">
<label>Ανάλυση φορτίων</label>

<?php if($result):
$sum=array_sum([
$result['tc_w'],
$result['tc_surf'],
$result['ts_w'],
$result['inf'],
$result['internal'],
$result['latent_total']
]);
$sum=$sum>0?$sum:1;
?>

<div class="analysis-bar">
<div style="width:<?=($result['tc_w']/$sum)*100?>%;background:var(--accent);"></div>
<div style="width:<?=($result['tc_surf']/$sum)*100?>%;background:#FF9500;"></div>
<div style="width:<?=($result['ts_w']/$sum)*100?>%;background:#FF375F;"></div>
<div style="width:<?=($result['inf']/$sum)*100?>%;background:#32D74B;"></div>
<div style="width:<?=($result['internal']/$sum)*100?>%;background:#BF5AF2;"></div>
<div style="width:<?=($result['latent_total']/$sum)*100?>%;background:#FFD60A;"></div>
</div>

<div class="analysis-legend">
<span><b style="color:var(--accent)">■</b> Τοίχοι</span>
<span><b style="color:#FF9500">■</b> Οροφή/Δάπεδο</span>
<span><b style="color:#FF375F">■</b> Ηλιακά</span>
<span><b style="color:#32D74B">■</b> Αερισμός</span>
<span><b style="color:#BF5AF2">■</b> Εσωτερικά</span>
<span><b style="color:#FFD60A">■</b> Λανθάνον</span>
</div>

<?php endif; ?>
</div>

<div class="disclaimer-box">
<h3 style="margin:0 0 10px 0;font-size:1rem;color:var(--accent);">⚖️ Σημείωση</h3>
<p style="font-size:0.72rem;color:#8e8e93;line-height:1.5;margin:0;">
Οι υπολογισμοί αποτελούν προεκτίμηση και δεν αντικαθιστούν μελέτη μηχανικού.
</p>
</div>

</form>

</body>
</html>