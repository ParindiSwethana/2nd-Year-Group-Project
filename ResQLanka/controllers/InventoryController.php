<?php
require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../models/InventoryItem.php";
requireRole(["district_admin","super_admin"]);

function invRedirect(string $page, string $message="", string $type="success"): void { $q=$message!=="" ? "?message=".urlencode($message)."&type=".urlencode($type) : ""; header("Location: ../views/inventory/{$page}{$q}"); exit(); }
function invDistrict(): ?string { return ($_SESSION["role"]??"")==="district_admin" ? trim((string)($_SESSION["district"]??"")) : null; }
$model=new InventoryItem(); $action=$_GET["action"]??$_POST["action"]??""; $districtLock=invDistrict();

try {
    if($action==="save") {
        $id=(int)($_POST["item_id"]??0); $district=$districtLock ?? trim($_POST["district"]??"");
        if($id>0 && $districtLock!==null){ $old=$model->find($id); if(!$old || $old["district"]!==$districtLock) throw new RuntimeException("You can only edit inventory in your district."); }
        $data=["item_name"=>trim($_POST["item_name"]??""),"category"=>trim($_POST["category"]??""),"district"=>$district,"quantity"=>(float)($_POST["quantity"]??0),"unit"=>trim($_POST["unit"]??""),"low_stock_threshold"=>(float)($_POST["low_stock_threshold"]??0),"expiry_date"=>trim($_POST["expiry_date"]??"")?:null];
        if($data["item_name"]===""||$data["category"]===""||$data["district"]===""||$data["unit"]===""||$data["quantity"]<0||$data["low_stock_threshold"]<0) throw new RuntimeException("Please enter valid item details.");
        if($id>0) $model->save($data,$id); else { $data["created_by"]=(int)$_SESSION["user_id"]; $model->save($data); }
        invRedirect("inventory_list.php",$id>0?"Inventory item updated.":"Inventory item added.");
    }
    if($action==="delete") { $model->delete((int)($_POST["item_id"]??0),$districtLock); invRedirect("inventory_list.php","Inventory item deleted."); }
    if($action==="movement") {
        $id=(int)($_POST["item_id"]??0); $type=$_POST["movement_type"]??""; $amount=(float)($_POST["quantity"]??0); if(!in_array($type,["stock_in","distribution","adjustment"],true)||$amount<0||($amount==0&&$type!=="adjustment")) throw new RuntimeException("Enter a valid stock movement.");
        $model->adjustStock($id,$type,$amount,trim($_POST["notes"]??""),(int)$_SESSION["user_id"],$districtLock); invRedirect("stock_movements.php","Stock updated successfully.");
    }
} catch(Throwable $e){ $page=$action==="movement"?"stock_movements.php":"inventory_list.php"; invRedirect($page,$e->getMessage(),"error"); }
invRedirect("inventory_list.php");
