<?php

namespace App\Schemas;

/**
 * @OA\Schema(
 * schema="Image",
 * title="Image",
 * description="Model Gambar",
 * @OA\Property(property="id", type="integer", description="ID unik gambar"),
 * @OA\Property(property="title", type="string", description="Judul gambar"),
 * @OA\Property(property="file_path", type="string", description="Path lokasi file gambar"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Waktu dibuat"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Waktu diperbarui")
 * )
 */
class ImageSchema
{
    // This class is only used for OpenAPI schema definition
}
