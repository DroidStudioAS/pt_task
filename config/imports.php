<?php

return [
    'orders' => [
        'name' => 'Orders Import',
        'model' => \App\Models\ImportedData::class,
        'permission_required' => 'import-orders',
        'files' => [
            'orders' => [
                'headers_to_db' => [
                    'Order Date' => 'order_date',
                    'Channel' => 'channel',
                    'SKU' => 'sku',
                    'Item Description' => 'item_description',
                    'Origin' => 'origin',
                    'SO#' => 'so_number',
                    'Total Price' => 'total_price',
                    'Cost' => 'cost',
                    'Shipping Cost' => 'shipping_cost',
                    'Profit' => 'profit',
                ],
                'types' => [
                    'order_date' => 'date',
                    'total_price' => 'decimal',
                    'cost' => 'decimal',
                    'shipping_cost' => 'decimal',
                    'profit' => 'decimal'
                ],
                'validation' => [
                    'order_date' => 'required|date',
                    'channel' => 'required',
                    'sku' => 'required',
                    'total_price' => 'required|numeric',
                ],
                'update_or_create' => [
                    'keys' => ['so_number', 'sku'],
                    'audit' => true
                ]
            ]
        ]
                ],
    // Add more import types here
    'other-orders' => [
        'name' => 'Other Orders Import',
        'model' => \App\Models\ImportedData::class,
        'permission_required' => 'import-orders',
        'files' => [
            'other-orders' => [
                'headers_to_db' => [
                    'Order Date' => 'order_date',
                    'Channel' => 'channel',
                    'SKU' => 'sku',
                    'Item Description' => 'item_description',
                    'Origin' => 'origin',
                    'SO#' => 'so_number',
                    'Total Price' => 'total_price',
                    'Cost' => 'cost',
                    'Shipping Cost' => 'shipping_cost',
                    'Profit' => 'profit',
                ],
                'types' => [
                    'order_date' => 'date',
                    'total_price' => 'decimal',
                    'cost' => 'decimal',
                    'shipping_cost' => 'decimal',
                    'profit' => 'decimal'
                ],
                'validation' => [
                    'order_date' => 'required|date',
                    'channel' => 'required',
                    'sku' => 'required',
                    'total_price' => 'required|numeric',
                ],
                'update_or_create' => [
                    'keys' => ['so_number', 'sku'],
                    'audit' => true
                ]
            ]
        ]
    ]
]; 