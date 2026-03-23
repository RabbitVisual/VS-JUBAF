<?php

namespace Modules\Marketplace\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Modules\Marketplace\Models\Product;
use Modules\Marketplace\Models\ProductImage;
use Modules\Marketplace\Models\ProductOption;
use Modules\Marketplace\Models\ProductOptionValue;
use Modules\Marketplace\Models\ProductSku;
use Modules\Marketplace\Models\PickupLocation;
use Modules\Treasury\App\Models\Campaign;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['campaign', 'skus', 'images' => fn ($q) => $q->orderBy('sort_order')]);
        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->input('q') . '%');
        }
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }
        $products = $query->orderBy('sort_order')->orderBy('title')->paginate(12)->withQueryString();
        $lowStockThreshold = config('marketplace.low_stock_threshold', 5);

        return view('marketplace::admin.products.index', compact('products', 'lowStockThreshold'));
    }

    public function create()
    {
        $campaigns = Campaign::where('is_active', true)->orderBy('name')->get();
        $pickupLocations = PickupLocation::where('is_active', true)->orderBy('name')->get();

        return view('marketplace::admin.products.create', compact('campaigns', 'pickupLocations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:marketplace_products,slug',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url|max:500',
            'sample_url' => 'nullable|string|max:500',
            'video_url' => 'nullable|url|max:500',
            'specifications' => 'nullable|array',
            'specifications_json' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|in:alimentacao,vestuario,livros,eventos_oficinas',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'pickup_location_id' => 'nullable|exists:marketplace_pickup_locations,id',
            'delivery_type' => 'required|in:local_pickup,shipping,both',
            'weight_grams' => 'nullable|integer|min:0',
            'length_cm' => 'nullable|numeric|min:0',
            'width_cm' => 'nullable|numeric|min:0',
            'height_cm' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
            'video' => 'nullable|file|mimes:mp4|max:51200',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        if (! empty($validated['specifications_json'])) {
            $decoded = json_decode($validated['specifications_json'], true);
            $validated['specifications'] = is_array($decoded) ? $decoded : null;
        }
        unset($validated['images'], $validated['specifications_json'], $validated['video']);

        $product = Product::create($validated);
        $this->processImageUploads($product, $request->file('images', []));
        $this->processVideoUpload($product, $request->file('video'));

        return redirect()
            ->route('admin.marketplace.products.edit', $product)
            ->with('success', __('marketplace::messages.product') . ' criado. Agora adicione opções (Tamanho, Cor) e gere a grade de SKUs na etapa 5.')
            ->with('edit_step', 5);
    }

    public function edit(Product $product)
    {
        $product->load(['images' => fn ($q) => $q->orderBy('sort_order')], 'options.values', 'skus');
        $campaigns = Campaign::where('is_active', true)->orderBy('name')->get();
        $pickupLocations = PickupLocation::where('is_active', true)->orderBy('name')->get();

        return view('marketplace::admin.products.edit', compact('product', 'campaigns', 'pickupLocations'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:marketplace_products,slug,' . $product->id,
            'description' => 'nullable|string',
            'image_url' => 'nullable|url|max:500',
            'sample_url' => 'nullable|string|max:500',
            'video_url' => 'nullable|url|max:500',
            'specifications' => 'nullable|array',
            'specifications_json' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|in:alimentacao,vestuario,livros,eventos_oficinas',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'pickup_location_id' => 'nullable|exists:marketplace_pickup_locations,id',
            'delivery_type' => 'required|in:local_pickup,shipping,both',
            'weight_grams' => 'nullable|integer|min:0',
            'length_cm' => 'nullable|numeric|min:0',
            'width_cm' => 'nullable|numeric|min:0',
            'height_cm' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
            'video' => 'nullable|file|mimes:mp4|max:51200',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        if (array_key_exists('specifications_json', $validated)) {
            $decoded = ! empty($validated['specifications_json']) ? json_decode($validated['specifications_json'], true) : null;
            $validated['specifications'] = is_array($decoded) ? $decoded : null;
        }
        unset($validated['images'], $validated['specifications_json'], $validated['video']);

        $product->update($validated);
        $this->processImageUploads($product, $request->file('images', []));
        $this->processVideoUpload($product, $request->file('video'));

        $editStep = (int) $request->input('edit_step', 1);
        return redirect()->route('admin.marketplace.products.edit', $product)
            ->with('success', __('marketplace::messages.product') . ' atualizado.')
            ->with('edit_step', min(5, max(1, $editStep)));
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.marketplace.products.index')->with('success', __('marketplace::messages.product') . ' removido.');
    }

    public function reorderImages(Request $request, Product $product)
    {
        $request->validate(['order' => 'required|array', 'order.*' => 'integer|exists:marketplace_product_images,id']);
        foreach ($request->input('order') as $sortOrder => $id) {
            ProductImage::where('id', $id)->where('product_id', $product->id)->update(['sort_order' => $sortOrder]);
        }
        return response()->json(['ok' => true]);
    }

    public function destroyImage(Product $product, ProductImage $image)
    {
        if ((int) $image->product_id !== (int) $product->id) {
            abort(404);
        }
        Storage::disk('public')->delete($image->path);
        $image->delete();
        return redirect()->route('admin.marketplace.products.edit', $product)->with('success', 'Imagem removida.')->with('edit_step', 3);
    }

    public function storeOption(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'values_text' => 'nullable|string|max:1000',
            'values' => 'nullable|array',
            'values.*' => 'string|max:100',
        ]);
        $values = $validated['values'] ?? [];
        if (empty($values) && ! empty($validated['values_text'])) {
            $values = preg_split('/[\s,]+/', trim($validated['values_text']), -1, PREG_SPLIT_NO_EMPTY);
            $values = array_map('trim', $values);
        }
        if (empty($values)) {
            return redirect()->route('admin.marketplace.products.edit', $product)->withErrors(['values_text' => 'Informe ao menos um valor.']);
        }
        $sortOrder = $product->options()->max('sort_order') + 1;
        $option = $product->options()->create(['name' => $validated['name'], 'sort_order' => $sortOrder]);
        foreach (array_values($values) as $i => $value) {
            $option->values()->create(['value' => $value, 'sort_order' => $i]);
        }
        return redirect()->route('admin.marketplace.products.edit', $product)->with('success', __('marketplace::messages.options') . ' adicionada.')->with('edit_step', 5);
    }

    public function destroyOption(Product $product, ProductOption $option)
    {
        if ((int) $option->product_id !== (int) $product->id) {
            abort(404);
        }
        $option->delete();
        return redirect()->route('admin.marketplace.products.edit', $product)->with('success', __('marketplace::messages.options') . ' removida.')->with('edit_step', 5);
    }

    public function generateSkus(Request $request, Product $product)
    {
        $product->load('options.values');
        $options = $product->options;
        if ($options->isEmpty()) {
            return redirect()->route('admin.marketplace.products.edit', $product)->with('error', 'Adicione opções (ex.: Tamanho, Cor) antes de gerar a grade.');
        }
        $valueLists = $options->map(fn ($o) => $o->values->pluck('value')->toArray())->toArray();
        $combinations = $this->cartesian($valueLists);
        $optionNames = $options->pluck('name')->toArray();
        $slugBase = Str::slug($product->title);
        $created = 0;
        foreach ($combinations as $combo) {
            $attributes = array_combine($optionNames, $combo);
            $skuCode = $slugBase . '-' . implode('-', array_map(fn ($v) => Str::upper(Str::slug($v)), $combo));
            $skuCode = preg_replace('/[^a-z0-9\-]/i', '', $skuCode);
            if (strlen($skuCode) > 80) {
                $skuCode = substr($skuCode, 0, 77) . '-' . substr(md5(implode('', $combo)), 0, 3);
            }
            $exists = $product->skus()->where('sku_code', $skuCode)->exists();
            if (! $exists) {
                $product->skus()->create([
                    'sku_code' => $skuCode,
                    'attributes' => $attributes,
                    'stock' => 0,
                ]);
                $created++;
            }
        }
        return redirect()->route('admin.marketplace.products.edit', $product)->with('success', "Grade gerada. {$created} SKU(s) criado(s).")->with('edit_step', 5);
    }

    public function updateSku(Request $request, Product $product, ProductSku $sku)
    {
        if ((int) $sku->product_id !== (int) $product->id) {
            abort(404);
        }
        $validated = $request->validate([
            'price_override' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'barcode' => 'nullable|string|max:100',
        ]);
        $sku->update($validated);
        return redirect()->route('admin.marketplace.products.edit', $product)->with('success', 'SKU atualizado.')->with('edit_step', 5);
    }

    public function destroySku(Product $product, ProductSku $sku)
    {
        if ((int) $sku->product_id !== (int) $product->id) {
            abort(404);
        }
        $sku->delete();
        return redirect()->route('admin.marketplace.products.edit', $product)->with('success', 'SKU removido.')->with('edit_step', 5);
    }

    private function processImageUploads(Product $product, array $files): void
    {
        if (empty($files)) {
            return;
        }
        $dir = 'marketplace/products/' . $product->id;
        Storage::disk('public')->makeDirectory($dir);
        $maxOrder = $product->images()->max('sort_order') ?? -1;
        foreach ($files as $file) {
            $mime = $file->getMimeType();
            $isGif = str_starts_with($mime ?? '', 'image/gif');
            if ($isGif) {
                $path = $file->store($dir, 'public');
            } else {
                try {
                    $image = Image::read($file);
                    if ($image->width() > 1200) {
                        $image->scale(width: 1200);
                    }
                    $encoded = $image->toWebp(quality: 85);
                    $filename = Str::random(20) . '.webp';
                    $path = $dir . '/' . $filename;
                    Storage::disk('public')->put($path, (string) $encoded);
                } catch (\Throwable $e) {
                    $path = $file->store($dir, 'public');
                }
            }
            $product->images()->create(['path' => $path, 'sort_order' => ++$maxOrder]);
        }
    }

    private function processVideoUpload(Product $product, $file): void
    {
        if (! $file || ! $file->isValid()) {
            return;
        }
        $dir = 'marketplace/products/' . $product->id;
        Storage::disk('public')->makeDirectory($dir);
        if ($product->video_path) {
            Storage::disk('public')->delete($product->video_path);
        }
        $path = $file->storeAs($dir, 'video.mp4', 'public');
        $product->update(['video_path' => $path]);
    }

    /** @return array<int, array<int, string>> */
    private function cartesian(array $arrays): array
    {
        if (empty($arrays)) {
            return [[]];
        }
        $head = array_shift($arrays);
        $tail = $this->cartesian($arrays);
        $result = [];
        foreach ($head as $h) {
            foreach ($tail as $t) {
                $result[] = array_merge([$h], $t);
            }
        }
        return $result;
    }
}
