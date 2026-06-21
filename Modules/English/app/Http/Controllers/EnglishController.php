<?php

namespace Modules\English\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ArticleCategories\Models\ArticleCategory;
use Modules\Articles\Models\Article;
use Modules\Banners\Models\Banner;
use Modules\Categories\Models\Category;
use Modules\English\Models\English;
use Modules\Menus\Models\Menu;
use Modules\Products\Models\Product;
use Modules\Settings\Models\Setting;
use Modules\Sliders\Models\Slider;

class EnglishController extends Controller
{
    // article category
    public function getArticleCategory($id)
    {
        $category = ArticleCategory::findOrFail($id);
        $en = English::forModel($category);
        if ($en) {
            $data = $en->value;
        } else {
            $data = $category;
        }
        return response()->json([
            'data' => $data,
            'message' => 'en data'
        ]);
    }
    public function setArticleCategory(Request $request, $id)
    {
        $category = ArticleCategory::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required',
            'meta_title' => 'required',
            'meta_description' => 'required',
            'description' => 'required'
        ]);
        $en = English::storeForModel($category, $validated);
        return response()->json([
            'data' => $en,
            'message' => 'en data'
        ]);
    }
    // article
    public function getArticle($id)
    {
        $article = Article::findOrFail($id);
        $en = English::forModel($article);
        if ($en) {
            $data = $en->value;
        } else {
            $data = $article;
        }
        return response()->json([
            'data' => $data,
            'message' => 'en data'
        ]);
    }
    public function setArticle(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required',
            'meta_title' => 'required',
            'meta_description' => 'required',
            'short_description' => 'required',
            'description' => 'required'
        ]);
        $en = English::storeForModel($article, $validated);
        return response()->json([
            'data' => $en,
            'message' => 'en data'
        ]);
    }

    // menu
    public function getMenu($id)
    {
        $menu = Menu::findOrFail($id);
        $en = English::forModel($menu);
        if ($en) {
            $data = $en->value;
        } else {
            $data = $menu;
        }
        return response()->json([
            'data' => $data,
            'message' => 'en data'
        ]);
    }
    public function setMenu(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required',
        ]);
        $en = English::storeForModel($menu, $validated);
        return response()->json([
            'data' => $en,
            'message' => 'en data'
        ]);
    }
    // slider
    public function getSlider($id)
    {
        $slider = Slider::findOrFail($id);
        $en = English::forModel($slider);
        if ($en) {
            $data = $en->value;
        } else {
            $data = $slider;
        }
        return response()->json([
            'data' => $data,
            'message' => 'en data'
        ]);
    }
    public function setSlider(Request $request, $id)
    {
        $slider = Slider::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'button_text' => 'nullable',
        ]);
        $en = English::storeForModel($slider, $validated);
        return response()->json([
            'data' => $en,
            'message' => 'en data'
        ]);
    }
    // banners
    public function getBanners($id)
    {
        $banner = Banner::findOrFail($id);
        $en = English::forModel($banner);
        if ($en) {
            $data = $en->value;
        } else {
            $data = $banner;
        }
        return response()->json([
            'data' => $data,
            'message' => 'en data'
        ]);
    }
    public function setBanners(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required',
            'image_desktop' => 'required',
            'image_mobile' => 'required',
            'ratio' => 'required',
        ]);
        if ($request->hasFile('image_desktop')) {
            $path = $request->file('image_desktop')->store('banners', 'public');
            $validated['image_desktop'] = $path;
        }
        if ($request->hasFile('image_mobile')) {
            $path = $request->file('image_mobile')->store('banners', 'public');
            $validated['image_mobile'] = $path;
        }
        $en = English::storeForModel($banner, $validated);
        return response()->json([
            'data' => $en,
            'message' => 'en data'
        ]);
    }

    // categories
    public function getCategories($id)
    {
        $category = Category::findOrFail($id);
        $en = English::forModel($category);
        if ($en) {
            $data = $en->value;
        } else {
            $data = $category;
        }
        return response()->json([
            'data' => $data,
            'message' => 'en data'
        ]);
    }
    public function setCategories(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required',
            'meta_description' => 'required',
            'meta_title' => 'required',
            'description' => 'required',
        ]);
        $en = English::storeForModel($category, $validated);
        return response()->json([
            'data' => $en,
            'message' => 'en data'
        ]);
    }

    // products
    public function getProducts($id)
    {
        $product = Product::findOrFail($id);
        $en = English::forModel($product);
        if ($en) {
            $data = $en->value;
        } else {
            $data = $product;
        }
        return response()->json([
            'data' => $data,
            'message' => 'en data'
        ]);
    }
    public function setProducts(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required',
            'meta_description' => 'required',
            'meta_title' => 'required',
            'description' => 'required',
            'price' => 'required',
            'video' => 'nullable',
        ]);
        if ($request->hasFile(key: 'video')) {
            $validated['video'] = $request->file('video')->store('products/videos', 'public');
        }
        $en = English::storeForModel($product, $validated);
        return response()->json([
            'data' => $en,
            'message' => 'en data'
        ]);
    }
    // products
    public function getSettingGroup($group)
    {
        // گرفتن تنظیمات اصلی
        $settings = Setting::where('group', $group)->get();
        // گرفتن ترجمه‌های مربوط به همین setting ها
        $translations = English::where('model_name', 'Setting')
            ->whereIn('model_row', $settings->pluck('id'))
            ->get()
            ->keyBy('model_row'); // کلید بر اساس id تنظیم

        // merge کردن ترجمه با تنظیمات اصلی
        $result = $settings->map(function ($setting) use ($translations) {

            if ($translations->has($setting->id)) {
                $translatedValue = $translations[$setting->id]->value['value'] ?? null;
                $setting->value = $translatedValue;
            }

            return $setting;
        });

        return response()->json($result);
    }
    public function setSettingGroup(Request $request, $group)
    {
        $data = $request->validate([
            'settings' => 'required|array',
            'settings.*.id'    => 'required|integer|exists:settings,id',
            'settings.*.key'   => 'required|string|max:255',
            'settings.*.value' => 'nullable',
            'settings.*.type'  => 'required|string|in:string,boolean,number,json,text,image',
        ]);

        $savedItems = [];

        foreach ($data['settings'] as $item) {

            $value = $item['value'];
            switch ($item['type']) {
                case 'boolean':
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    break;

                case 'number':
                    $value = is_numeric($value) ? $value + 0 : null;
                    break;

                case 'json':
                    $value = is_string($value) ? json_decode($value, true) : $value;
                    break;
            }

            $savedItems[] = English::updateOrCreate(
                [
                    'model_name' => 'Setting',
                    'model_row'  => $item['id'], // 👈 استفاده از id اصلی
                ],
                [
                    'value' => [
                        'value' => $value,
                        'type'  => $item['type'],
                        'group' => $group,
                    ]
                ]
            );
        }

        return response()->json([
            'data' => $savedItems,
            'message' => 'Group translations saved successfully'
        ]);
    }
}
