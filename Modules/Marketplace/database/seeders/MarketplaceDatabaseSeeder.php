<?php

namespace Modules\Marketplace\Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Marketplace\Models\PickupLocation;
use Modules\Marketplace\Models\Product;
use Modules\Marketplace\Models\ProductOption;
use Modules\Marketplace\Models\ProductOptionValue;
use Modules\Marketplace\Models\ProductSku;

class MarketplaceDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates one demo product per category (vestuário, alimentação, livros, eventos)
     * and enables the store on the homepage.
     */
    public function run(): void
    {
        if (PickupLocation::count() === 0) {
            PickupLocation::create([
                'name' => 'Cantina após o culto',
                'address' => 'Salão da igreja - Cantina',
                'instructions' => 'Retirar após o culto de domingo.',
                'availability' => ['sunday_after_service' => true],
                'is_active' => true,
            ]);
        }

        $campaign = null;
        if (class_exists(\Modules\Treasury\App\Models\Campaign::class)) {
            $campaign = \Modules\Treasury\App\Models\Campaign::where('is_active', true)->first();
        }
        $pickup = PickupLocation::where('is_active', true)->first();

        $productsData = [
            [
                'title' => 'Camisa Missões 2026',
                'slug' => 'camisa-missoes-2026',
                'description' => 'Camisa oficial da campanha de Missões Mundiais. Algodão, cores da igreja. Sua compra apoia missionários.',
                'category' => Product::CATEGORY_VESTUARIO,
                'price' => 49.90,
                'stock' => 0,
                'delivery_type' => Product::DELIVERY_BOTH,
                'weight_grams' => 200,
                'length_cm' => 30,
                'width_cm' => 20,
                'height_cm' => 2,
                'sort_order' => 0,
                'with_variations' => true,
                'options' => [
                    ['name' => 'Tamanho', 'values' => ['P', 'M', 'G', 'GG']],
                ],
            ],
            [
                'title' => 'Bolo de Cenoura da Cantina',
                'slug' => 'bolo-cenoura-cantina',
                'description' => 'Bolo de cenoura caseiro, feito pelas irmãs da cantina. Fatia generosa. Retirada após o culto.',
                'category' => Product::CATEGORY_ALIMENTACAO,
                'price' => 8.00,
                'stock' => 30,
                'delivery_type' => Product::DELIVERY_LOCAL_PICKUP,
                'weight_grams' => 150,
                'sort_order' => 1,
                'specifications' => [
                    'Ingredientes' => 'Farinha, cenoura, ovos, óleo, açúcar, chocolate.',
                    'Peso aproximado' => '150g por fatia',
                    'Conservação' => 'Ambiente fresco.',
                ],
            ],
            [
                'title' => 'Devocional Diário - 365 Dias',
                'slug' => 'devocional-diario-365',
                'description' => 'Livro de meditações diárias para o ano. Um versículo e uma reflexão por dia. Ideal para presente ou uso pessoal.',
                'category' => Product::CATEGORY_LIVROS,
                'price' => 35.00,
                'stock' => 25,
                'delivery_type' => Product::DELIVERY_BOTH,
                'weight_grams' => 400,
                'length_cm' => 21,
                'width_cm' => 14,
                'height_cm' => 2,
                'sort_order' => 2,
                'sample_url' => 'https://www.example.com/amostra-devocional.pdf',
                'specifications' => [
                    'Páginas' => 384,
                    'Formato' => '14x21 cm',
                    'Idioma' => 'Português',
                ],
            ],
            [
                'title' => 'Oficina de Louvor - Inscrição',
                'slug' => 'oficina-louvor-inscricao',
                'description' => 'Inscrição para a Oficina de Louvor. Um sábado de ensaios, técnicas de voz e instrumentos. Inclui material e coffee break.',
                'category' => Product::CATEGORY_EVENTOS_OFICINAS,
                'price' => 25.00,
                'stock' => 40,
                'delivery_type' => Product::DELIVERY_LOCAL_PICKUP,
                'sort_order' => 3,
                'specifications' => [
                    'Data' => 'A definir (sábado)',
                    'Local' => 'Salão da igreja',
                    'Inclui' => 'Material + coffee break',
                ],
            ],
        ];

        foreach ($productsData as $data) {
            $withVariations = $data['with_variations'] ?? false;
            $optionsData = $data['options'] ?? null;
            $specs = $data['specifications'] ?? null;
            unset($data['with_variations'], $data['options'], $data['specifications']);
            $slug = $data['slug'];
            $data['campaign_id'] = $campaign?->id;
            $data['pickup_location_id'] = $pickup?->id;
            $data['is_active'] = true;
            if (! empty($specs) && is_array($specs)) {
                $data['specifications'] = $specs;
            }
            $product = Product::updateOrCreate(
                ['slug' => $slug],
                $data
            );
            if ($withVariations && $optionsData) {
                $this->createVariationsForProduct($product, $optionsData);
            }
        }

        Settings::set('homepage_show_marketplace', true, 'boolean', 'homepage', 'Loja Missionária disponível na Home');
        Settings::set('homepage_marketplace_title', 'Loja Missionária', 'string', 'homepage', 'Título da Loja na Home');
    }

    private function createVariationsForProduct(Product $product, array $optionsData): void
    {
        foreach ($product->options as $opt) {
            $opt->values()->delete();
            $opt->delete();
        }
        $product->skus()->delete();

        $sortOrder = 0;
        $optionModels = [];
        foreach ($optionsData as $opt) {
            $option = $product->options()->create([
                'name' => $opt['name'],
                'sort_order' => $sortOrder++,
            ]);
            foreach ($opt['values'] as $i => $val) {
                $option->values()->create(['value' => $val, 'sort_order' => $i]);
            }
            $optionModels[] = $option->fresh(['values']);
        }

        $valueLists = array_map(fn ($o) => $o->values->pluck('value')->toArray(), $optionModels);
        $combinations = $this->cartesian($valueLists);
        $optionNames = array_map(fn ($o) => $o->name, $optionModels);
        $slugBase = Str::slug($product->title);
        foreach ($combinations as $combo) {
            $attributes = array_combine($optionNames, $combo);
            $skuCode = $slugBase . '-' . implode('-', array_map(fn ($v) => Str::upper(Str::slug($v)), $combo));
            $skuCode = preg_replace('/[^a-z0-9\-]/i', '', $skuCode);
            if (strlen($skuCode) > 80) {
                $skuCode = substr($skuCode, 0, 77) . '-' . substr(md5(implode('', $combo)), 0, 3);
            }
            if (! $product->skus()->where('sku_code', $skuCode)->exists()) {
                $product->skus()->create([
                    'sku_code' => $skuCode,
                    'attributes' => $attributes,
                    'price_override' => null,
                    'stock' => rand(5, 20),
                ]);
            }
        }
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
