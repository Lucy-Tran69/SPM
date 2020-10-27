<?php
function isPending($cID,$conn)
{
    $stmt = $conn->prepare("SELECT  distinct print_type.name as type,
    customer.no as customer,
    commodity.no as commodity,
    a.seq as seq,
    maker.name as maker,
    commodity.cd as code,
    commodity.price as original,
    a.price as sp,
    a.approver as approver,
    commodity.num as qty,
    commodity.printer_support as support,
    inventory_mark.display as display,
    a.approvalday
    from selling_price a
    INNER JOIN commodity on a.commodity=commodity.no
    INNER JOIN inventory on inventory.commodity=commodity.no
    INNER JOIN maker on commodity.maker=maker.no
    INNER JOIN inventory_mark on inventory.inventory_mark=inventory_mark.no
    INNER JOIN print_type on print_type.no=commodity.print_type
    INNER JOIN customer on a.customer=customer.no		
    WHERE a.seq = (select max(seq) from selling_price b WHERE (b.approvalday IS NULL AND b.status=2) AND customer=".$cID.")
    AND commodity.invalid=0 AND maker.invalid=0 AND customer.invalid=0
    GROUP BY a.customer,a.commodity,a.num
    ORDER BY maker.name,print_type.no,code ASC");

    $result = execute($stmt,$conn);
    if($result==TRUE)
    {
        $result = $stmt->get_result();
        $resultSet = $result;
    }

    if($resultSet->num_rows > 0){
        return true;
    }
    else
    {
        return false;
    }
}
?>