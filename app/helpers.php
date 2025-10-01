<?php

if (!function_exists('spa_view')) {
    /**
     * Helper function to return appropriate view for SPA or regular requests
     *
     * @param string $regularView
     * @param string $spaView
     * @param array $data
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
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
    /**
     * Check if current request is an SPA request
     *
     * @return bool
     */
    function is_spa_request()
    {
        return request()->header('X-SPA-Request') !== null;
    }
}