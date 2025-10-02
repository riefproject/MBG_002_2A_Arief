<?php

if (!function_exists('spa_view')) {
    /* 
     * Fungsi ini membantu mengembalikan view yang sesuai berdasarkan jenis permintaan (SPA atau reguler).
     * Jika permintaan adalah SPA (ditandai dengan header 'X-SPA-Request'), maka view khusus SPA akan dikembalikan.
     * Jika tidak, view reguler akan dikembalikan.    
    */
    function spa_view($regularView, $spaView = null, $data = [])
    {
        $isSpaRequest = request()->header('X-SPA-Request');
        
        if ($isSpaRequest && $spaView) {
            return view($spaView, $data);
        }
        
        return view($regularView, $data);
    }
}

if (!function_exists('is_spa_request')) {
    function is_spa_request()
    {
        return request()->header('X-SPA-Request') !== null;
    }
}