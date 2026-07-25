<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class DemoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Tote Bags', 'tote-bags', 'https://images.unsplash.com/photo-1594223274512-ad4803739b7c?auto=format&fit=crop&w=600&q=85'],
            ['Shoulder Bags', 'shoulder-bags', 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=600&q=85'],
            ['Crossbody Bags', 'crossbody-bags', 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=600&q=85'],
            ['Clutches', 'clutches', 'https://images.unsplash.com/photo-1566150905458-1bf1fc113f0d?auto=format&fit=crop&w=600&q=85'],
            ['Work Bags', 'work-bags', 'https://images.unsplash.com/photo-1559563458-527698bf5295?auto=format&fit=crop&w=600&q=85'],
            ['Mini Bags', 'mini-bags', 'https://images.unsplash.com/photo-1585488434455-1e7b6b53b9a5?auto=format&fit=crop&w=600&q=85'],
        ];

        $categoryIds = [];
        foreach ($categories as $sort => [$name, $slug, $image]) {
            $category = Category::updateOrCreate(['slug' => $slug], [
                'name' => $name, 'image' => $image, 'is_active' => true, 'sort_order' => $sort + 1,
            ]);
            $categoryIds[$slug] = $category->id;
        }

        $products = [
            ['EES-TOTE-001','tote-bags','The Élan Structured Tote','the-elan-structured-tote',7490,6490,18,'BESTSELLER',1,1,'https://images.unsplash.com/photo-1594223274512-ad4803739b7c?auto=format&fit=crop&w=900&q=88','A polished everyday tote crafted for modern women. Its structured silhouette, roomy interior, and understated gold-tone details move effortlessly from meetings to evenings.'],
            ['EES-SHO-002','shoulder-bags','Celeste Crescent Shoulder Bag','celeste-crescent-shoulder-bag',6290,null,12,null,1,1,'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=900&q=88','A softly sculpted shoulder bag with an elegant crescent profile, smooth finish, and comfortable strap for all-day styling.'],
            ['EES-CRO-003','crossbody-bags','Noir Avenue Crossbody','noir-avenue-crossbody',5490,4690,24,null,1,0,'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=900&q=88','Compact without compromise. The Noir Avenue keeps your daily essentials organised with an adjustable strap and refined hardware.'],
            ['EES-CLU-004','clutches','Luna Evening Clutch','luna-evening-clutch',4890,null,8,'LIMITED',1,0,'https://images.unsplash.com/photo-1566150905458-1bf1fc113f0d?auto=format&fit=crop&w=900&q=88','A luminous evening clutch made for celebrations, weddings, and special dinners, finished with a delicate detachable chain.'],
            ['EES-WRK-005','work-bags','The Executive Carryall','the-executive-carryall',8990,7990,15,'EDITOR’S PICK',1,0,'https://images.unsplash.com/photo-1559563458-527698bf5295?auto=format&fit=crop&w=900&q=88','Designed for ambitious days, with a laptop-friendly interior, protective base, secure zip closure, and timeless tailored lines.'],
            ['EES-MIN-006','mini-bags','Amélie Mini Top Handle','amelie-mini-top-handle',4290,null,20,null,0,1,'https://images.unsplash.com/photo-1585488434455-1e7b6b53b9a5?auto=format&fit=crop&w=900&q=88','Petite, playful, and beautifully proportioned. Carry it by the top handle or wear it crossbody with the detachable strap.'],
            ['EES-TOTE-007','tote-bags','Sienna Soft Leather Tote','sienna-soft-leather-tote',8290,7290,11,null,1,0,'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=900&q=88','Relaxed luxury for every day, with a supple feel, generous capacity, and minimalist silhouette that grows more beautiful with wear.'],
            ['EES-SHO-008','shoulder-bags','Monaco Chain Shoulder Bag','monaco-chain-shoulder-bag',6990,null,9,'HOT',1,0,'https://images.unsplash.com/photo-1591561954557-26941169b49e?auto=format&fit=crop&w=900&q=88','An elevated day-to-night shoulder bag featuring a gleaming chain strap, quilted texture, and a confident, feminine shape.'],
            ['EES-CRO-009','crossbody-bags','Rue Camera Bag','rue-camera-bag',4590,3990,26,null,0,1,'https://images.unsplash.com/photo-1564422167509-4f5c7ef5fc23?auto=format&fit=crop&w=900&q=88','A versatile camera bag with two zipped compartments and a wide adjustable strap—ideal for errands, travel, and weekends.'],
            ['EES-CLU-010','clutches','Aurora Pleated Pouch','aurora-pleated-pouch',3790,null,14,'NEW',0,1,'https://images.unsplash.com/photo-1585036156171-384164a8c675?auto=format&fit=crop&w=900&q=88','Soft pleats and a sculptural frame give this elegant pouch its signature look. Tuck it under your arm for instant polish.'],
            ['EES-WRK-011','work-bags','Cambridge Laptop Tote','cambridge-laptop-tote',7790,null,17,null,0,0,'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=900&q=88','A streamlined work tote with padded laptop space, organised pockets, and comfortable handles for commutes and business travel.'],
            ['EES-MIN-012','mini-bags','Rosé Petite Bucket Bag','rose-petite-bucket-bag',5190,4490,0,null,1,0,'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=900&q=88','A charming bucket silhouette in a soft rose tone, finished with an adjustable drawstring and refined crossbody strap.'],
        ];

        foreach ($products as $sort => $row) {
            [$sku,$category,$name,$slug,$price,$discount,$stock,$badge,$featured,$new,$image,$description] = $row;
            $product = Product::updateOrCreate(['sku' => $sku], [
                'category_id' => $categoryIds[$category], 'name' => $name, 'slug' => $slug,
                'description' => $description, 'price' => $price, 'discount_price' => $discount,
                'stock' => $stock, 'image' => $image, 'badge_text' => $badge,
                'is_featured' => (bool) $featured, 'is_new' => (bool) $new,
                'is_sold_out' => $stock === 0, 'is_preorder' => false, 'is_active' => true,
                'sort_order' => $sort + 1, 'meta_title' => $name.' | Premium Women’s Handbags',
                'meta_description' => $description,
            ]);
            ProductImage::updateOrCreate(['product_id' => $product->id, 'sort_order' => 0], [
                'image_path' => $image, 'alt_text' => $name, 'is_primary' => true,
            ]);
        }

        foreach ([
            ['Nusrat Jahan','The finishing feels genuinely premium and the packaging was beautiful. My tote has become my everyday favourite.'],
            ['Farzana Rahman','Elegant, spacious, and exactly like the photos. Delivery was quick and the entire ordering experience felt effortless.'],
            ['Sadia Karim','I carried my EEsome clutch to a wedding and received so many compliments. It looks far more expensive than it is.'],
        ] as $sort => [$name, $content]) {
            Testimonial::updateOrCreate(['name' => $name], [
                'content' => $content, 'rating' => 5, 'is_active' => true, 'sort_order' => $sort + 1,
            ]);
        }
    }
}
