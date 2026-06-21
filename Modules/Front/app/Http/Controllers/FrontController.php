<?php

namespace Modules\Front\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Articles\Models\Article;
use Modules\Banners\Models\Banner;
use Modules\Categories\Models\Category;
use Modules\English\Models\English;
use Modules\Menus\Models\Menu;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductVariant;
use Modules\Settings\Models\Setting;
use Modules\Sliders\Models\Slider;

class FrontController extends Controller
{
    // app/Http/Controllers/ProductController.php

    public function priceRange(): array
    {
        // کمترین/بیشترین قیمت در جدول products
        $minProductPrice = Product::min('price');
        $maxProductPrice = Product::max('price');

        // کمترین/بیشترین قیمت در جدول variants
        $minVariantPrice = ProductVariant::min('price');
        $maxVariantPrice = ProductVariant::max('price');

        // محاسبه‌ی نهایی با در نظر گرفتن مقدار null
        $min = null;
        $max = null;
        if ($minProductPrice !== null && $minVariantPrice !== null) {
            $min = min($minProductPrice, $minVariantPrice);
        } elseif ($minProductPrice !== null) {
            $min = $minProductPrice;
        } else {
            $min = $minVariantPrice;
        }

        if ($maxProductPrice !== null && $maxVariantPrice !== null) {
            $max = max($maxProductPrice, $maxVariantPrice);
        } elseif ($maxProductPrice !== null) {
            $max = $maxProductPrice;
        } else {
            $max = $maxVariantPrice;
        }

        return [
            'min_price' => $min,
            'max_price' => $max,
        ];
    }

    public function filters(Request $request)
    {
        $data = [];
        $lang = $request->header('Accept-Language');
        $data['categories'] = Category::with('children')
            ->whereNull('parent_id')
            ->get();
        if ($lang === 'en') {
            $categoryIds = $data['categories']->pluck('id')
                ->merge(
                    $data['categories']->flatMap->children->pluck('id')
                );
            $translations = English::where('model_name', 'Category')
                ->whereIn('model_row', $categoryIds)
                ->get()
                ->keyBy('model_row');
            $data['categories']->each(function ($category) use ($translations) {
                if ($translations->has($category->id)) {
                    foreach ($translations[$category->id]->value as $key => $val) {
                        $category->$key = $val;
                    }
                }
                $category->children->each(function ($child) use ($translations) {
                    if ($translations->has($child->id)) {
                        foreach ($translations[$child->id]->value as $key => $val) {
                            $child->$key = $val;
                        }
                    }
                });
            });
        }
        $data['price'] = $this->priceRange();
        return response()->json([
            'success' => true,
            'message' => 'فیلتر های محصولات',
            'data'    => $data
        ], 200);
    }

    public function home(Request $request)
    {
        $lang = $request->header('Accept-Language');

        $data = [];

        $data['selected_categories'] = Category::where('show_in_home', 1)->get();
        $data['top_discounted_products'] = Product::topDiscounted();
        $data['banners'] = Banner::groupedByPosition();
        $data['sliders'] = Slider::orderBy('id')->get();
        $data['new_products'] = Product::latestProducts();
        $data['blogs'] = Article::latestArticles();

        if ($lang === 'en') {

            $data['selected_categories'] =
                English::applyTranslations(
                    $data['selected_categories'],
                    'Category'
                );

            $data['top_discounted_products'] =
                English::applyTranslations(
                    $data['top_discounted_products'],
                    'Product'
                );

            $data['new_products'] =
                English::applyTranslations(
                    $data['new_products'],
                    'Product'
                );

            $data['sliders'] =
                English::applyTranslations(
                    $data['sliders'],
                    'Slider'
                );

            $data['blogs'] =
                English::applyTranslations(
                    $data['blogs'],
                    'Article'
                );

            $data['banners'] = $data['banners']->map(function ($group) {
                // $group الان array از Banner هاست
                return array_map(function ($banner) {
                    $translation = English::where('model_name', 'Banner')
                        ->where('model_row', $banner['id'])
                        ->first();

                    if ($translation) {
                        foreach ($translation->value as $key => $val) {
                            $banner[$key] = $val;
                        }
                    }

                    return $banner;
                }, $group);
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'اطلاعات صفحه اصلی',
            'data'    => $data
        ], 200);
    }

    public function base(Request $request)
    {
        $lang = $request->header('Accept-Language');

        $data = [];

        // وضعیت لاگین
        $user = Auth::guard('sanctum')->user();
        $data['user'] = $user ?? null;

        /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */

        $settings = Setting::all();
        // اگر زبان انگلیسی بود ترجمه‌ها رو بگیر
        if ($lang === 'en') {

            $translations = English::where('model_name', 'Setting')
                ->whereIn('model_row', $settings->pluck('id'))
                ->get()
                ->keyBy('model_row');

            $settings->each(function ($setting) use ($translations) {

                if ($translations->has($setting->id)) {
                    $translated = $translations[$setting->id]->value['value'] ?? null;
                    $setting->value = $translated;
                }
            });
        }

        $data['settings'] = $settings;

        /*
    |--------------------------------------------------------------------------
    | Menus
    |--------------------------------------------------------------------------
    */
        $menus = Menu::with('children')
            ->whereNull('parent_id')
            ->get();
        if ($lang === 'en') {
            // گرفتن همه id های منو (پدر + فرزند)
            $menuIds = $menus->pluck('id')
                ->merge($menus->flatMap->children->pluck('id'));
            $menuTranslations =  English::where('model_name', 'Menu')
                ->whereIn('model_row', $menuIds)
                ->get()
                ->keyBy('model_row');

            // اعمال ترجمه روی پدر و فرزند
            $menus->each(function ($menu) use ($menuTranslations) {

                if ($menuTranslations->has($menu->id)) {
                    foreach ($menuTranslations[$menu->id]->value as $key => $val) {
                        $menu->$key = $val;
                    }
                }

                $menu->children->each(function ($child) use ($menuTranslations) {
                    if ($menuTranslations->has($child->id)) {
                        foreach ($menuTranslations[$child->id]->value as $key => $val) {
                            $child->$key = $val;
                        }
                    }
                });
            });
        }

        $data['menus'] = $menus;
        return response()->json([
            'success' => true,
            'message' => 'home data successfully',
            'data'    => $data
        ], 200);
    }
}
