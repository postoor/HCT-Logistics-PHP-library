<?php

namespace postoor\HCTLogistics;

class GoodsHelper
{
    /**
     * Get Goods Status Code.
     *
     * @return string
     */
    public function getGoodsStatusCode(string $statusString, $pieces)
    {
        $info = preg_match('/貨件已由\W+。貨物件數共(\d+)件。/', $statusString);
        if ($info) {
            return $info == $pieces ? 'delivered' : 'in_transit';
        }

        if (preg_match('/貨件已抵達\W+貨件整理中。貨物件數共(\d+)件。/', $statusString)) {
            return 'picked_up';
        }

        return preg_match('/(貨件由|貨件已抵達|貨件由永和|貨件已由)\W+(分貨中|配送中|前往配送站途中)。/', $statusString)
            ? 'in_transit'
            : 'unknown';
    }
}
