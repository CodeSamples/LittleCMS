<?php

    ob_end_clean();
    header('Content-type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . FRANCHISE_COUPON_LIST_EXPORT_FILENAME . '"');
    header('Cache-Control: max-age=0'); 
    $response->save('php://output');
    exit();
?>