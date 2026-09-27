<?php
require_once __DIR__ . "/../config/database.php";

class InventoryItem
{
    private PDO $conn;
    public function __construct() { $this->conn = (new Database())->connect(); }

    public function search(string $district = "", string $search = "", string $category = "", string $status = "", string $sort = "name_asc"): array
    {
        $where = ["1=1"];
        $params = [];
        if ($district !== "") { $where[] = "district = :district"; $params["district"] = $district; }
        if ($search !== "") { $where[] = "item_name LIKE :search"; $params["search"] = "%{$search}%"; }
        if ($category !== "") { $where[] = "category = :category"; $params["category"] = $category; }
        if ($status === "expired") $where[] = "expiry_date IS NOT NULL AND expiry_date < CURDATE()";
        if ($status === "low") $where[] = "(expiry_date IS NULL OR expiry_date >= CURDATE()) AND quantity <= low_stock_threshold";
        if ($status === "stock") $where[] = "(expiry_date IS NULL OR expiry_date >= CURDATE()) AND quantity > low_stock_threshold";
        $orders = ["name_asc"=>"item_name ASC", "name_desc"=>"item_name DESC", "quantity_desc"=>"quantity DESC", "quantity_asc"=>"quantity ASC", "updated_desc"=>"updated_at DESC", "expiry_asc"=>"expiry_date IS NULL, expiry_date ASC"];
        $order = $orders[$sort] ?? $orders["name_asc"];
        $sql = "SELECT *, CASE WHEN expiry_date IS NOT NULL AND expiry_date < CURDATE() THEN 'Expired' WHEN quantity <= low_stock_threshold THEN 'Low stock' ELSE 'In stock' END AS item_status FROM inventory_items WHERE " . implode(" AND ", $where) . " ORDER BY {$order}";
        $stmt = $this->conn->prepare($sql); $stmt->execute($params); return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt=$this->conn->prepare("SELECT * FROM inventory_items WHERE item_id=:id"); $stmt->execute(["id"=>$id]); $row=$stmt->fetch(); return $row ?: null;
    }

    public function save(array $d, ?int $id=null): int
    {
        if ($id) {
            $sql="UPDATE inventory_items SET item_name=:item_name, category=:category, district=:district, quantity=:quantity, unit=:unit, low_stock_threshold=:low_stock_threshold, expiry_date=:expiry_date WHERE item_id=:id";
            $stmt=$this->conn->prepare($sql); $d["id"]=$id; $stmt->execute($d); return $id;
        }
        $sql="INSERT INTO inventory_items (item_name,category,district,quantity,unit,low_stock_threshold,expiry_date,created_by) VALUES (:item_name,:category,:district,:quantity,:unit,:low_stock_threshold,:expiry_date,:created_by)";
        $stmt=$this->conn->prepare($sql); $stmt->execute($d); return (int)$this->conn->lastInsertId();
    }

    public function delete(int $id, ?string $district=null): bool
    {
        $sql="DELETE FROM inventory_items WHERE item_id=:id" . ($district!==null ? " AND district=:district" : "");
        $stmt=$this->conn->prepare($sql); $params=["id"=>$id]; if($district!==null)$params["district"]=$district; $stmt->execute($params); return $stmt->rowCount()>0;
    }

    public function categories(?string $district=null): array
    {
        $sql="SELECT DISTINCT category FROM inventory_items" . ($district ? " WHERE district=:district" : "") . " ORDER BY category";
        $stmt=$this->conn->prepare($sql); $stmt->execute($district ? ["district"=>$district] : []); return array_column($stmt->fetchAll(), "category");
    }

    public function stats(?string $district=null): array
    {
        $where=$district ? " WHERE district=:district" : ""; $stmt=$this->conn->prepare("SELECT COUNT(*) total_items, COALESCE(SUM(quantity),0) total_quantity, SUM(CASE WHEN (expiry_date IS NULL OR expiry_date >= CURDATE()) AND quantity <= low_stock_threshold THEN 1 ELSE 0 END) low_stock, SUM(CASE WHEN expiry_date IS NOT NULL AND expiry_date < CURDATE() THEN 1 ELSE 0 END) expired FROM inventory_items{$where}"); $stmt->execute($district ? ["district"=>$district] : []); return $stmt->fetch();
    }

    public function adjustStock(int $id, string $type, float $amount, string $notes, int $userId, ?string $district=null): bool
    {
        $this->conn->beginTransaction();
        try {
            $sql="SELECT * FROM inventory_items WHERE item_id=:id" . ($district!==null ? " AND district=:district" : "") . " FOR UPDATE";
            $stmt=$this->conn->prepare($sql); $params=["id"=>$id]; if($district!==null)$params["district"]=$district; $stmt->execute($params); $item=$stmt->fetch();
            if(!$item) throw new RuntimeException("Inventory item not found.");
            $before=(float)$item["quantity"];
            $after=$type==="stock_in" ? $before+$amount : ($type==="distribution" ? $before-$amount : $amount);
            if($after<0) throw new RuntimeException("Not enough stock for this distribution.");
            $this->conn->prepare("UPDATE inventory_items SET quantity=:q WHERE item_id=:id")->execute(["q"=>$after,"id"=>$id]);
            $this->conn->prepare("INSERT INTO inventory_movements (item_id,movement_type,quantity,quantity_before,quantity_after,notes,performed_by) VALUES (:item_id,:type,:quantity,:before,:after,:notes,:user)")->execute(["item_id"=>$id,"type"=>$type,"quantity"=>$amount,"before"=>$before,"after"=>$after,"notes"=>$notes?:null,"user"=>$userId]);
            $this->conn->commit(); return true;
        } catch(Throwable $e) { if($this->conn->inTransaction())$this->conn->rollBack(); throw $e; }
    }
}
