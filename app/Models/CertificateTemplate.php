<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'name',
        'background_path',
        'canvas_width',
        'canvas_height',
        'font_family',
        'fields_config',
        'is_active',
    ];

    protected $casts = [
        'fields_config' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active template.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::creating(function ($template) {
            if (empty($template->fields_config)) {
                $template->fields_config = self::getDefaultFieldsConfig();
            }
        });
    }

    /**
     * Return default coordinate config for certificate elements (in percentage).
     */
    public static function getDefaultFieldsConfig()
    {
        return [
            'certificate_number' => [
                'top' => 15,
                'left' => 50,
                'font_size' => 14,
                'font_weight' => 'normal',
                'text_align' => 'center',
                'color' => '#64748b',
                'is_visible' => true,
            ],
            'name' => [
                'top' => 38,
                'left' => 50,
                'font_size' => 34,
                'font_weight' => 'bold',
                'text_align' => 'center',
                'color' => '#1d4ed8', // Brand Primary
                'is_visible' => true,
            ],
            'nim' => [
                'top' => 45,
                'left' => 50,
                'font_size' => 16,
                'font_weight' => 'normal',
                'text_align' => 'center',
                'color' => '#475569',
                'is_visible' => true,
            ],
            'faculty' => [
                'top' => 53,
                'left' => 50,
                'font_size' => 16,
                'font_weight' => 'normal',
                'text_align' => 'center',
                'color' => '#475569',
                'is_visible' => true,
            ],
            'major' => [
                'top' => 59,
                'left' => 50,
                'font_size' => 20,
                'font_weight' => 'bold',
                'text_align' => 'center',
                'color' => '#0f172a',
                'is_visible' => true,
            ],
            'degree' => [
                'top' => 65,
                'left' => 50,
                'font_size' => 18,
                'font_weight' => 'bold',
                'text_align' => 'center',
                'color' => '#0f172a',
                'is_visible' => true,
            ],
            'graduation_date' => [
                'top' => 72,
                'left' => 50,
                'font_size' => 14,
                'font_weight' => 'normal',
                'text_align' => 'center',
                'color' => '#0f172a',
                'is_visible' => true,
            ],
            'qr_code' => [
                'top' => 78,
                'left' => 80,
                'size' => 80,
                'is_visible' => true,
            ],
        ];
    }
}
