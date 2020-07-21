# HCT-Logistics-PHP-library

### Upload Shipping information

```php
use postoor\HCTLogistics\EDIWebService;

$edi = new EDIWebService('company', 'password');

$shippingData = [
    [
        'epino' => 'O0000001',
        'ercsig' => '苗栗客家圓樓',
        'ertel1' => '037732940',
        'eraddr' => '苗栗縣後龍鎮校椅里7鄰新港三路295號',
        'ejamt' => '1',
        'eqamt' => '71',
    ],
    [
        'epino' => 'O0000002',
        'ercsig' => '彰化扇形車庫',
        'ertel1' => '047624438',
        'eraddr' => '彰化縣彰化市彰美路一段1號',
        'ejamt' => '1',
        'eqamt' => '64',
    ],
];
$data = $edi->uploadTransData($shippingData, $errorMessages);
```

#### request data
|key|desc.|max len|note|
|---| --- |  ---  | ---|
| epino | Order No | 30 | required |
| ercsig| Recipient's Name | 40 | required |
| ertel1| Recipient's Phone - 1 | 15 | required |
| ertel2| Recipient's Phone - 2 | 15 |  |
| eraddr| Recipient's Address | 100 | required |
| ejamt | Pieces |4| required, min: 1 |
| eqamt | weight(Kg) |5| required, min: 9 |
| esdate| Shipping Date |8| format: Ymd, default: Upload Date|
| escsno| 客代 |11| default |
| esstno| Shipping Station | 4 | default |
| edelno| HCT Serial No | 10 | default |
| etcsig| Shipping Name | 40 | default |
| ettel1| Shipping Phone - 1 | 15 | default |
| ettel2| Shipping Phone - 2 | 15 | default |
| etaddr| Shipping address | 100 | default |
| eddate| Designated date | 8 | default |
| eqmny | 代收貨款 | 5 | default: 0 |
| eprdct| Payment slip | 2 | default:11 (月結:11, 到付:21,現收:31) |
| emark | Remarks | 100 |  |
|eprdcl2| Product Type  | 3 | default:001 (一般:001, 冷凍:003, 冷藏:008) |

#### return Data
| Key | desc. |
| --- | ----- |
| Num | Sort Number |
| success| Y: success, R: modify, N: fail |
| edelno | HCT Serial No|
| epino | Oreder No|
| eqamt | Weight(Kg) |
| image | Tag Image |
| ErrMsg | Error Message |

### Update Shipping information
Modify weight

```php
use postoor\HCTLogistics\EDIWebService;

$edi = new EDIWebService('company', 'password');

$shippingData = [
    [
        'epino' => 'O0000001',
        'edelno' => '1001076020',
        'eqamt' => '71',
    ],
    [
        'epino' => 'O0000002',
        'edelno' => '1001076031',
        'eqamt' => '64',
    ],
];
$data = $edi->updateData($shippingData, $errorMessages);
```

#### request data
|key|desc.|max len|note|
|---| --- |  ---  | ---|
|epino| Order Serial No | 30 | required |
|edelno| HCT Serial No | 10 | required |
|eqamt| Weight(Kg) | 5 | required |

#### return Data
| Key | desc. |
| --- | ----- |
| Num | Sort Number |
| success| R: modify, N: fail |
| edelno | HCT Serial No|
| epino | Oreder No|
| eqamt | Weight(Kg) |
| ErrMsg | Error Message |

### Confirm Shipping information

```php
use postoor\HCTLogistics\EDIWebService;

$edi = new EDIWebService('company', 'password');

$shippingData = [
    [
        'epino' => 'O0000001',
        'edelno' => '1001076020'
    ],
    [
        'epino' => 'O0000002',
        'edelno' => '1001076031'
    ],
];
$data = $edi->transReport($shippingData, $errorMessages);
```

#### request data
|key|desc.|max len|note|
|---| --- |  ---  | ---|
|epino| Order Serial No | 30 | required |
|edelno| HCT Serial No | 10 | required |

#### return Data
| Key | desc. |
| --- | ----- |
| Num | Sort Number |
| success| Y: success, N: fail |
| edelno | HCT Serial No|
| ErrMsg | Error Message |

### Query Goods

```php
use postoor\HCTLogistics\Goods;
use postoor\HCTLogistics\GoodsHelper;

$iv = 'LIUALIED';
$v = '6542DFAKLJ4465465465446';
$goods = new Goods($iv, $v);

// Get Tracking History
$data = $goods->queryGoods(['6679804342']);

// Get Tracking Status Code
$goodsHelper = new GoodsHelper();
foreach ($data as $id => $value) {
        $trackData[$id] = $goodsHelper->getGoodsStatusCode($value['detail'][0]['statusString'], 1);
    }
```