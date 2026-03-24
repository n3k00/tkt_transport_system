<?php

namespace App\Filament\Widgets;

use App\Services\MerchantStockService;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class MerchantStockBalanceWidget extends Widget
{
    protected string $view = 'filament.widgets.merchant-stock-balance-widget';

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        return [
            'rows' => app(MerchantStockService::class)->getBalanceRows(),
        ];
    }
}
