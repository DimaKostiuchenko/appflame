<?php

namespace App\Enums;

enum EventType: string
{
    case PAGE_VIEW = 'page_view';
    case CTA_CLICK = 'cta_click';
    case FORM_SUBMIT = 'form_submit';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
