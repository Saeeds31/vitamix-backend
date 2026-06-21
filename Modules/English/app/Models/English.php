<?php

namespace Modules\English\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

// use Modules\English\Database\Factories\EnglishFactory;

class English extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['model_name', 'model_row', 'value'];
    protected $table = "english";
    protected $casts = [
        'value' => 'array',
    ];

    public static function forModel(Model $model)
    {
        return self::where('model_name', class_basename($model))
            ->where('model_row', $model->getKey())
            ->first();
    }
    public static function storeForModel(Model $model, array $data)
    {
        return self::updateOrCreate(
            [
                'model_name' => class_basename($model),
                'model_row'  => $model->getKey(),
            ],
            [
                'value' => $data
            ]
        );
    }
    public static function deleteForModel(Model $model): void
    {
        $translation = self::where('model_name', class_basename($model))
            ->where('model_row', $model->getKey())
            ->first();

        if (!$translation) {
            return;
        }

        $value = $translation->value;

        if (is_array($value)) {
            foreach ($value as $item) {

                if (is_string($item)) {
                    $is_likely_path = strlen($item) < 255;

                    if ($is_likely_path) {
                        if (Storage::disk('public')->exists($item)) {
                            Storage::disk('public')->delete($item);
                        }
                    }
                }
            }
        }
        $translation->delete();
    }
    public static function applyTranslationToModel($model, string $modelName)
    {
        if (!$model) return $model;

        $translation = self::where('model_name', $modelName)
            ->where('model_row', $model->id)
            ->first();

        if ($translation) {
            foreach ($translation->value as $key => $val) {
                $model->$key = $val;
            }
        }

        return $model;
    }
    public static function applyTranslations(Collection $models, string $modelName)
    {
        if ($models->isEmpty()) {
            return $models;
        }

        $translations = self::where('model_name', $modelName)
            ->whereIn('model_row', $models->pluck('id'))
            ->get()
            ->keyBy('model_row');

        return $models->each(function ($model) use ($translations) {

            if ($translations->has($model->id)) {

                foreach ($translations[$model->id]->value as $key => $val) {
                    $model->$key = $val;
                }
            }
        });
    }
}
